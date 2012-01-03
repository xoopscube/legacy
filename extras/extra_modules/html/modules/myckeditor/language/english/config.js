/*
$Id: config.js ver0.04 2011/5/21 11:56:00 domifara Exp $
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.docType = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">';
	config.shiftEnterMode = CKEDITOR.ENTER_BR;
	config.enterMode = CKEDITOR.ENTER_BR,
	//config.plugins = 'about,basicstyles,blockquote,button,clipboard,colorbutton,contextmenu,elementspath,enterkey,entities,filebrowser,find,flash,font,format,horizontalrule,htmldataprocessor,image,indent,justify,keystrokes,link,list,maximize,newpage,pagebreak,pastefromword,pastetext,popup,preview,print,removeformat,resize,save,scayt,smiley,showblocks,sourcearea,stylescombo,table,tabletools,specialchar,tab,templates,toolbar,undo,wysiwygarea,wsc';

// toolbar custumize sample
//-------------
/* esample Full  or Basic toolbar display */

	config.toolbar = 'Full';

/* or */

//	config.toolbar = 'Basic';


/* esample xclfull toolbar display */
//	config.toolbar = 'mycustom';
//	config.removePlugins = 'save,newpage,print,tab,pagebreak,preview,scayt';

/* esample xclfull xclsimple display */
//	config.toolbar = 'simple';
//	config.removePlugins = 'save,forms,newpage,print,smiley,specialchar,tab,pagebreak,stylescombo,preview,scayt,find,undo,templates';
//-------------
	config.toolbar_mycustom =
		[
			['Source','-','Preview'],
			['Cut','Copy','Paste','PasteText','PasteFromWord'],
			['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
			['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
			'/',
			['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
			['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
			['JustifyLeft','JustifyCenter','JustifyRight'],
			'/',
			['Link','Unlink','Anchor'],
			['Image','Flash','Youtube','Vimeo','Iframe'],
			['Table','HorizontalRule','Smiley','SpecialChar'],
			['TextColor','BGColor'],
			['Maximize', 'ShowBlocks'],
			'/',
			['Styles','Format','Font','FontSize','-','Templates','-','About']
		];

	config.toolbar_simple =
		[
			['Source', '-','Undo','Redo'],
			['Find','Replace','-','SelectAll','RemoveFormat'],
			['Link', 'Unlink'],
			['Bold', 'Italic','Underline','Strike','-','Blockquote'],
			['SpecialChar'],['Maximize']
		];

	config.width = '500px';
	config.height = '400px';

	config.fontSize_sizes='80%/80%;90%/90%;100%/100%;110%/110%;120%/120%;130%/130%;140%/140%;150%/150%;160%/160%;170%/170%;180%/180%;190%/190%;200%/200%';
//	config.fontSize_sizes='xx-small/xx-small;x-small/x-small;small/small;medium/medium;large/large;x-large/x-large;xx-large/xx-large';
//	config.font_names='MSゴシック/MS Gothic, Osaka-Mono, monospace; MS Pゴシック/MS PGothic, Osaka, sans-serif; MS UI Gothic/MS UI Gothic, Meiryo, Meiryo UI, Osaka, sans-serif; MS P明朝/MS PMincho, Saimincho, serif; Arial/Arial, Helvetica, sans-serif;Comic Sans MS/Comic Sans MS, cursive;Courier New/Courier New, Courier, monospace;Georgia/Georgia, serif;Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif;Tahoma/Tahoma, Geneva, sans-serif;Times New Roman/Times New Roman, Times, serif;Trebuchet MS/Trebuchet MS, Helvetica, sans-serif;Verdana/Verdana, Geneva, sans-serif';
	config.coreStyles_underline = { element : 'ins' };
	config.coreStyles_strike = { element : 'del' };

//	config.protectedSource.push( /<\?[\s\S]*?\?>/g );// For php
//	config.protectedSource.push(  /<\{[\s\S]*?\}>/g ) ;	// For smarty

// myplugin youtube Youtube Vimeo  MediaEmbed
//	config.extraPlugins += (config.extraPlugins ? ',youtube' : 'youtube' );
//	config.extraPlugins += (config.extraPlugins ? ',vimeo' : 'vimeo' );

};

//CKEDITOR.plugins.addExternal('MediaEmbed', '../plugins/mediaembed/plugin.js');
//CKEDITOR.plugins.addExternal('vimeo', '../plugins/vimeo/');
//CKEDITOR.plugins.addExternal('youtube', '../plugins/youtube/');


CKEDITOR.dtd.del = CKEDITOR.dtd.strike;
CKEDITOR.dtd.ins = CKEDITOR.dtd.u;

