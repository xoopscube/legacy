/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2008 Frederico Caldeira Knabben
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * Sample custom configuration settings used by the xpwiki plugin. It simply
 * loads the plugin. All the rest is done by the plugin itself.
 */


// プラグイン
//FCKConfig.Plugins.Add('InternalEx');
FCKConfig.Plugins.Add('BlockquoteEx');
FCKConfig.Plugins.Add('FontFormatEx', 'en,ja');
FCKConfig.Plugins.Add('AlignEx');
FCKConfig.Plugins.Add('ListEx', 'en,ja');
FCKConfig.Plugins.Add('IndentEx');
FCKConfig.Plugins.Add('InsertText', 'en,ja');
FCKConfig.Plugins.Add('PukiWikiPlugin', 'en,ja');
FCKConfig.Plugins.Add('TableEx', 'en,ja');
FCKConfig.Plugins.Add('HRuleEx');
FCKConfig.Plugins.Add('SmileyEx');
FCKConfig.Plugins.Add('SpecialCharEx', 'en,ja');

// Add the xpwiki plugin.
FCKConfig.Plugins.Add( 'xpwiki', 'en,ja') ;

//	Document type
FCKConfig.DocType = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';

//	Resizeing
FCKConfig.DisableObjectResizing = true ;

//	Popup source
FCKConfig.SourcePopup = false;

//	Shortcut
FCKConfig.Keystrokes = [
	[ CTRL + 65 /*A*/, true ],
	[ CTRL + 67 /*C*/, true ],
	[ CTRL + 70 /*F*/, true ],
	[ CTRL + 83 /*S*/, true ],
	[ CTRL + 88 /*X*/, true ],
	[ CTRL + 86 /*V*/, 'Paste' ],
	[ CTRL + 90 /*Z*/, 'Undo' ],
	[ CTRL + 89 /*Y*/, 'Redo' ],
	[ CTRL + 76 /*L*/, 'Link' ],
	[ CTRL + 50 /*B*/, 'Bold' ],
	[ CTRL + 73 /*I*/, 'Italic' ],
	[ CTRL + 85 /*U*/, 'Underline' ],
	[ CTRL + ALT + 13 /*ENTER*/, 'FitWindow' ],
	[ CTRL + ALT + 83 /*S*/, 'Source' ]
] ;

//	Tollbar
FCKConfig.ToolbarSets["Default"] = [
	['Cut','Copy','Paste','PasteText','PasteWord'],
	['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	['InsertText','Attachment','Table','Rule','Smiley','SpecialChar','PukiWikiPlugin'],
	'/',
	['Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript'],
	['OrderedList','UnorderedList','DList','Blockquote','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight'],
	['Link','Unlink','Anchor'],
	'/',
	['FontFormat','FontSize','TextColor','BGColor'],
	['FitWindow','ShowBlocks','-','About']
] ;

//	ContextMenu
FCKConfig.ContextMenu = ['Generic','Link','Anchor'] ;

//	Format
FCKConfig.FontFormats	= 'p;div;pre;h2;h3;h4;h5;h6' ;

//	Font format
FCKConfig.FontSizes = '80%;120%;140%;160%;180%;200%;250%;300%;'
					+ '8px;9px;10px;11px;12px;14px;16px;18px;20px;24px;28px;32px;40px;48px;60px;'
					+ 'xx-small;x-small;small;medium;large;x-large;xx-large';
FCKConfig.CoreStyles['Size'].Styles['line-height'] = '130%';
FCKConfig.CoreStyles['Bold'].Element = 'strong';
FCKConfig.CoreStyles['Italic'].Element = 'em';

//	Link
FCKConfig.LinkDlgHideTarget		= true ;
FCKConfig.LinkDlgHideAdvanced	= true ;
FCKConfig.LinkBrowser = false ;
FCKConfig.LinkUpload = false ;
FCKConfig.EMailProtection = 'none';

//	Smiley
FCKConfig.SmileyColumns = 8 ;
FCKConfig.SmileyWindowWidth		= 320 ;
FCKConfig.SmileyWindowHeight	= 200 ;

// Non Protect
FCKConfig.ProtectedSource.RegexEntries = [];
