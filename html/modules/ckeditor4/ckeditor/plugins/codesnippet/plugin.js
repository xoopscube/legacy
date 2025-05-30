/**
 * @version 2.5.0 XOOPSCube XCL, by Nuno Luciano aka gigamaster
 * Theme.html must preload /common/primsjs
 *  
 * @license Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
 * @fileOverview Rich code snippets for CKEditor.
 */

'use strict';

( function() {
	CKEDITOR.plugins.add( 'codesnippet', {
		requires: 'widget,dialog',
		lang: 'en,fr,ja,pt,ru',
		icons: 'codesnippet',
		hidpi: true,

		isSupportedEnvironment: function() {
			return !CKEDITOR.env.ie || CKEDITOR.env.version > 8;
		},

		beforeInit: function( editor ) {
			editor._.codesnippet = {};

			/**
			 * Sets the custom syntax highlighter. See {@link CKEDITOR.plugins.codesnippet.highlighter}
			 * to learn how to register a custom highlighter.
			 *
			 * **Note**:
			 *
			 * * This method can only be called while initialising plugins (in one of
			 * the three callbacks).
			 * * This method is accessible through the `editor.plugins.codesnippet` namespace only.
			 *
			 * @since 4.4.0
			 * @member CKEDITOR.plugins.codesnippet
			 * @param {CKEDITOR.plugins.codesnippet.highlighter} highlighter
			 */
			this.setHighlighter = function( highlighter ) {
				editor._.codesnippet.highlighter = highlighter;

				var langs = editor._.codesnippet.langs =
					editor.config.codeSnippet_languages || highlighter.languages;

				// We might escape special regex chars below, but we expect that there
				// should be no crazy values used as lang keys.
				editor._.codesnippet.langsRegex = new RegExp( '(?:^|\\s)language-(' +
					CKEDITOR.tools.object.keys( langs ).join( '|' ) + ')(?:\\s|$)' );
			};

			editor.once( 'pluginsLoaded', function() {
				// Remove the method once it cannot be used, because it leaks the editor reference (#589).
				this.setHighlighter = null;
			}, this );
		},

		onLoad: function() {
			CKEDITOR.dialog.add( 'codeSnippet', this.path + 'dialogs/codesnippet.js' );
		},

		init: function( editor ) {
			editor.ui.addButton && editor.ui.addButton( 'CodeSnippet', {
				label: editor.lang.codesnippet.button,
				command: 'codeSnippet',
				toolbar: 'insert,10'
			} );
		},

		afterInit: function( editor ) {
			var path = this.path;
			registerWidget( editor );

			// At the very end, if no custom highlighter was set so far, set a default one.
			if ( !editor._.codesnippet.highlighter ) {
				var prismHighlighter = new CKEDITOR.plugins.codesnippet.highlighter( {
					languages: {
						javascript: 'JavaScript',
						php: 'PHP',
						html: 'HTML',
						css: 'CSS',
						java: 'Java',
						// add other languages as needed
					},
					// Theme preload PrismJS from common folder
					init: function( callback ) {
						callback();
					},

					highlighter: function( code, language, callback ) {
						// Use Prism to highlight code.
						// Fall back to a default language if needed.
						var langKey = ( window.Prism.languages[ language ] ) ? language : 'javascript';
						var highlighted = Prism.highlight( code, Prism.languages[ langKey ], langKey );
						callback( highlighted );
					}
				} );

				this.setHighlighter( prismHighlighter );
			}
		}
	} );

	/**
	 * Global helpers and classes of the Code Snippet plugin.
	 * @class
	 * @singleton
	 */
	CKEDITOR.plugins.codesnippet = {
		highlighter: Highlighter
	};

	/**
	 * A Code Snippet highlighter. It can be set as a default highlighter
	 *
	 * @since 4.4.0
	 * @class CKEDITOR.plugins.codesnippet.highlighter
	 * @extends CKEDITOR.plugins.codesnippet
	 * @param {Object} def Highlighter definition. See {@link #highlighter}, {@link #init} and {@link #languages}.
	 */
	function Highlighter( def ) {
		CKEDITOR.tools.extend( this, def );

		/**
		 * A queue of {@link #highlight} jobs to be
		 * done once the highlighter is {@link #ready}.
		 *
		 * @readonly
		 * @property {Array} [=[]]
		 */
		this.queue = [];

		// Async init – execute jobs when ready.
		if ( this.init ) {
			this.init( CKEDITOR.tools.bind( function() {
				// Execute pending jobs.
				var job;

				while ( ( job = this.queue.pop() ) )
					job.call( this );

				this.ready = true;
			}, this ) );
		} else {
			this.ready = true;
		}

	}

	/**
	 * Executes the {@link #highlighter}. If the highlighter is not ready, it defers the job ({@link #queue})
	 * and executes it when the highlighter is {@link #ready}.
	 *
	 * @param {String} code Code to be formatted.
	 * @param {String} lang Language to be used ({@link CKEDITOR.config#codeSnippet_languages}).
	 * @param {Function} callback Function which accepts highlighted String as an argument.
	 */
	Highlighter.prototype.highlight = function() {
		var arg = arguments;

		// Highlighter is ready – do it now.
		if ( this.ready )
			this.highlighter.apply( this, arg );
		// Queue the job. It will be done once ready.
		else {
			this.queue.push( function() {
				this.highlighter.apply( this, arg );
			} );
		}
	};

	// Encapsulates snippet widget registration code.
	// @param {CKEDITOR.editor} editor
	function registerWidget( editor ) {
		var codeClass = editor.config.codeSnippet_codeClass,
			newLineRegex = /\r?\n/g,
			textarea = new CKEDITOR.dom.element( 'textarea' ),
			lang = editor.lang.codesnippet;

		editor.widgets.add( 'codeSnippet', {
			allowedContent: 'pre; code(language-*)',
			// Actually we need both - pre and code, but ACF does not make it possible
			// to defire required content with "and" operator.
			requiredContent: 'pre',
			styleableElements: 'pre',
			template: '<pre><code class="' + codeClass + '"></code></pre>',
			dialog: 'codeSnippet',
			pathName: lang.pathName,
			mask: true,

			parts: {
				pre: 'pre',
				code: 'code'
			},

			highlight: function() {
				var that = this,
					widgetData = this.data,
					callback = function( formatted ) {
						// IE8 (not supported browser) have issue with new line chars, when using innerHTML.
						// It will simply strip it.
						that.parts.code.setHtml( editor.plugins.codesnippet.isSupportedEnvironment() ?
							formatted : formatted.replace( newLineRegex, '<br>' ) );
					};

				// Set plain code first, so even if custom handler will not call it the code will be there.
				callback( CKEDITOR.tools.htmlEncode( widgetData.code ) );

				// Call higlighter to apply its custom highlighting.
				editor._.codesnippet.highlighter.highlight( widgetData.code, widgetData.lang, function( formatted ) {
					editor.fire( 'lockSnapshot' );
					callback( formatted );
					editor.fire( 'unlockSnapshot' );
				} );
			},

			data: function() {
				var newData = this.data,
					oldData = this.oldData;

				if ( newData.code )
					this.parts.code.setHtml( CKEDITOR.tools.htmlEncode( newData.code ) );

				// Remove old .language-* class.
				if ( oldData && newData.lang != oldData.lang )
					this.parts.code.removeClass( 'language-' + oldData.lang );

				// Lang needs to be specified in order to apply formatting.
				if ( newData.lang ) {
					// Apply new .language-* class.
					this.parts.code.addClass( 'language-' + newData.lang );

					this.highlight();
				}

				// Save oldData.
				this.oldData = CKEDITOR.tools.copy( newData );
			},

			// Upcasts <pre><code [class="language-*"]>...</code></pre>
			upcast: function( el, data ) {
				if ( el.name != 'pre' )
					return;

				var childrenArray = getNonEmptyChildren( el ),
					code;

				if ( childrenArray.length != 1 || ( code = childrenArray[ 0 ] ).name != 'code' )
					return;

				// Upcast <code> with text only: https://dev.ckeditor.com/ticket/11926#comment:4
				if ( code.children.length != 1 || code.children[ 0 ].type != CKEDITOR.NODE_TEXT )
					return;

				// Read language-* from <code> class attribute.
				var matchResult = editor._.codesnippet.langsRegex.exec( code.attributes[ 'class' ] );

				if ( matchResult )
					data.lang = matchResult[ 1 ];

				// Use textarea to decode HTML entities (https://dev.ckeditor.com/ticket/11926).
				textarea.setHtml( code.getHtml() );
				data.code = textarea.getValue();

				code.addClass( codeClass );

				return el;
			},

			// Downcasts to <pre><code [class="language-*"]>...</code></pre>
			downcast: function( el ) {
				var code = el.getFirst( 'code' );

				// Remove pretty formatting from <code>...</code>.
				code.children.length = 0;

				// Remove config#codeSnippet_codeClass.
				code.removeClass( codeClass );

				// Set raw text inside <code>...</code>.
				code.add( new CKEDITOR.htmlParser.text( CKEDITOR.tools.htmlEncode( this.data.code ) ) );

				return el;
			}
		} );

		// Returns an **array** of child elements, with whitespace-only text nodes
		// filtered out.
		// @param {CKEDITOR.htmlParser.element} parentElement
		// @return Array - array of CKEDITOR.htmlParser.node
		var whitespaceOnlyRegex = /^[\s\n\r]*$/;

		function getNonEmptyChildren( parentElement ) {
			var ret = [],
				preChildrenList = parentElement.children,
				curNode;

			// Filter out empty text nodes.
			for ( var i = preChildrenList.length - 1; i >= 0; i-- ) {
				curNode = preChildrenList[ i ];

				if ( curNode.type != CKEDITOR.NODE_TEXT || !curNode.value.match( whitespaceOnlyRegex ) )
					ret.push( curNode );
			}

			return ret;
		}
	}
} )();

// custom class for '<code>' tag
CKEDITOR.config.codeSnippet_codeClass = '';
// custom theme
CKEDITOR.config.codeSnippet_theme = 'default';
