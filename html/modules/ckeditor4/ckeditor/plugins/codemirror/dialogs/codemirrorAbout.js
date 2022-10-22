/**
 * @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.dialog.add('codemirrorAboutDialog',
    function(editor) {
        var lang = editor.lang.codemirror,
            imagePath = CKEDITOR.getUrl(CKEDITOR.plugins.get('codemirror').path + 'dialogs/logo.png');

        return {
            title: lang.dlgTitle,
            minWidth: 390,
            minHeight: 210,
            contents: [
                {
                    id: 'tab1',
                    label: '',
                    title: '',
                    expand: true,
                    padding: 0,
                    elements: [
                        {
                            type: 'html',
                            html: '<style type="text/css">' +
                                '.cke_about_container' +
                                '{' +
                                'color:#000 !important;' +
                                'padding:10px 10px 0;' +
                                'margin-top:5px' +
                                '}' +
                                '.cke_about_container p' +
                                '{' +
                                'margin: 0 0 10px;' +
                                '}' +
                                '.cke_about_container .cke_about_logo' +
                                '{' +
                                'height:105px;' +
                                'background-color:#fff;' +
                                'background-image:url(' +
                                imagePath +
                                ');' +
                                'background-position:center; ' +
                                'background-repeat:no-repeat;' +
                                'margin-bottom:10px;' +
                                '}' +
                                '.cke_about_container a' +
                                '{' +
                                'cursor:pointer !important;' +
                                'color:#00B2CE !important;' +
                                'text-decoration:underline !important;' +
                                '}' +
                                '.cke_about_container > div,' +
                                '.cke_about_container > h5' +
                                '{' +
                                'text-align:center;' +
                                'margin:auto;' +
                                'padding:1em;' +
                                '}' +
                                '.cke_rtl .cke_about_container > p' +
                                '{' +
                                'text-align:left;' +
                                '}' +
                                '</style>' +
                                '<div class="cke_about_container">' +
                                '<div class="cke_about_logo"></div>' +
                                '<div>' +
                                (typeof (CodeMirror) == "undefined" ? "" : 'CodeMirror ' + CodeMirror.version) +
                                ' (CKEditor Plugin Version ' +
                                editor.plugins.codemirror.version +
                                ')<br>' +
                                '<a target="_blank" rel="noopener noreferrer" href="https://codemirror.net">https://codemirror.net</a> - ' +
                                '<a target="_blank" rel="noopener noreferrer" href="https://github.com/w8tcha/CKEditor-CodeMirror-Plugin">w8tcha CKEditor CodeMirror Plugin</a>' +
                                '<br>' +
                                '<a target="_blank" rel="noopener noreferrer" href="https://github.com/gigamaster/CKEditor-CodeMirror-Plugin">CKEditor CodeMirror Plugin for Smarty Template Engine</a>' +
                                '</div>' +
                                '<h5>' +
                                lang.moreInfoShortcuts +
                                '</h5>' +
                                '<p>' +
                                lang.moreInfoShortcuts1 +
                                '</p>' +
                                '<p>' +
                                lang.moreInfoShortcuts2 +
                                '</p>' +
                                '<p>' +
                                lang.moreInfoShortcuts3 +
                                '</p>' +
                                '<p>' +
                                lang.moreInfoShortcuts4 +
                                '</p>' +
                                '<p>' +
                                lang.moreInfoShortcuts5 +
                                '</p>' +
                                '<p>' +
                                lang.moreInfoShortcuts6 +
                                '</p>' +
                                '<p>' +
                                lang.moreInfoShortcuts7 +
                                '</p>' +
                                '<p>' +
                                lang.moreInfoShortcuts8 +
                                '</p>' +
                                '<p>' +
                                lang.moreInfoShortcuts9 +
                                '</p>' +
                                '<p>' +
                                lang.copyright +
                                '</p>' +
                                '</div>'
                        }
                    ]
                }
            ],
            buttons: [CKEDITOR.dialog.cancelButton]
        };
    });
