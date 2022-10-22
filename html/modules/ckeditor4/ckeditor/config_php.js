CKEDITOR.editorConfig = function( config ) {
    config.docType = '<!DOCTYPE html>';
    config.language = 'en';
    config.uiColor = '#000000';
    // config.disableAutoInline = true;
    config.startupMode= 'source';
    config.height = 320;
    config.allowedContent= true;

    // All content will be pasted as plain text.
    // config.forcePasteAsPlainText = true;
    // config.pasteFilter = 'plain-text';

    config.plugins = 'codemirror,clipboard,dialog';


    // The toolbar groups arrangement, optimized for a single toolbar row.
    config.toolbarGroups = [
        { name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
    ];

    //config.removeButtons = 'Cut,Copy,Paste,Undo,Redo,Anchor,Underline,Strike,Subscript,Superscript,about';
    config.removeButtons = 'Source,Anchor,Underline,Strike,Subscript,Superscript,about';

    config.codemirror = {
        autoCloseBrackets: true,
        autoCloseTags: true,
        autoFormatOnStart: true,
        autoFormatOnUncomment: true,
        continueComments: true,
        enableCodeFolding: true,
        enableCodeFormatting: true,
        enableSearchTools: true,
        highlightMatches: true,
        lineNumbers: true,
        lineWrapping: true,
        mode: 'text/x-php',
        matchBrackets: true,
        showAutoCompleteButton: true,
        indentUnit: 4,
        indentWithTabs: true,
        showCommentButton: true,
        showUncommentButton: true,
        showFormatButton: true,
        showSearchButton: true,
        theme: 'ayu-dark',
        useBeautifyOnStart: false
    }
}
