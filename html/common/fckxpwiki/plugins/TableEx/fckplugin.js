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


///////////////////////////////////////////////////////////
//	コマンド

//	テーブル プロパティ
FCKCommands.GetCommand('Table').Url = FCKPlugins.Items['TableEx'].Path + 'Table.html';
FCKCommands.GetCommand('Table').Width = 260;
FCKCommands.GetCommand('Table').Height = 160;

FCKCommands.GetCommand('Table').GetState = function() {
	// Disabled if not WYSIWYG.
	if ( FCK.EditMode != FCK_EDITMODE_WYSIWYG || ! FCK.EditorWindow )
		return FCK_TRISTATE_DISABLED ;

	var tags = new Array('H2', 'H3', 'H4', 'H5', 'H6', 'PRE', 'TABLE');
	for (i = 0; i < tags.length; i++) {
		if (FCKSelection.HasAncestorNode(tags[i])) {
			return FCK_TRISTATE_DISABLED;
		}
	}
	
	var oElement = FCKSelection.GetSelectedElement();
	if (oElement && oElement.tagName.Equals('IMG', 'HR', 'DIV', 'SPAN')) {
		return FCK_TRISTATE_DISABLED;
	}
	
	return FCK_TRISTATE_OFF;
}


//	セル プロパティ
FCKCommands.RegisterCommand('TableCellProp',
	new FCKDialogCommand('TableCellEx',  FCKLang.CellProperties,
						 FCKPlugins.Items['TableEx'].Path + 'TableCell.html', 380, 200
	)
);


//	列 プロパティ
FCKCommands.RegisterCommand('TableCol',
	new FCKDialogCommand('TableCol',  FCKLang.TableColDlgTitle,
						 FCKPlugins.Items['TableEx'].Path + 'TableCol.html', 380, 180
	)
);


//	セルの挿入

FCKCommands.GetCommand('TableInsertCellBefore').Execute = function() {
	TableInsertCell(true);
}

FCKCommands.GetCommand('TableInsertCellAfter').Execute = function() {
	TableInsertCell(false);
}

function TableInsertCell(insertBefore) {
	FCKUndo.SaveUndoStep();
	
	var oCell = FCKSelection.MoveToAncestorNode('TD') || FCKSelection.MoveToAncestorNode('TH');
	
	oCell = FCKTableHandler.InsertCell(oCell, insertBefore);
	
	oCell.className = 'style_td';
	
	FCKUndo.SaveUndoStep();
}

//	列削除
FCKCommands.GetCommand('TableDeleteColumns').Execute = function() {
	var oCell = FCKSelection.MoveToAncestorNode('TD') || FCKSelection.MoveToAncestorNode('TH');
	if (!oCell) {
		return;
	}
	
	FCKUndo.SaveUndoStep();
	
	var oTable = FCKTools.GetElementAscensor(oCell, 'TABLE');
	var aTableMap = FCKTableHandler._CreateTableMap(oTable);
	var nColIndex = FCKTableHandler._GetCellIndexSpan(aTableMap, oCell.parentNode.rowIndex, oCell);
	
	FCKTableHandler.DeleteColumns();
	
	var aColGroups = oTable.getElementsByTagName('COLGROUP');
	var aCols;
	if (!aColGroups.length) {
		return;
	}
	
	for (i = 0; i < aColGroups.length; i++) {
		aCols = aColGroups[i].getElementsByTagName('COL');
		aColGroups[i].removeChild(aCols[nColIndex]);
	}
	
	FCKUndo.SaveUndoStep();
}

//	セルを左右に分割
FCKCommands.RegisterCommand('TableSplitCellRightLeft', {
	Execute : function() {
		var cells = FCKTableHandler.GetSelectedCells();
		if ( cells.length != 1 )
			return ;
		
		FCKUndo.SaveUndoStep();
		
		FCKTableHandler.HorizontalSplitCell();
		
		var refBase = cells[0].parentNode.parentNode;
		var tag = cells[0].nodeName;
		var elems = refBase.getElementsByTagName(tag);
		var name = 'style_' + cells[0].nodeName.toLowerCase();
		for (var i=0; i<elems.length; i++) {
			if (! elems[i].className) {
				elems[i].className = name;
			}
		}
		
		FCKUndo.SaveUndoStep();
	},
	
	GetState : function() { return FCK_TRISTATE_OFF; }
})


//	セルを上下に分割
FCKCommands.RegisterCommand('TableSplitCellTopBottom', {
	Execute : function() {
		var cells = FCKTableHandler.GetSelectedCells();
		if ( cells.length != 1 )
			return ;
			
		FCKUndo.SaveUndoStep();
		
		FCKTableHandler.VerticalSplitCell();
		
		var refBase = cells[0].parentNode.parentNode;
		var tag = cells[0].nodeName;
		var elems = refBase.getElementsByTagName(tag);
		var name = 'style_' + cells[0].nodeName.toLowerCase();
		for (var i=0; i<elems.length; i++) {
			if (! elems[i].className) {
				elems[i].className = name;
			}
		}
		
		FCKUndo.SaveUndoStep();
	},
	
	GetState : function() { return FCK_TRISTATE_OFF; }
})

// FCKeditorのバグ対策 ([Firefox]セル結合でリンクを含むセルが消える)
FCKCommands.RegisterCommand('TableMergeGecko', {
	Execute : function() {
		var cells = FCKTableHandler.GetSelectedCells();
		for (var i=0; i<cells.length; i++) {
			var elems = cells[i].getElementsByTagName('a');
			for (var i2=0; i2<elems.length; i2++) {
				if (! elems[i2].className) {
					elems[i2].setAttribute('_moz_dirty', '');
				}
			}		
		}
		
		FCKUndo.SaveUndoStep();
		
		FCKTableHandler.MergeCells();
		
		FCKUndo.SaveUndoStep();
	},
	
	GetState : function() { return FCK_TRISTATE_OFF; }
})

// セル 見出し/通常 反転
FCKCommands.RegisterCommand('TableCellHeadToggle', {
	Execute : function() {
		var aCells = FCKTableHandler.GetSelectedCells();
		
		FCKUndo.SaveUndoStep();
		
		for (var i=0; i<aCells.length; i++) {
			var tag = (aCells[i].tagName.toLowerCase() == 'td')? 'TH' : 'TD';
			//	タグの置換
			oElement = FCK.EditorDocument.createElement(tag);
			oElement.className = (tag == 'TH') ? 'style_th' : 'style_td';
			oElement.innerHTML = aCells[i].innerHTML;
			oElement.colSpan = aCells[i].colSpan;
			oElement.rowSpan = aCells[i].rowSpan;
			oElement.setAttribute('style', aCells[i].getAttribute('style'));
			aCells[i] = aCells[i].parentNode.replaceChild(oElement, aCells[i]);
		}
		
		FCKUndo.SaveUndoStep();
	},
	
	GetState : function() { return FCK_TRISTATE_OFF; }
})

///////////////////////////////////////////////////////////
//	ツールバー

FCK.Events.AttachEvent('OnSelectionChange', function () { FCKToolbarItems.GetItem('Table').RefreshState() });



///////////////////////////////////////////////////////////
//	コンテキストメニュー

FCK.ContextMenu.RegisterListener( {
	AddItems : function(menu, tag, tagName) {
		var bIsTable = (tagName == 'TABLE');
		var bIsCell = (!bIsTable && FCKSelection.HasAncestorNode('TABLE'));
		
		if (bIsCell) {
			menu.AddSeparator();
			var oItem = menu.AddItem('Cell', FCKLang.CellCM);
			oItem.AddItem('TableCellHeadToggle', FCKLang.TableCellHeadToggle, 39);
			oItem.AddItem('TableInsertCellBefore', FCKLang.InsertCellBefore, 69);
			oItem.AddItem('TableInsertCellAfter', FCKLang.InsertCellAfter, 58);
			oItem.AddItem('TableDeleteCells', FCKLang.DeleteCells, 59);
			if ( FCKBrowserInfo.IsGecko ) {
				oItem.AddItem( 'TableMergeGecko'	, FCKLang.MergeCells, 60,
					FCKCommands.GetCommand( 'TableMergeCells' ).GetState() == FCK_TRISTATE_DISABLED ) ;
			}
			else {
				oItem.AddItem('TableMergeRight', FCKLang.MergeRight, 60);
				oItem.AddItem('TableMergeDown', FCKLang.MergeDown, 60);
			}
			oItem.AddItem('TableSplitCellRightLeft', FCKLang.HorizontalSplitCell, 61, FCKCommands.GetCommand( 'TableHorizontalSplitCell' ).GetState() == FCK_TRISTATE_DISABLED );
			oItem.AddItem('TableSplitCellTopBottom', FCKLang.VerticalSplitCell, 61, FCKCommands.GetCommand( 'TableVerticalSplitCell' ).GetState() == FCK_TRISTATE_DISABLED);
			oItem.AddSeparator();
			oItem.AddItem('TableCellProp', FCKLang.CellProperties, 57);

			menu.AddSeparator();
			oItem = menu.AddItem('Row', FCKLang.RowCM);
			oItem.AddItem('TableInsertRowBefore', FCKLang.InsertRowBefore, 70);
			oItem.AddItem('TableInsertRowAfter', FCKLang.InsertRowAfter, 62);
			oItem.AddItem('TableDeleteRows', FCKLang.DeleteRows, 63);
			
			menu.AddSeparator();
			oItem = menu.AddItem('Column', FCKLang.TableColMenu);
			oItem.AddItem('TableInsertColumnBefore', FCKLang.InsertColumnBefore, 71);
			oItem.AddItem('TableInsertColumnAfter', FCKLang.InsertColumnAfter, 64);
			oItem.AddItem('TableDeleteColumns', FCKLang.DeleteColumns, 65);
			oItem.AddSeparator();
			oItem.AddItem('TableCol', FCKLang.TableColDlgTitle);
		}

		if (bIsTable || bIsCell) {
			menu.AddSeparator();
			menu.AddItem('TableDelete', FCKLang.TableDelete);
			menu.AddItem('Table', FCKLang.TableProperties, 39);
		}
	}}
);
