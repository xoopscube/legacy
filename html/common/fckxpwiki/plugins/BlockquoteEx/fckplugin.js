//
//	guiedit - PukiWiki Plugin
//
//	License:
//	  GNU General Public License Version 2 or later (GPL)
//	  http://www.gnu.org/licenses/gpl.html
//
//	Copyright (C) 2006-2007 garand
//	PukiWiki : Copyright (C) 2001-2006 PukiWiki Developers Team
//	FCKeditor : Copyright (C) 2003-2007 Frederico Caldeira Knabben
//


FCKCommands.GetCommand('Blockquote').GetState = function() {
	/*
	var tags = new Array('H2', 'H3', 'H4', 'H5', 'H6', 'PRE', 'TABLE');
	for (i = 0; i < tags.length; i++) {
		if (FCKSelection.HasAncestorNode(tags[i])) {
			return FCK_TRISTATE_DISABLED;
		}
	}
	
	return FCK.GetNamedCommandState(this.Name);
	*/
	// Disabled if not WYSIWYG.
	if ( FCK.EditMode != FCK_EDITMODE_WYSIWYG || ! FCK.EditorWindow )
		return FCK_TRISTATE_DISABLED ;

	var tags = new Array('H2', 'H3', 'H4', 'H5', 'H6', 'PRE', 'TABLE');
	for (i = 0; i < tags.length; i++) {
		if (FCKSelection.HasAncestorNode(tags[i])) {
			return FCK_TRISTATE_DISABLED;
		}
	}

	var path = new FCKElementPath( FCKSelection.GetBoundaryParentElement( true ) ) ;
	var firstBlock = path.Block || path.BlockLimit ;

	if ( !firstBlock || firstBlock.nodeName.toLowerCase() == 'body' )
		return FCK_TRISTATE_OFF ;

	// See if the first block has a blockquote parent.
	for ( var i = 0 ; i < path.Elements.length ; i++ )
	{
		if ( path.Elements[i].nodeName.IEquals( 'blockquote' ) )
			return FCK_TRISTATE_ON ;
	}
	return FCK_TRISTATE_OFF ;
};


/*
FCKCommands.GetCommand('Blockquote').Execute = function() {
	FCK.ExecuteNamedCommand(this.Name);
};
*/
