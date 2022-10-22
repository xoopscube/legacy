/**
 * @license Copyright (c) 2003-2022, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
    // Define changes to default configuration here. For example:
    config.language = 'en';

    config.uiColor = '#d91192';

    config.removePlugins = 'a11ychecker,a11yheading,a11yhelp,a11yhelp,footnotes,forms,html5audio,iframe,inserthtmlfile,oembed,replaceTagNameByBsquochoai,templates,tweetabletext';

    config.extraPlugins = 'codemirror';

    config.contentsCss = 'contents.css';

    config.codemirror = {
        theme: 'ayu-mirage',
        lineWrapping: true,
        mode: 'htmlmixed',
    };

    /*config.codemirror = {

        // Theme to use (codemirror / themes)
        theme: 'ayu-mirage',

        // Show line numbers
        lineNumbers: true,

        // Use line wrapping
        lineWrapping: true,

        // Highlight matching braces
        matchBrackets: true,

        // Highlight matching tags
        matchTags: true,

        // Automatically close tags
        autoCloseTags: true,

        // Automatically close Brackets
        autoCloseBrackets: true,

        // Enable search tools, CTRL+F (Find), CTRL+SHIFT+F (Replace), CTRL+SHIFT+R (Replace All), CTRL+G (Find Next), CTRL+SHIFT+G (Find Previous)
        enableSearchTools: true,

        // Enable code folding (requires 'lineNumbers' to be set to 'true')
        enableCodeFolding: true,

        // Enable code formatting
        enableCodeFormatting: true,

        // Automatically format code should be done when the editor is loaded
        autoFormatOnStart: true,

        // Automatically format code which has just been uncommented
        autoFormatOnUncomment: true,

        // Highlight the currently active line
        highlightActiveLine: true,

        // Highlight all matches of current word/selection
        highlightMatches: true,

        // Define the language specific mode 'htmlmixed' for html including (css, xml, javascript)
        // 'application/x-httpd-php' for php mode including html
        // or 'text/javascript' for using java script only
        mode: 'htmlmixed',

        // Show the search Code button on the toolbar
        showSearchButton: true,

        // Show Trailing Spaces
        showTrailingSpace: true,

        // Show the format button on the toolbar
        showFormatButton: true,

        // Show the comment button on the toolbar
        showCommentButton: true,

        // Show the uncomment button on the toolbar
        showUncommentButton: true,

        // Show the showAutoCompleteButton button on the toolbar
        showAutoCompleteButton: true
    };*/

    config.codeSnippet_theme = 'ayu-mirage';

    // Register custom context for image widgets on the fly.
    config.balloonToolbars = {
        buttons: 'Link,Unlink,Image',
        widgets: 'image'
    };



    // config.protectedSource.push( /\<{[\s\S]*?\}>/g );

    // config.autoGrow_minHeight = 480;
    // config.autoGrow_maxHeight = 640;
    // config.autoGrow_bottomSpace = 50;
    // config.balloonToolbars = {
    //             buttons: 'Bold,Link,Unlink,Italic,Image',
    //             widgets: 'image'
    //         };

        // Register custom context for image widgets on the fly.
        // config.editor.balloonToolbars.create({
        //     buttons: 'Link,Unlink,Image',
        //     widgets: 'image'
        //   });

// The following example will show a balloon toolbar on any selection change. The toolbar is anchored to the
// last element in the selection, assuming that the editor variable is an instance of CKEDITOR.editor.
//     editor.on( 'instanceReady', function() {
//         var toolbar = new CKEDITOR.ui.balloonToolbar( editor );
//
//         toolbar.addItems( {
//             link: new CKEDITOR.ui.button( {
//                 command: 'link'
//             } ),
//             unlink: new CKEDITOR.ui.button( {
//                 command: 'unlink'
//             } )
//         } );
//
//         editor.on( 'selectionChange', function( evt ) {
//             var lastElement = evt.data.path.lastElement;
//
//             if ( lastElement ) {
//                 toolbar.attach( lastElement );
//             }
//         } );
//     } );

};
