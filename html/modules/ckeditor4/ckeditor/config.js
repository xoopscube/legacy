/**
 * @license Copyright (c) 2003-2022, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
    config.docType = '<!DOCTYPE html>';
	// Define changes to default configuration here. For example:
	config.language = 'en';

    // NOTE
    // This option should not be changed unless when outputting a non-HTML data format like BBCode
    // &nbsp; (non-breaking space)
    // &gt; = >
    // &lt; = <
    // &amp;= &
    config.basicEntities = false;

    // NOTE
    // The following options can be set in Control Panel > CKEditor4 > Preferences

        // config.uiColor = '#AADC6E';

        // Pressing Enter will create a new <P> element.
        // config.enterMode = CKEDITOR.ENTER_P;

        // Pressing Shift+Enter will create a new <br> element
        // config.shiftEnterMode = CKEDITOR.ENTER_BR;

    //config.editorplaceholder = 'Start typing here…';

    config.magicline_color = '#e43140';

    // 'la:Language:rtl' = right to left
    // config.language_list = ['ja:Japanese:rtl'];
    config.language_list = [ 'en:English', 'fr:Français', 'ja:Japanese', 'pt:Portuguese' ];

    config.codemirror = {
        autoCloseTags: true,
        autoFormatOnStart: true,
        continueComments: true,
        enableCodeFolding: true,
        enableCodeFormatting: true,
        enableSearchTools: true,
        highlightMatches: true,
        lineNumbers: true,
        lineWrapping: true,
        mode: 'htmlmixed',
        showCommentButton: true,
        showFormatButton: true,
        showSearchButton: true,
        showUncommentButton: true,
        theme: 'ayu-dark'
    }
};
