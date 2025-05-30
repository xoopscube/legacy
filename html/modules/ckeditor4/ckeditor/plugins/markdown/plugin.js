/*
 * Markdown plugin for XCL module CKEditor4
 * Convert HTML to markdown, the syntax highlighting of code blocks in the output HTML 
 * is handled by Prism.js (/common/prismjs/) and preloaded in theme.html
 * 
 * 2025 Nuno Luciano aka gigamaster
 * Licensed under the MIT license
 * Markdown Parser: marked.js (v15.x.x)
 * HTML to Markdown Parser: turndown.js (v7.x.x)
 */
'use_strict';
(function() {
    CKEDITOR.plugins.add('markdown', {
        icons: 'markdown',
        hidpi: true,
        init: function(editor) {
            if (editor.elementMode == CKEDITOR.ELEMENT_MODE_INLINE)
                return;

            var rootPath = this.path;
            var defaultConfig = {
                readOnly : ('disabled' == editor.element.getAttribute('disabled'))
            };
            editor.config.markdown = CKEDITOR.tools.extend(defaultConfig, editor.config.markdown || {}, true);

            var onResize = function() {
                if (!this.element || !this.element.$ || !this.element.$.parentNode) {
                    return;
                }
                var parentCKElement = this.element.getParent();
                if (!parentCKElement || !parentCKElement.$) {
                    return;
                }
                var parentDomElement = parentCKElement.$;
                var needsFocusHack = CKEDITOR.env.ie && CKEDITOR.env.version == 9;
                var wasActive = needsFocusHack && this.equals(CKEDITOR.document.getActive());

                this.element.hide();

                var newHeight = parentDomElement.clientHeight;
                var newWidth = parentDomElement.clientWidth;

                var textareaDomElement = this.element.$;
                textareaDomElement.style.height = newHeight + 'px';
                textareaDomElement.style.width = newWidth + 'px';
                textareaDomElement.style.boxSizing = 'border-box';

                this.element.show();

                if (wasActive) {
                    this.element.focus();
                }
            };

            editor.addMode('markdown', function(callback) {
                var contentsSpace = editor.ui.space('contents'),
                    textarea = contentsSpace.getDocument().createElement('textarea');

                textarea.setStyles(
                    CKEDITOR.tools.extend({
                            width: '100%',
                            height: '100%',
                            resize: 'none',
                            outline: 'none',
                            'text-align': 'left'
                        },
                        CKEDITOR.tools.cssVendorPrefix('tab-size', editor.config.sourceAreaTabSize || 4)));

                textarea.setAttribute('dir', 'ltr');
                textarea.addClass('cke_source').addClass('cke_reset').addClass('cke_enable_context_menu');

                editor.ui.space('contents').append(textarea);

                var editable = editor.editable(new sourceEditable(editor, textarea));
                var htmlData = editor.getData(1);

                function setMarkdownData(htmlToConvert) {
                    var turndownOptions = editor.config.markdown.turndown || {};
                    var turndownService = new TurndownService(turndownOptions);

                    // Add custom rule for fenced code blocks
                    turndownService.addRule('fencedCodeBlock', {
                      filter: function (node, options) {
                        return (
                          node.nodeName === 'PRE' &&
                          node.firstChild &&
                          node.firstChild.nodeName === 'CODE' &&
                          node.firstChild.getAttribute('class') &&
                          node.firstChild.getAttribute('class').indexOf('language-') === 0
                        );
                      },
                      replacement: function (content, node, options) {
                        var codeNode = node.firstChild; // The <code> element
                        var className = codeNode.getAttribute('class') || '';
                        var language = (className.match(/language-(\S+)/) || [null, ''])[1];
                        var codeContent = codeNode.textContent || '';

                        return (
                          '\n\n' + (options.fence || '```') + language + '\n' +
                          codeContent.replace(/\n$/, '') + // Remove a single trailing newline
                          '\n' + (options.fence || '```') + '\n\n'
                        );
                      }
                    });

                    var markdownResult = '';
                    try {
                        markdownResult = turndownService.turndown(htmlToConvert);
                    } catch (e) {
                        console.error("Turndown conversion error:", e);
                        markdownResult = "Error converting HTML to Markdown. Original HTML:\n" + htmlToConvert;
                    }
                    editable.setData(markdownResult);
                    CKEDITOR.tools.setTimeout( function() { if (editable && editable.element && editable.element.$) { onResize.call( editable ); } }, 0 );
                }

                if (typeof TurndownService === 'undefined') {
                    CKEDITOR.scriptLoader.load(rootPath + 'js/turndown-v7.min.js', function() {
                        setMarkdownData(htmlData);
                    });
                } else {
                    setMarkdownData(htmlData);
                }

                if (typeof marked === 'undefined' || typeof marked.parse === 'undefined') {
                    CKEDITOR.scriptLoader.load(rootPath + 'js/marked-v15.js');
                }

                editable._editorResizeHandler = editable.attachListener(editor, 'resize', onResize, editable);
                editable._windowResizeHandler = editable.attachListener(CKEDITOR.document.getWindow(), 'resize', onResize, editable);

                editor.fire('ariaWidget', this);

                if(typeof editor.commands.maximize !== 'undefined'){
                    editor.commands.maximize.modes.markdown = 1;
                }
                callback();
            });

            editor.addCommand('markdown', CKEDITOR.plugins.markdown.commands.markdown);

            if (editor.ui.addButton) {
                editor.ui.addButton('Markdown', {
                    label: 'Markdown',
                    command: 'markdown'
                });
            }
            CKEDITOR.document.appendStyleText('.cke_button__markdown_label {display: inline;}');

            editor.on('mode', function() {
                var isMarkdownMode = (editor.mode == 'markdown');
                editor.getCommand('markdown').setState(isMarkdownMode ? CKEDITOR.TRISTATE_ON : CKEDITOR.TRISTATE_OFF);
                if (isMarkdownMode && editor.editable()) {
                    var currentEditable = editor.editable();
                    if (currentEditable instanceof sourceEditable) {
                         CKEDITOR.tools.setTimeout( function() { if (currentEditable && currentEditable.element && currentEditable.element.$) { onResize.call( currentEditable ); } }, 0 );
                    }
                }
            });
        }
    });

    var sourceEditable = CKEDITOR.tools.createClass({
        base: CKEDITOR.editable,
        proto: {
            _editorResizeHandler: null,
            _windowResizeHandler: null,

            setData: function(data) {
                this.setValue(data);
                this.status = 'ready';
                this.editor.fire('dataReady');
            },

            getData: function() {
                return this.getValue();
            },

            insertHtml: function() {},
            insertElement: function() {},
            insertText: function() {},

            setReadOnly: function(isReadOnly) {
                this[(isReadOnly ? 'set' : 'remove') + 'Attribute']('readOnly', 'readonly');
            },

            detach: function() {
                var editor = this.editor;

                if (this._windowResizeHandler) {
                    this._windowResizeHandler.removeListener();
                    this._windowResizeHandler = null;
                }
                if (this._editorResizeHandler) {
                    this._editorResizeHandler.removeListener();
                    this._editorResizeHandler = null;
                }

                var markdownSource = this.getValue();
                if (typeof marked !== 'undefined' && typeof marked.parse !== 'undefined') {
                    editor.setData(marked.parse(markdownSource));
                } else {
                    editor.setData(markdownSource);
                    CKEDITOR.error( 'markdown-marked-not-loaded' );
                }
                sourceEditable.baseProto.detach.call(this);
                this.clearCustomData();
                this.remove();
            }
        }
    });
})();
CKEDITOR.plugins.markdown = {
    commands: {
        markdown: {
            modes: {
                wysiwyg: 1,
                markdown: 1
            },
            editorFocus: false,
            readOnly: 1,
            exec: function(editor) {
                if (editor.mode == 'wysiwyg')
                    editor.fire('saveSnapshot');
                editor.getCommand('markdown').setState(CKEDITOR.TRISTATE_DISABLED);
                editor.setMode(editor.mode == 'markdown' ? 'wysiwyg' : 'markdown');
            },
            canUndo: false
        }
    }
};
