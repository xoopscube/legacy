// ----------------------------------------------------------------------------
// SmartMarkUP - Universal Markup Editor and Engine
// v 1.0
// Licensed under GPL license.
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Joseph Woods & PHPCow.com
// http://www.phpcow.com/smartmarkup
// ----------------------------------------------------------------------------
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
// ----------------------------------------------------------------------------
var SMarkUp = function() {
	
	var ua 		= navigator.userAgent.toLowerCase(),
		browser = {
		opera:  ua.indexOf('opera')  > -1,
		ie:     ua.indexOf('msie') 	 > -1,
		ie6:    ua.indexOf('msie 6') > -1,
		ie7:    ua.indexOf('msie 7') > -1,
		safari: ua.indexOf('webkit')
	};
	
	var sMarkUpPath = '', scripts = document.getElementsByTagName('script');
		
	for (var i = 0; i < scripts.length; i++) {
		if (scripts[i].src) {
			var tokens = scripts[i].src.split('/'),
				file   = tokens.pop();
			if (/smarkup\.?(.*?)\.js$/i.test(file)) {
				sMarkUpPath = tokens.join('/');
				break;
			}
		}
	}
	
	scripts = null;
	
	var hasClass = function(cls) {
		return new RegExp('(^|\\s)' + cls + '(\\s|$)');
	};
	
	String.prototype.pasteTo = function(field, keepSelection) {
		field.focus();
		var start = 0;
		if (document.selection && !browser.opera) {
			var range = document.selection.createRange(),
				copy  = range.duplicate();
				
			copy.moveToElementText(field);
			copy.setEndPoint('EndToEnd', range);
			start = copy.text.strlen() - range.text.strlen();
			range.text = this;
			if (keepSelection) {
				copy.moveStart('character', start);
				copy.moveEnd('character', this.strlen());
				copy.select();
			}
			
		} else if (field.selectionStart != undefined) {
			
			start = field.selectionStart
				
			var value  = field.value,
				scroll = field.scrollTop, 
				end    = field.selectionEnd,
				len	   = start + this.strlen();
			
			field.value 	= value.substr(0, start) + this + value.substr(end, value.length);								
			field.scrollTop = scroll;
			
			if (keepSelection) {
				field.setSelectionRange(start, len);
			} else {
				field.setSelectionRange(len, len);
			}
			
		} else {
			field.value += this;
			field.setSelectionRange(field.value.length, field.value.length);		
		}
		return start;
	};
	
	String.prototype.parse = function(map) {
		var s = this;
		for (var i in map) {
			s = s.replace(new RegExp('{' + i + '}', 'g'), map[i]);
		}
		return s;
	};
	
	String.prototype.strlen = function() {
		var len = this.length;
		if (browser.opera) {
			len += this.length - this.replace(/\n*/g, '').length; 
		} else if (browser.ie) {
			len -= this.length - this.replace(/\r*/g, '').length;
		}
		return len;
	};
	
	if (!String.prototype.trim) {
		String.prototype.trim = function() {
			return this.replace(/^\s+|\s+$/g, "");
		};
	}
	
	var Dialog = function(id) {
					
		var toggle = function(display) {
			var dialog = $(document.getElementById(id))
				.find('div.smarkup-overlap,div.smarkup-dialog')
				.css({display: display ? 'block' : 'none'})[1];
			if (display == false) {
				var form = $(dialog).find('div.smarkup-dialog-form')[0];
				var parent = form.parentNode;
				parent.removeChild(form);
				parent.innerHTML = '<div class="smarkup-dialog-form"></div>'
			}
			return dialog;
		};
		
		var button = function(name, value, click) {
			var b = document.createElement('input');
			b.type = 'button';
			b.name = name;
			b.value = value;
			b.className = name + '-button';
			b.onclick = click;
			return b;
		};
		
		var getAtts = function(context) {
			var atts = {};
			$(context).parent('div').find('input,select').each(
				function() {
					if (this.type != 'button') {
						atts[this.name.replace(/^smu_/, '')] = this.value;
					}
				}
			);
			return atts;
		};
		
		this.show = function(params) {
			
			var dialog  = toggle(true),
				events  = params.events || {},
				form 	= $(dialog).find('div.smarkup-dialog-form')[0],
				content = params.content || {};
			
			if (!form.parentNode.onkeydown) {
				$(form.parentNode).bind({keydown: function(e, target) {
					var key = e.which || e.keyCode;
					if (key == 13) {
						if ((target.type || null) == 'text') {					
							$(this).find('input.insert-button')[0].onclick(e);
							return false;
						}
					}
				}});
			}
			
			if (content.content) {
				content.content.call(this, form);
				if (content.events) {
					for (var evt in content.events) {
						var context = this;
						form[evt] = function(e) {
							content.events[evt].call(context, e || window.event, getAtts(this));
							return false;
						}
					}
				}
			} else if (typeof content == 'string') {
				form.innerHTML = content;
			} else {
				form.appendChild(content);
			}
			
			var p = document.createElement('p');
			form.appendChild(p);
			
			var context = this;
			
			if (events.onsave || content.onBeforeInsert) {
				p.appendChild(
					button(
						'insert', 'Ok',
						function(e) {
							var atts = getAtts(this);	
							if (content.onBeforeInsert) {
								content.onBeforeInsert.call(context, atts);
							} else {
								events.onsave.callback.call(this, e, atts);
							}
							context.hideDialog();
						}
					)
				);
			}
			
			p.appendChild(
				button(
					'cancel', 'Cancel',
					function(e) {
						if (events.oncancel) {
							events.oncancel.call(this, e);
						}
						e.stopPropagation();
						context.select();
					}
				)
			);
			
			$(dialog).css({marginTop: '-' + (dialog.offsetHeight / 2) + 'px'});
			
			try {
				$(dialog).find('input.smarkup-dialog-text')[0].focus();
			} catch(ex) {}
			
			p = form = null;
			
			return dialog;
			
		};
		
		this.hide = function() {
			return toggle(false);
		};
		
	};
	
	var TagAttributes = function() {
		
		var cnt = 0, container;
		
		var append = function(o1, o2) {		
			container.innerHTML += '<dl><dt></dt><dd></dd></dl>';
			container.getElementsByTagName('dt')[cnt].appendChild(o1);
			container.getElementsByTagName('dd')[cnt++].appendChild(o2);
		};
		
		var label = function(value) {
			var label = document.createElement('label');
			label.innerHTML = value + ':&nbsp;';
			return label;
		};
		
		var el = function(tag, name) {
			var input = (browser.ie && !browser.opera) ? document.createElement('<' + tag +' name="smu_' + name + '"/>') : document.createElement(tag);
			input.name = 'smu_' + name;
			return input;
		};
		
		var input = function(atts) {
			var input = el('input', atts.name);
			input.type = atts.type;
			input.value = atts.value || '';
			input.className = atts.className || '';
			append(label(atts.label), input);
			input = null;
		};
		
		var text = function(atts) {
			atts.className = 'smarkup-dialog-text'
			input(atts);
		};
		
		var list = function(atts) {
			
			var addTo = function(to, what, selected) {			
				var opt, value, text;				
				for (var i in what) value = i; text = what[i];				
				opt = document.createElement('option');
				opt.setAttribute('value', value);
				opt.innerHTML = text;
				opt.selected = selected == value;				
				to.appendChild(opt);				
			};
			
			var s = el('select', atts.name), list = atts.list, og, obj;
	
			for (var i in list) {
			
				if (list[i].constructor == String) {
					obj = {};
					obj[i] = list[i];
				} else {
					obj = list[i];
				}
				
				if (!obj.group && !obj.options) {
					addTo(s, obj, atts.value || null);
				} else {
					og = document.createElement('optgroup');
					og.label = obj.group;
					for (var j in obj.options) {
						addTo(og, obj.options[j], atts.value || null);
					}
					s.appendChild(og);
					og = null;					
				}
				
			}
			
			append(label(atts.label), s);
			s = null;
			
		};
		
		return {
			
			build: function(atts, title) {
				
				cnt = 0; container = document.createElement('div');
				
				if (title) {
					container.innerHTML = '<h3>' + title + '</h3>';
				}
				
				for (var i = 0; i < atts.length; i++) {
					if (atts[i].type == 'text') {
						text(atts[i]);
					} else if (atts[i].type == 'list') {
						list(atts[i]);
					}
				}
		
				return container;
				
			}
			
		};
		
	}();
	
	var fn = function(context) {		
		this.length = 0;
		this.push([context]);		
		return this;		
	};
	
	fn.prototype = function() {
		
		var find = function(o, type, q) {
		
			var result = [];
			var tokens = q.split(',');
			
			var compare = function(o, tag, cls, result) {
				var found = 0;
				if (cls && hasClass(cls).test(o.className)) {
					if (!tag || tag == o.tagName.toLowerCase()) {
						found = result.push(o);
					}
				} else if (!cls && tag == o.tagName.toLowerCase()) {
					found = result.push(o);
				}
				return !!found;
			};
			
			var parse = function(q) {
				var tokens = q.split('.');
				return {
					tag: tokens[0],
					cls: tokens[1]
				};
			};
			
			for (var i = 0; i < tokens.length; i++) {
				q = parse(tokens[i]);
				for (var j = 0; j < o.length; j++) {
					if (type == 'parent') {
						var p = null;
						for (p = o[i].parentNode; p && p != document.body; p = p.parentNode) {
							if (compare(p, q.tag, q.cls, result)) break;
						}
						break;
					} else if (type == 'children') {
						var children = o[j].childNodes;
						for (var k = 0; k < children.length; k++) {
							compare(children[k], q.tag, q.cls, result);
						}
					} else if (type == 'find') {
						var children = o[j].getElementsByTagName(q.tag || '*');
						for (var k = 0; k < children.length; k++) {
							compare(children[k], q.tag, q.cls, result);
						}
					}
				}
			}
			
			return result;
			
		};
		
		var event = function(evt) {
			return function(e) {
				e = e || window.event;
				var target = e.target || e.srcElement;
				if (target.nodeType == 3) target = target.parentNode;
				return evt.call(this, e, target);
			}
		};
		
		return {
		
			push: function(arg) {
				this.length = 0;
				Array.prototype.push.apply(this, arg);
			},
			
			each: function(fn) {
				for (var i = 0; i < this.length; i++) {
					fn.call(this[i]);
				}
				return this;
			},
			
			css: function(css) {
				for (var i in css) {
					for (var j = 0; j < this.length; j++) {
						this[j].style[i] = css[i];
					}
				}
				return this;
			},
			
			parent: function(q) {
				this.push(find(this, 'parent', q));
				return this;			
			},
			
			children: function(q) {
				this.push(find(this, 'children', q));
				return this;			
			},
			
			find: function(q) {
				this.push(find(this, 'find', q));
				return this;
			},
			
			bind: function(events) {
				this.each(
					function() {
						for (var i in events) {
							this['on' + i] = event(events[i]);
						}
					}
				);
				return this;
			}, 
			
			attach: function(events) {
				this.each(
					function() {
						for (var i in events) {
							if (this.addEventListener) {
								this.addEventListener(i, event(events[i]), false);
							} else {
								el.attachEvent('on' + i, function(el) {
									return function() {
										return event(events[i]).call(el);
									}
								}(this));
							}
						}
					}
				);
			}
			
		};
		
	}();
	
	var $ = function(context) {
		return new fn(context);
	};
	
	$.ajax = function() {
		
		var getXHR = function() {
			return window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
		};
		
		var req = function(options) {
			var xhr = getXHR();
			xhr.open(options.method, options.url + '?&nocache=' + (new Date().getTime()), options.async);
			if (options.async) {
				xhr.onreadystatechange = function() {
					if (xhr.readyState == 4 && xhr.status == 200) {
						options.complete(xhr.responseText);
					}
				};
			}
			xhr.setRequestHeader('content-type', 'application/x-www-form-urlencoded');
			if (options.data) {
				var kv = [];
				for (var i in options.data) {
					kv.push(encodeURIComponent(i) + '=' + encodeURIComponent(options.data[i]));
				}
				options.data = kv.join('&');
			} else {
				options.data = null;
			}
			xhr.send(options.data);
			if (!options.async) {
				options.complete(xhr.responseText);
			}
		};
		
		return {
			get: function(options) {
				options.method = 'get',
				req(options);
			},
			post: function(options) {
				options.method = 'post',
				req(options);
			}
		};
		
	}();
	
	function instance(id, config, height) {
		
		height = parseInt(height) || 300;
		
		var sid      	  = 'id-smarkup-' + id,
			dlg      	  = new Dialog(sid),
			tags     	  = {},
			accessKeys	  = {},
			context  	  = this,
			altKey   	  = shiftKey = ctrlKey = false,
			templatePath  = null,
			parserPath	  = null,
			parserVar	  = null,
			autoRefresh   = false,
			caretPosition = 0;

		if (config.preview) {
			var preview = config.preview;
			if (preview.template) {
				templatePath = preview.template.replace(/^~/, sMarkUpPath);
			} else if (preview.parser) {
				parserPath = preview.parser.replace(/^~/, sMarkUpPath);
			}
			parserVar = preview.parserVar || null;
			autoRefresh = preview.autoRefresh || false;
		}
		
		var parent = function() {
			return document.getElementById(sid);
		};
		
		var field = function() {
			return document.getElementById(id);
		};
		
		var select = function(start, len) {
			var fld = field();
			fld.focus();
			if (isNaN(start) && isNaN(len)) {
				if (browser.ie) {
					var range = document.selection.createRange(),
					copy  = range.duplicate();
					copy.moveToElementText(fld);
					copy.setEndPoint('EndToEnd', range);
					caretPosition = start = copy.text.strlen() - range.text.strlen();
					len = range.text.strlen();
				} else {
					caretPosition = start = fld.selectionStart;
					len   = fld.selectionEnd - fld.selectionStart;
				}
			}
			if (browser.ie && fld.createTextRange) {
				if (!isNaN(start) && !isNaN(len)) {
					var range = textarea.createTextRange();
					range.collapse(true);
					range.moveStart('character', start); 
					range.moveEnd('character', len); 
					range.select();
				}
			} else if (fld.setSelectionRange) {
				scroll = fld.scrollTop;
				fld.setSelectionRange(start, start + len);
				fld.scrollTop = scroll;
			}
		};
		
		var selection = function() {
			var fld = field(),
				sel = null;
			if (document.selection) {
				sel = document.selection.createRange().text.replace(/\r/g, '');
			} else {
				sel = fld.value.substr(fld.selectionStart, fld.selectionEnd - fld.selectionStart);
			}
			return sel;
		};
		
		var paste = function(params, attributes) {
			
			var markup = [],
				string = [],
				open   = params.open || '',
				close  = params.close || '',
				wrap   = params.wrapSelection || '',
				fld    = field(),
				sel    = selection().trim(),
				phlen  = false;
				
			if (!sel) {
				sel    = params.placeholder || '';
				phlen  = sel.length || -1;
			}
			
			if ((shiftKey && ctrlKey) || params.wrapMultiline) {
				sel = sel.match(/^(.*?)$/gm);
			} else {
				sel = [sel];
			}
			
			if (altKey && params.alt) {
				open = params.alt.open || '';
				close = params.alt.close || '';
			}
						
			markup.push(params.prepend || '');
			markup.push(open + '{selection}' + close);
			markup.push(params.append || '');
			markup = markup.join('');
			
			if (wrap && !wrap.replace(/{selection}|\n/g, '')) {
				markup = markup.replace('{selection}', wrap);
				wrap = null;
			}
			
			if (wrap) {
				for(var i = 0, s = null; i < sel.length; i++) {
					if (s = wrap.replace('{selection}', sel[i].trim())) {
						string.push(s);
					}
				}
				markup = markup.replace('{selection}', string.join(''));
			} else {
				for(var i = 0, s = null; i < sel.length; i++) {
					if (s = markup.replace('{selection}', sel[i].trim())) {
						string.push(s);
					}
				}
				markup = string.join('');
			}
			
			if (attributes) {
				markup = markup.parse(attributes).parse(
					{
						attributes: function() {
							var result = [];							
							for (var i in attributes) {
								result.push(i + '="' + attributes[i] + '"');
							}
							return ' ' + result.join(' ');
						}
					}
				);
			}
			
			caretPosition = markup.replace(/\n{2,}/g, "\n").pasteTo(fld);
			
			if (phlen > -1) {
				var str = open + (params.prepend || '');
				select(caretPosition + (browser.opera ? str.strlen() : str.length), phlen);
			}
			
			if (autoRefresh) preview();
			
		};
		
		var insert = function(string) {
			caretPosition = string.pasteTo(field());
			if (autoRefresh) preview();
		};
		
		var dialog = function(target) {
			
			if (!target) return false;
			
			var conf 	= target.rel ? tags[target.rel] : target,
				content = conf.dialog || null;
			
			if (!content) {
				dlg.show.call(
					context,
					{
						content: TagAttributes.build(conf.attributes, conf.title || ''),
						events: {
							onsave: {
								callback: function(evt, atts) {										
									paste(conf, atts);									
								}
							},
							oncancel: function() {
								dlg.hide();
							}
						}
					}
				);
			} else {
				dlg.show.call(
					context,
					{
						content: content,
						events: {
							oncancel: function() {
								dlg.hide();
							}
						}
					}
				);
			}
			
			return false;
			
		};
		
		var template = '';
		
		var preview = function() {
			
			var wrapper = $(parent()).find('div.smarkup-preview')[0],
				iframe  = null;
				
			$(wrapper).css({display: 'block'});
			iframe = wrapper.getElementsByTagName('iframe')[0] || null;
			
			if (!iframe) {
				iframe = document.createElement(browser.ie ? '<iframe frameborder="0"></iframe>' : 'iframe');
				iframe.src = '';
				iframe.className = 'smarkup-preview-iframe';
				wrapper.appendChild(iframe);
			}
			
			if (!wrapper.onclick) {
				wrapper.onclick = function() {
					$(this).css({display: 'none'});
					iframe = this.getElementsByTagName('iframe')[0] || null;
					if (iframe) {
						iframe.parentNode.removeChild(iframe);
						iframe = null;
					}					
				}
			}
			
			if (templatePath || parserPath) {
				var complete = function(text) {
					iframe = iframe.contentWindow || iframe;
					iframe.document.open();
					iframe.document.write(text.parse({content: field().value}));
					iframe.document.close();
					iframe = null;
				};
				if (templatePath && !template) {
					$.ajax.get(
						{
							url: templatePath,
							complete: complete,
							async: false
						}
					);
				} else if (parserPath) {
					var data = {};
					data[parserVar || 'data'] = field().value;
					$.ajax.post(
						{
							url: parserPath,
							complete: complete,
							data: data,
							async: true
						}
					);
				}
			}
		};
		
		var tpl = '<div class="smarkup ' + config.name + '" id="' + sid + '">' +
			'<div class="smarkup-toolbar">' +
				'<ul class="smarkup-toolbar"></ul>' +
				'<br clear="all" />' +
			'</div>' +
			'<div class="smarkup-search">' +
				'<input type="text" name="smu_qsearch" class="qsearch" autocomplete="off" />' +
				'<a href="#" title="Move to next match"></a>' +
				'<small></small>' +
			'</div>' +
			'<div class="smarkup-textarea-wrapper">' +
				'<div class="smarkup-textarea"></div>' +
			'</div>' +
			'<div class="smarkup-preview">' +
				'<div class="smarkup-preview-header">Document Preview [ x ]</div>' +
			'</div>' +
			'<div class="smarkup-overlap"></div>' +
			'<div class="smarkup-dialog">' +
				'<div class="smarkup-dialog-header">' +
					'<div class="smarkup-dialog-tl"></div><div class="smarkup-dialog-tr"></div>' +
				'</div>' +
				'<div class="smarkup-dialog-body">' +
					'<div class="smarkup-dialog-form"></div>' +
				'</div>' +
				'<div class="smarkup-dialog-footer">' +
					'<div class="smarkup-dialog-bl"></div><div class="smarkup-dialog-br"></div>' +
				'</div>' +
			'</div>'
		'</div>';
		
		var textarea  = field(),
			tmp 	  = document.createElement('div');
		tmp.innerHTML = tpl;
		
		textarea.parentNode.insertBefore(
			tmp.getElementsByTagName('div')[0],
			textarea
		);
		
		//store defaut class and style for later use
		textarea.oldClass = textarea.className || '';
		textarea.oldCSS = textarea.style.cssText || '';
		//remove class and style
		textarea.className == '';
		textarea.style.cssText = '';
		
		$(textarea).css({height: height + 'px'});
		
		var onKeyPress = function(e) {
			
		var key = e.which || e.keyCode;
			
			context.shiftKey = shiftKey = e.shiftKey;
			context.altKey   = altKey 	= e.altKey;
			context.ctrlKey  = ctrlKey 	= e.ctrlKey || e.metaKey;
			
			if (browser.opera && /^(keydown|keypress)$/.test(e.type)) {
				context.altKey  = altKey = key == 18;
				context.ctrlKey = key == 17;
			}
			
			var qsearch = function() {
				$(parent()).find('div.smarkup-search').css({display: 'block'}).find('input.qsearch')[0].focus();
				return false;
			};
			
			if (e.type == 'keydown') {
				
				if (key == 9) {
					var matches = selection().match(/^(.*?)$/mg);
					if (matches.length > 1) {
						for (var i = 0; i < matches.length; i++) {
							if (!shiftKey) {
								matches[i] = matches[i].trim() ? '   ' + matches[i] : '';
							} else {
								matches[i] = matches[i].replace(/^   /, '');
							}
						}
						caretPosition = matches.join("\n").pasteTo(this, true);
					} else {
						caretPosition = "   ".pasteTo(this);
					}
					return false;
				} else if (key == 13) {
					var enter = null;
					if (ctrlKey) {
						enter = config.onCtrlEnter || null;
					} else if (shiftKey) {
						enter = config.onShiftEnter || null;
					}
					if (enter) {
						insert((enter.open || '') + (enter.close || ''));
						return false;
					}
				} else if (key == 27) {
					$(parent()).find('div.smarkup-search').css({display: 'none'});
				} else if (key == 112) {
					dialog(tags['help']);
					return false;
				}
				if (ctrlKey) {
					var accessKey = String.fromCharCode(key).toLowerCase();
					if (accessKeys[accessKey]) {
						if (accessKeys[accessKey].dialog) {
							dialog(accessKeys[accessKey])
						} else {
							paste(accessKeys[accessKey]);
						}
						return false;
					}
				}				
			} else if (e.type == 'keyup') {
				if (key == 70 && ctrlKey) {
					return qsearch();
				}			
			} else if (e.type == 'keypress') {
				if (browser.opera) {
					if (key == 9 || (ctrlKey && (key == 112 || key == 98))) {
						return false;
					}
				}
				if (key == 102 && ctrlKey) {
					return qsearch();
				}
			}
			
		};
		
		$(textarea).bind(
			{
				keydown: onKeyPress,
				keyup: onKeyPress,
				keypress: onKeyPress,
				focus: function(e) {
					SMarkUp.focused = SMarkUp.instances[this.id];
				}
			}
		);
		
		$(parent()).find('div.smarkup-textarea')[0].appendChild(textarea);
		
		var conf    = config.markup,
			button  = '<li{ddcls}><a href="#" rel="{tag}" class="{cls}" title="{title}"></a>{ddmenu}</li>',
			sep	    = '<li class="separator">------------------------</li>',
			ddtpl	= '<div class="smarkup-ddmenu"><ul class="smarkup-ddmenu">{tags}</ul></div>',
			li      = '<li><a href="#" rel="{tag}" class="{cls}">{title}</a></li>',
			groups  = [],
			buttons = [];
		
		for (var i in conf) {
			if (!conf[i].separator) {
			  	var listitem = button.parse(
			  		{
			  			tag: conf[i].name,
			  			cls: conf[i].className || conf[i].name,
			  			title: (conf[i].title || '') + (conf[i].key ? ' [Ctrl + ' + conf[i].key.toUpperCase() + ']' : '')
			  		}
			  	);
				if (conf[i].dropDownMenu) {
					var ddmenu = [];
					for (var j = 0; j < conf[i].dropDownMenu.length; j++) {
						var item = conf[i].dropDownMenu[j];
						ddmenu.push(
							li.parse(
								{
									tag: item.name,
									title: item.title || item.name,
									cls: item.className || item.name
								}							
							)  
						);
						tags[item.name] = item;
						if (item.key) {
							accessKeys[item.key.toLowerCase()] = item;
						}
					}					
					listitem = listitem.parse(
						{
							ddcls: ' class="ddmenu"', 
							ddmenu: ddtpl.parse({tags: ddmenu.join('')})
						}
					);
				} else {
					listitem = listitem.parse({ddcls: '', ddmenu: ''});
				}
				buttons.push(listitem);
			}
			if (conf[i].separator) {
				groups.push(buttons.join(''));
				buttons = [];
			} else {
				tags[conf[i].name] = conf[i];
				if (conf[i].key) {
					accessKeys[conf[i].key.toLowerCase()] = conf[i];
				}
			}
		}
		
		if (buttons.length) {
			groups.push(buttons.join(''));
		}
		
		$(parent()).find('ul.smarkup-toolbar').bind(
			{			
				click: function(e, target) {
					if (!target.parentNode.className) {
						var conf = context.button = tags[target.rel];
						if (conf.invoke && context[conf.invoke]) {
							context[conf.invoke].call(context);
							return false;
						} else if (conf.dialog) {
							dialog(target);
						} else if (!conf.attributes) {
							if (conf.onBeforeInsert) {
								conf.onBeforeInsert.call(context);
							} else {
								paste(tags[target.rel]);
							}
						} else if (conf.replace) {
							conf.replace.call(this);
						} else {
							dialog(target);
						}
					}
					return false;			
				},
				contextmenu: function(e) {
					return false;
				}
			}
		)[0].innerHTML = groups.join(sep);
		
		$(parent()).find('input.qsearch').bind(
			{
				keyup: function(e) {
					
					var msg = '', a = $(this).parent('div').find('a')[0],
						value = this.value.replace(/\\/g, '');
					
					if (value) {
						
						var regex 	= new RegExp(value, "gi"),
							matches = [],
							string 	= field().value,
							len 	= value.length;
						
						while(regex.test(string)) {
							matches.push({
								start: regex.lastIndex - len,
								end: len
							});
						}
						
						if (matches.length > 0) {
							msg = matches.length + ' matches found';
							a.matches = matches;
							a.index = 0;
							if (!a.onclick) {
								a.onclick = function() {
									if (this.matches) {
										if (this.index == this.matches.length) {
											this.index = 0;
										}
										var textarea = field(),
											pos = this.matches[this.index++];
										select(pos.start, pos.end);
									}
									return false;
								}
							}
						} else {
							msg = 'Nothing found'
							a.matches = null;
						}
						
					} else {
						msg = '';
						a.matches = null;
					}
					
					$(this).parent('div').find('small')[0].innerHTML = msg;
					
				},
				keydown: function(e) {
					var key = e.which || e.keyCode;
					if (key == 13) {
						$(this).parent('div').find('a')[0].onclick();
						return false;
					} else if (key == 27) {
						$(parent()).find('div.smarkup-search').css({display: 'none'});
						field().focus();
						return false;
					}
				},
				keypress: function(e) {
					if (browser.opera && (e.which || e.keyCode) == 13) {
						return false;
					}
				},
				focus: function() {
					this.select();
				}
			}
		);
		
		$(document).bind({
			keydown: function(e) {
				if ((e.which || e.keyCode) == 27) {
					dlg.hide();
					select();
				}
			}
		});
		
		var remove = function() {			
			var textarea = field();
			var parent = $(textarea).parent('div.smarkup')[0];
			parent.parentNode.insertBefore(textarea, parent);
			parent.parentNode.removeChild(parent);
			id = textarea.id;
			if (/^smu-/.test(id)) {
				textarea.id = '';
			}
			//restore previous class and style
			textarea.className = textarea.oldClass;
			textarea.style.cssText = textarea.oldCSS;
			//unbind keyup, keydown and keypress events
			textarea.onkeydown = textarea.onkeyup = textarea.onkeypress = null;
			//remove instance from cache
			delete SMarkUp.instances[id];
		};
		
		/** public methods and properties */
		
		this.id = id;
		this.name = field().name || id;
		this.button = null;
		this.shiftKey = this.ctrlKey = this.altKey = false;
		
		this.paste = function(string) {
			insert(string);
		};
		
		this.insert = function(options) {
			paste(options);
		};
		
		this.select = function(start, len) {
			select(start, len);
		};
		
		this.selection = function() {
			return selection();
		};
		
		this.value = function(value) {
			if (!value) {
				return field().value;
			} else {
				field().value = value;
			}
		};
		
		this.textarea = function() {
			return field();
		};
		
		this.buttons = function() {
			return tags;
		};
		
		this.showDialog = function(conf) {
			dialog(conf);
		};
		
		this.hideDialog = function() {
			dlg.hide();
		};
		
		this.preview = function() {
			preview();
		};
		
		this.remove = function() {
			remove();
		};
		
	};
	
	return {		
		bind: function(id, conf, height) {
			var els = [];
			if (typeof id == 'string') {
				els.push(document.getElementById(id));
			} else if (typeof id == 'object') {
				if (id.nodeType) {
					els.push(id);
				} else if (id.constructor) {
					if (id.constructor == Function) {
						els = id();
					} else if (id.length) {
						for (var i = 0; i < id.length; i++) {
							if (typeof id[i] == 'string') {
								els.push(document.getElementById(id[i]));
							} else if (typeof id[i] == 'object' && id[i].nodeType) {
								els.push(id[i]);
							}
						}
					}
				}
			}
			this.conf[conf].name = conf;
			for (var i = 0; i < els.length; i++) {
				if (!els[i].id) {
					els[i].id = 'smu-' + (new Date().getTime());
				}
				this.instances[els[i].id] = new instance(els[i].id, this.conf[conf], height);
			}
			els = null;
		},
		insert: function(options) {
			if (options.target && SMarkUp.instances[options.target]) {
				SMarkUp.instances[options.target].insert(options);
			} else {
				for (var i in SMarkUp.instances) {
					SMarkUp.instances[i].insert(options);
				}
			}
		},
		instances: {},
		getInstance: function(id) {
			return SMarkUp.instances[id] || null;
		},
		getInstanceByName: function(name) {
			for (var i in SMarkUp.instances) {
				SMarkUp.instances[i].name == name;
				return SMarkUp.instances[i];
			}
			return null;
		},
		conf: {},
		registerAddOns: function(map) {
			for (var i in map.addons) {
				SMarkUp.addons[i] = map.addons[i];
			}
			var style = map.style || false;
			if (style == true) {
				var link  = document.createElement('link');
				link.rel  = 'stylesheet';
				link.type = 'text/css';
				link.href = sMarkUpPath + '/addons/' + map.name + '/style.css';
				document.getElementsByTagName('head')[0].appendChild(link);
			}
		},
		addons: {
			searchAndReplace: {
				name: 'find',
				title: 'Search &amp; Replace',
				key: 'H',
				dialog: {
					content: function(content) {
						var tpl = '<h3>Search &amp; Replace</h3>' +
						'<dl>' +
							'<dt><label>Search:</label></dt>' +
							'<dd><input type="text" name="smu_search" value="" class="smarkup-dialog-text"/></dd>' +
						'</dl>' +
						'<dl>' +
							'<dt><label>Replace:</label></dt>' +
							'<dd><input type="text" name="smu_replace" class="smarkup-dialog-text"/></dd>' +
						'</dl>';
						content.innerHTML = tpl;
					},
					onBeforeInsert: function(fields) {
						
						if (fields.search) {
							
							var regex = new RegExp(fields.search, 'gi');
							
							if (this.selection()) {
								this.paste(
									this.selection().replace(
										regex,
										fields.replace
									)
								);
							} else {
								this.value(
									this.value().replace(
										regex,
										fields.replace
									)
								);
							}
							
						}
						
					}
				}
			}, //end search and replace
			preview: {
				name: 'preview',
				invoke: 'preview',
				title: 'Preview Document'
			},
			help: {
				name: 'help',
				title: 'Quick Help',
				dialog: {
					content: function(container) {
						var shortcuts = {
							'Tab': 'Use Tab key to indent multiline selection',
							'Shift + Tab': 'Use Shift + Tab keys to decrease indent of multiline selection',
							'Ctrl + Shift + Button Click': 'Will aply markup to all selected lines instead of whole selection',
							'Cmd + Shift + Button Click': 'Will aply markup to all selected lines (Mac OS X only)',
							'Alt + Button Click': 'Will wrap text in alternative markup if alternate markup is defined for the button',
							'Ctrl + F': 'Will open Quick Search toolbar for focused textarea',
							'Cmd + F': 'Will open Quick Search toolbar for focused textarea (Mac OS X only)',
							'Esc': 'Will hide Quick Search toolbar for focused textarea or will hide any opened modal dialog',
							'Ctrl + F1': 'Will show Quick Help',
							'Cmd + F1': 'Will show Quick Help (Mac OS X only)'
						},
						tpl  = '<dt>{dt}</dt><dd>{dd}</dd>',
						ico  = '<a href="#" class="{class}></a>',
						html = [];
						
						html.push('<h3>Quick Help</h3>');
						html.push('<div class="smarkup_help">');
						html.push('<h4>Shortcut Keys</h4>');
						html.push('<dl>');
						
						for (var i in shortcuts) {
							html.push(tpl.parse({dt: i, dd: shortcuts[i]}));
						}
						
						html.push('</dl>');
						html.push('</div>');
						
						container.innerHTML = html.join('');
						
					}
				}
			} //end help
		}
	};
	
}();