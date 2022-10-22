(function($){
elFinder.prototype.commands.perm = function() {
	this.updateOnSelect = false;
	var self = this;
	var fm  = this.fm,
	spclass = 'elfinder-info-spinner',
	level = {
		0 : 'owner',
		1 : 'group',
		2 : 'guest'
	},
	msg = {
		read     : fm.i18n('read'),
		write    : fm.i18n('write'),
		unlock   : fm.i18n('unlock'),
		hidden   : fm.i18n('hidden'),
		newitem  : fm.i18n('newitem'),
		perm     : fm.i18n('perm'),
		perms    : fm.i18n('perms'),
		kind     : fm.i18n('kind'),
		files    : fm.i18n('files')
	};

	this.tpl = {
		main       : '<div class="ui-helper-clearfix elfinder-info-title elfinder-perm-dialog"><span class="elfinder-cwd-icon {class} ui-corner-all"></span>{title}</div>'
					+'{filter}{dataTable}{targetGroups}',
		filter     : '<div>'+fm.i18n('mimeserach')+': <input class="elfinder-tabstop" id="{id}-filter" type="textbox" name="filter" size="30" value="{value}"></div>',
		itemTitle  : '<strong>{name}</strong><span id="elfinder-info-kind">{kind}</span> ('+fm.i18n('owner')+':<span id="{id}-owner-name">{owner}{uidinput}</span>)',
		groupTitle : '<strong>{items}: {num}</strong>',
		dataTable  : '<table id="{id}-table-{type}"><tr><td>{0}</td><td>{1}</td><td>{2}</td></tr></table>'
					+'<div class="">'+msg.perm+': <input class="elfinder-tabstop elfinder-focus" id="{id}-{type}" type="text" size="4" maxlength="3" value="{value}"></div>',
		fieldset   : '<fieldset id="{id}-fieldset-{level}"><legend>{f_title}</legend>'
					+'<input type="checkbox" value="4" class="elfinder-tabstop" id="{id}-read-{level}-{type}"{checked-r}> <label for="{id}-read-{level}-{type}">'+msg.read+'</label><br>'
					+'<input type="checkbox" value="6" class="elfinder-tabstop" id="{id}-write-{level}-{type}"{checked-w}{disabled-w}> <label for="{id}-write-{level}-{type}">'+msg.write+'</label><br>'
					+'<input type="checkbox" value="5" class="elfinder-tabstop" id="{id}-unlock-{level}-{type}"{checked-u}> <label for="{id}-unlock-{level}-{type}">'+msg.unlock+'</label><br>'
					+'<input type="checkbox" value="8" class="elfinder-tabstop" id="{id}-hidden-{level}-{type}"{checked-h}{disabled-h}> <label for="{id}-hidden-{level}-{type}">'+msg.hidden+'</label></fieldset>',
		tab        : '<div id="{id}-tab"><ul><li><a href="#{id}-tab-perm">'+msg.perms+'</a></li><li><a href="#{id}-tab-umask">'+msg.newitem+'</a></li></ul>'
					+'<div id="{id}-tab-perm">{permTable}</div><div id="{id}-tab-umask">{umaskTable}</div></div>',
		groups     : '<fieldset id="{id}-fieldset-groups"><legend>'+fm.i18n('targetgroups')+'</legend><div id="{id}-groups"><span class="'+spclass+'"></span></div></fieldset>',
		groupCheck : '<input type="checkbox" class="elfinder-tabstop" id="{id}-group-{gid}" name="gids" value="{gid}"{checked} /><label for="{id}-group-{gid}">{gname}</label>',
		uidInput   : ' uid: <input type="text" class="elfinder-tabstop" id="{id}-uid" class="perm-uid" value="{uid}">'
	};

	this.shortcuts = [{
		pattern     : 'ctrl+p'
	}];

	this.getstate = function(sel) {
		var fm = this.fm;
		sel = sel || fm.selected();
		if (sel.length == 0) {
			sel = [ fm.cwd().hash ];
		}
		return !this._disabled && self.checkstate(this.files(sel)) ? 0 : -1;
	};
	
	this.checkstate = function(sel) {
		var cnt = sel.length;
		if (!cnt) return false;
		var loccheck = (cnt && sel[0]._localalias);
		var chk = $.map(sel, function(f) {
			return (f.isowner && (cnt == 1 || f.mime != 'directory') && !((f._localalias) ^ loccheck)) ? f : null;
		}).length;
		return (cnt == chk)? true : false;
	};

	this.exec = function(hashes) {
		var files   = this.files(hashes);
		if (! files.length) {
			hashes = [ this.fm.cwd().hash ];
			files   = this.files(hashes);
		}
		var fm  = this.fm,
		dfrd    = $.Deferred().always(function() {
			fm.enable();
		}),
		tpl     = this.tpl,
		hashes  = this.hashes(hashes),
		phashes = $.map(hashes, function(h) {
			var f = fm.file(h);
			return f.thash && f.phash? f.phash : '';
		}),
		cnt     = files.length,
		file    = files[0],
		id = fm.namespace + '-perm-' + file.hash,
		view    = tpl.main,
		checked = ' checked="checked"',
		buttons = function() {
			var buttons = {};
			buttons[fm.i18n('btnCancel')] = function() { dialog.elfinderdialog('close'); };
			buttons[fm.i18n('btnApply')] = save;
			return buttons;
		},
		save = function() {
			var perm = $('#'+id+'-perm').val();
			var umask = $('#'+id+'-umask').val();
			var uid = $('#'+id+'-uid').val();
			var gids = [];
			var filter = $('#'+id+'-filter').val();
			$('#'+id+'-fieldset-groups input[name=gids]:checked').map(function() {
				 gids.push($(this).val());
			});
			
			dialog.elfinderdialog('close');

			if (umask) {
				umask = (0xfff - parseInt(umask, 16)).toString(16);
			} else {
				umask = '';
			}
			
			if (fm.customData && !fm.customData.admin) {
				uid = '';
			}
			
			fm.request({
				data : {
					cmd    : 'perm',
					targets: hashes,
					phash  : phashes,
					perm   : perm,
					umask  : umask,
					gids   : gids,
					filter : filter,
					uid    : uid
				},
				notify : {type : 'perm', cnt : cnt}
			})
			.fail(function(error) {
				dfrd.reject(error);
			})
			.done(function(data) {
				dfrd.resolve(data);
			});
		},
		setperm = function(type) {
			var perm = '';
			var _perm;
			for (var i = 0; i < 3; i++){
				_perm = 0;
				if ($("#"+id+"-read-"+level[i]+'-'+type).is(':checked')) {
					_perm = (_perm | 4);
				}
				if ($("#"+id+"-write-"+level[i]+'-'+type).is(':checked')) {
					_perm = (_perm | 2);
				}
				if ($("#"+id+"-unlock-"+level[i]+'-'+type).is(':checked')) {
					_perm = (_perm | 1);
				}
				if ($("#"+id+"-hidden-"+level[i]+'-'+type).is(':checked')) {
					_perm = (_perm | 8);
				}
				perm += _perm.toString(16);
			}
			$('#'+id+'-'+type).val(perm);
		},
		setcheck = function(perm, type) {
			var _perm;
			for (var i = 0; i < 3; i++){
				_perm = parseInt(perm.slice(i, i+1), 16);
				$("#"+id+"-read-"+level[i]+'-'+type).prop("checked", false);
				$("#"+id+"-write-"+level[i]+'-'+type).prop("checked", false);
				$("#"+id+"-unlock-"+level[i]+'-'+type).prop("checked", false);
				$("#"+id+"-hidden-"+level[i]+'-'+type).prop("checked", false);
				if ((_perm & 4) == 4) {
					$("#"+id+"-read-"+level[i]+'-'+type).prop("checked", true);
				}
				if ((_perm & 2) == 2) {
					$("#"+id+"-write-"+level[i]+'-'+type).prop("checked", true);
				}
				if ((_perm & 1) == 1) {
					$("#"+id+"-unlock-"+level[i]+'-'+type).prop("checked", true);
				}
				if (i && (_perm & 8) == 8) {
					$("#"+id+"-hidden-"+level[i]+'-'+type).prop("checked", true);
				}
			}
			setperm(type);
		},
		makeperm = function(files, type) {
			var perm = '777', ret = '', chk, _chk, _perm;
			var len = files.length;
			for (var i2 = 0; i2 < len; i2++) {
				if (type == 'umask') {
					chk = files[i2].umask;
				} else {
					chk = files[i2].perm;
				}
				ret = '';
				for (var i = 0; i < 3; i++){
					_chk = parseInt(chk.slice(i, i+1), 16);
					if (type == 'umask') {
						_chk = 0xf - _chk;
					}
					_perm = parseInt(perm.slice(i, i+1), 16);
					if ((_chk & 4) != 4 && (_perm & 4) == 4) {
						_perm -= 4;
					}
					if ((_chk & 2) != 2 && (_perm & 2) == 2) {
						_perm -= 2;
					}
					if ((_chk & 1) != 1 && (_perm & 1) == 1) {
						_perm -= 1;
					}
					if ((_chk & 8) == 8 && (_perm & 8) != 8) {
						_perm += 8;
					}
					ret += _perm.toString(16);
				}
				perm = ret;
			}
			return perm;
		},
		makeDataTable = function(perm, type) {
			var _perm, fieldset;
			var value = '';
			var dataTable = tpl.dataTable;
			for (var i = 0; i < 3; i++){
				_perm = parseInt(perm.slice(i, i+1), 16);
				value += _perm.toString(16);
				fieldset = tpl.fieldset.replace('{f_title}', fm.i18n(level[i])).replace(/\{level\}/g, level[i]);
				dataTable = dataTable.replace('{'+i+'}', fieldset)
				                     .replace('{checked-r}', ((_perm & 4) == 4)? checked : '')
				                     .replace('{checked-w}', ((_perm & 2) == 2 && ! file._localalias)? checked : '')
				                     .replace('{checked-u}', ((_perm & 1) == 1)? checked : '')
				                     .replace('{checked-h}', ((_perm & 8) == 8)? checked : '')
				                     .replace('{disabled-w}', (file._localalias? ' disabled' : ''))
				                     .replace('{disabled-h}', ((i == 0)? ' disabled' : ''));
			}
			dataTable = dataTable.replace('{value}', value).replace(/{type}/g, type).replace('{valueCaption}', msg[type]);
			return dataTable;
		},
		replSpinner = function(msg, id) {
			if (id) {
				$('#'+id).html(msg);
			} else {
				dialog.find('.'+spclass).parent().html(msg);
			}
		},
		opts    = {
			title : this.title,
			width : 'auto',
			buttons : buttons(),
			close : function() { $(this).elfinderdialog('destroy'); }
		},
		dialog = fm.getUI().find('#'+id),
		tmb = '', title, dataTable, targetGroups, filter, uidInput;

		if (dialog.length) {
			dialog.elfinderdialog('toTop');
			return $.Deferred().resolve();
		}
		
		if (fm.customData && fm.customData.admin) {
			uidInput = tpl.uidInput.replace('{uid}', file.uid);
		} else {
			uidInput = '';
		}

		view  = view.replace('{class}', cnt > 1 ? 'elfinder-cwd-icon-group' : fm.mime2class(file.mime));
		if (cnt > 1) {
			title = tpl.groupTitle.replace('{items}', fm.i18n('items')).replace('{num}', cnt);
		} else {
			title = tpl.itemTitle.replace('{name}', file.name).replace('{kind}', fm.mime2kind(file)).replace('{owner}', file.owner).replace('{uidinput}', uidInput);
			if (file.tmb) {
				tmb = fm.option('tmbUrl')+file.tmb;
			}
		}

		if (file.mime == 'directory') {
			dataTable = tpl.tab.replace('{permTable}', makeDataTable(makeperm(files), 'perm')).replace('{umaskTable}', makeDataTable(makeperm(files, 'umask'), 'umask'));
			filter = tpl.filter.replace('{value}', file.filter);
		} else {
			dataTable = makeDataTable(makeperm(files), 'perm');
			filter = '';
		}
		targetGroups = tpl.groups;

		view = view.replace('{title}', title).replace('{filter}', filter).replace('{dataTable}', dataTable).replace('{targetGroups}', targetGroups).replace(/{id}/g, id);

		buttons[fm.i18n('btnApply')] = save;
		buttons[fm.i18n('btnCancel')] = function() { dialog.elfinderdialog('close'); };

		dialog = fm.dialog(view, opts);
		dialog.attr('id', id);

		// load thumbnail
		if (tmb) {
			$('<img/>')
				.on('load', function() { dialog.find('.elfinder-cwd-icon').css('background', 'url("'+tmb+'") center center no-repeat'); })
				.attr('src', tmb);
		}

		$('#' + id + '-table-perm :checkbox').on('click', function(){setperm('perm');});
		$('#' + id + '-perm').on('keydown', function(e) {
			var c = e.keyCode;
			if (c == $.ui.keyCode.ENTER) {
				e.stopPropagation();
				save();
				return;
			}
		}).on('focus', function(e){
			$(this).select();
		}).on('keyup', function(e) {
			if ($(this).val().length === 3) {
				$(this).select();
				setcheck($(this).val(), 'perm');
			}
		});
		$('#' + id + '-filter,#' + id + '-uid').keydown(function(e) {
			e.keyCode !== $.ui.keyCode.TAB && e.stopPropagation();
		});
		if (file.mime == 'directory') {
			$('#' + id + '-tab').tabs();
			$('#' + id + '-table-umask :checkbox').on('click', function(){setperm('umask');});
			$('#' + id + '-umask').on('keydown', function(e) {
				var c = e.keyCode;
				if (c == $.ui.keyCode.ENTER) {
					e.stopPropagation();
					save();
					return;
				}
			}).on('focus', function(e){
				$(this).select();
			}).on('keyup', function(e) {
				if ($(this).val().length == 3) {
					$(this).select();
					setcheck($(this).val(), 'umask');
				}
			});
		}
		
		fm.request({
			data : {cmd : 'perm', targets : hashes, perm : 'getgroups'},
			preventDefault : true
		})
		.fail(function() {
			replSpinner(msg.unknown, id+'-groups');
		})
		.done(function(data) {
			var groups = data.groups;
			var html = '';
			//fm.log(groups);
			if (groups) {
				for (var gid in groups) {
					var gname = groups[gid].name;
					var checked = groups[gid].on? ' checked="checked"' : '';
					html += tpl.groupCheck.replace(/\{gid\}/g, gid).replace('{gname}', gname).replace('{checked}', checked).replace(/\{id\}/g, id);
				};
			}
			replSpinner(html ? html : msg.unknown, id+'-groups');
		});

		return dfrd;
	};
};
}(jQuery));