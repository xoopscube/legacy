elFinder.prototype.commands.perm = function() {
	this.updateOnSelect = false;

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
		main       : '<div class="ui-helper-clearfix elfinder-info-title"><span class="elfinder-cwd-icon {class} ui-corner-all"/>{title}</div>'
					+'{filter}{dataTable}{targetGroups}',
		filter     : '<div>'+fm.i18n('mimeserach')+': <input id="{id}-filter" type="textbox" name="filter" size="30" value="{value}"></div>',
		itemTitle  : '<strong>{name}</strong><span id="elfinder-info-kind">{kind}</span> ('+fm.i18n('owner')+':<span id="{id}-owner-name"><span class="'+spclass+'"/></span>)',
		dataTable  : '<table id="{id}-table-{type}"><tr><td>{0}</td><td>{1}</td><td>{2}</td></tr></table>'
					+'<div class="">'+msg.perm+': <input id="{id}-{type}" type="text" size="4" maxlength="3" value="{value}"></div>',
		fieldset   : '<fieldset id="{id}-fieldset-{level}"><legend>{f_title}</legend>'
					+'<input type="checkbox" value="4" id="{id}-read-{level}-{type}"{checked-r}> <label for="{id}-read-{level}-{type}">'+msg.read+'</label><br>'
					+'<input type="checkbox" value="6" id="{id}-write-{level}-{type}"{checked-w}> <label for="{id}-write-{level}-{type}">'+msg.write+'</label><br>'
					+'<input type="checkbox" value="5" id="{id}-unlock-{level}-{type}"{checked-u}> <label for="{id}-unlock-{level}-{type}">'+msg.unlock+'</label><br>'
					+'<input type="checkbox" value="8" id="{id}-hidden-{level}-{type}"{checked-h}{disabled-h}> <label for="{id}-hidden-{level}-{type}">'+msg.hidden+'</label></fieldset>',
		tab        : '<div id="{id}-tab"><ul><li><a href="#{id}-tab-perm">'+msg.perms+'</a></li><li><a href="#{id}-tab-umask">'+msg.newitem+'</a></li></ul>'
					+'<div id="{id}-tab-perm">{permTable}</div><div id="{id}-tab-umask">{umaskTable}</div></div>',
		groups     : '<fieldset id="{id}-fieldset-groups"><legend>'+fm.i18n('targetgroups')+'</legend><div id="{id}-groups"><span class="'+spclass+'"/></div></fieldset>',
		groupCheck : '<input type="checkbox" id="{id}-group-{gid}" name="gids" value="{gid}"{checked} /><label for="{id}-group-{gid}">{gname}</label>'
	};

	this.shortcuts = [{
		pattern     : 'ctrl+p'
	}];

	this.getstate = function(sel) {
		var fm = this.fm;
		sel = sel || fm.selected();
		return !this._disabled && sel.length == 1 && fm.file(sel[0]).isowner && !fm.file(sel[0]).alias ? 0 : -1;
	};

	this.exec = function(hashes) {
		var fm  = this.fm,
		dfrd    = $.Deferred().always(function() {
			fm.enable();
		}),
		tpl     = this.tpl,
		files   = this.files(hashes),
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
			
			fm.request({
				data : {
					cmd    : 'perm',
					target : file.hash,
					perm   : perm,
					umask  : umask,
					gids   : gids,
					filter : filter
				},
				notify : {type : 'perm', cnt : 1}
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
				if ($("#"+id+"-read-"+level[i]+'-'+type).attr('checked') == 'checked') {
					_perm = (_perm | 4);
				}
				if ($("#"+id+"-write-"+level[i]+'-'+type).attr('checked') == 'checked') {
					_perm = (_perm | 2);
				}
				if ($("#"+id+"-unlock-"+level[i]+'-'+type).attr('checked') == 'checked') {
					_perm = (_perm | 1);
				}
				if ($("#"+id+"-hidden-"+level[i]+'-'+type).attr('checked') == 'checked') {
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
				$("#"+id+"-read-"+level[i]+'-'+type).removeAttr('checked');
				$("#"+id+"-write-"+level[i]+'-'+type).removeAttr('checked');
				$("#"+id+"-unlock-"+level[i]+'-'+type).removeAttr('checked');
				$("#"+id+"-hidden-"+level[i]+'-'+type).removeAttr('checked');
				if ((_perm & 4) == 4) {
					$("#"+id+"-read-"+level[i]+'-'+type).attr('checked', 'checked');
				}
				if ((_perm & 2) == 2) {
					$("#"+id+"-write-"+level[i]+'-'+type).attr('checked', 'checked');
				}
				if ((_perm & 1) == 1) {
					$("#"+id+"-unlock-"+level[i]+'-'+type).attr('checked', 'checked');
				}
				if (i && (_perm & 8) == 8) {
					$("#"+id+"-hidden-"+level[i]+'-'+type).attr('checked', 'checked');
				}
			}
			setperm(type);
		},
		makeDataTable = function(perm, type) {
			var _perm;
			var value = '';
			var dataTable = tpl.dataTable;
			for (var i = 0; i < 3; i++){
				_perm = parseInt(perm.slice(i, i+1), 16);
				if (type == 'umask') {
					_perm = 0xf - _perm;
				}
				value += _perm.toString(16);
				fieldset = tpl.fieldset.replace('{f_title}', fm.i18n(level[i])).replace(/\{level\}/g, level[i]);
				dataTable = dataTable.replace('{'+i+'}', fieldset).replace('{checked-r}', ((_perm & 4) == 4)? checked : '').replace('{checked-w}', ((_perm & 2) == 2)? checked : '').replace('{checked-u}', ((_perm & 1) == 1)? checked : '').replace('{checked-h}', ((_perm & 8) == 8)? checked : '').replace('{disabled-h}', ((i == 0)? ' disabled' : ''));
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
			buttons : buttons,
			close : function() { $(this).elfinderdialog('destroy'); }
		},
		dialog = fm.getUI().find('#'+id),
		tmb = '', title, dataTable, targetGroups, filter;

		if (dialog.length) {
			dialog.elfinderdialog('toTop');
			return $.Deferred().resolve();
		}

		view  = view.replace('{class}', fm.mime2class(file.mime));
		title = tpl.itemTitle.replace('{name}', file.name).replace('{kind}', fm.mime2kind(file));

		if (file.tmb) {
			tmb = fm.option('tmbUrl')+file.tmb;
		}

		if (file.mime == 'directory') {
			dataTable = tpl.tab.replace('{permTable}', makeDataTable(file.perm, 'perm')).replace('{umaskTable}', makeDataTable(file.umask, 'umask'));
			filter = tpl.filter.replace('{value}', file.filter);
		} else {
			dataTable = makeDataTable(file.perm, 'perm');
			filter = '';
		}
		//dataTable = dataTable.replace(/{id}/g, id);
		targetGroups = tpl.groups;

		view = view.replace('{title}', title).replace('{filter}', filter).replace('{dataTable}', dataTable).replace('{targetGroups}', targetGroups).replace(/{id}/g, id);

		buttons[fm.i18n('btnCancel')] = function() { dialog.elfinderdialog('close'); };
		buttons[fm.i18n('btnApply')] = save;

		dialog = fm.dialog(view, opts);
		dialog.attr('id', id);

		// load thumbnail
		if (tmb) {
			$('<img/>')
				.load(function() { dialog.find('.elfinder-cwd-icon').css('background', 'url("'+tmb+'") center center no-repeat'); })
				.attr('src', tmb);
		}

		$('#' + id + '-table-perm :checkbox').click(function(){setperm('perm');});
		$('#' + id + '-perm').keydown(function(e) {
			var c = e.keyCode;
			e.stopPropagation();
			if (c == 13) {
				save();
				return;
			}

		});
		$('#' + id + '-perm').keyup(function(e) {
			if ($(this).val().length == 3) {
				setcheck($(this).val(), 'perm');
			}
		});
		$('#' + id + '-filter').keydown(function(e) {
			e.stopPropagation();
		});
		if (file.mime == 'directory') {
			$('#' + id + '-tab').tabs();
			$('#' + id + '-table-umask :checkbox').click(function(){setperm('umask');});
			$('#' + id + '-umask').keydown(function(e) {
				var c = e.keyCode;
				e.stopPropagation();
				if (c == 13) {
					save();
					return;
				}
			});
			$('#' + id + '-umask').keyup(function(e) {
				if ($(this).val().length == 3) {
					setcheck($(this).val(), 'umask');
				}
			});
		}
		
		fm.request({
			data : {cmd : 'perm', target : file.hash, perm : 'getgroups'},
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
			replSpinner(data.uname, id+'-owner-name');
		});

		return dfrd;
	};
};