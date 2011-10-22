SMarkUp.registerAddOns(
	{
		name: 'commons',
		style: true,
		addons: {
			specialChars: {
				name: 'omega',
				dialog: {
					content: function(content) {
						
						var chars 	 = ['&nbsp;','&iexcl;','&cent;','&pound;','&yen;','&sect;','&uml;','&copy;','&laquo;','&not;','&reg;','&deg;','&plusmn;','&acute;','&micro;','&para;','&middot;','&cedil;','&raquo;','&iquest;','&Agrave;','&Aacute;','&Acirc;','&Atilde;','&Auml;','&Aring;','&AElig;','&Ccedil;','&Egrave;','&Eacute;','&Ecirc;','&Euml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&Ntilde;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&Oslash;','&Ugrave;','&Uacute;','&Ucirc;','&Uuml;','&szlig;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;','&aelig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&igrave;','&iacute;','&icirc;','&iuml;','&ntilde;','&ograve;','&oacute;','&ocirc;','&otilde;','&ouml;','&divide;','&oslash;','&ugrave;','&uacute;','&ucirc','&uuml','&yuml;','&#8218;','&#402;','&#8222;','&#8230;','&#8224;','&#8225;','&#710;','&#8240;','&#8249;','&#338;','&#8216;','&#8217;','&#8220;','&#8221;','&#8226;','&#8211;','&#8212;','&#732;','&#8482;','&#8250;','&#339;','&#376;'],
							table 	 = '<table class="smarkup-special-chars"><tbody>{rows}</tbody></table>',
							tr  	 = '<tr>{cols}</tr>',
							td 		 = '<td><a href="#">{char}</a></td>',
							rowCount = chars.length / 11,
							rows 	 = [];
			
						for (var i = 0; i < rowCount; i++) {
							var cols = [];
							for (var j = i * 11; j < (i * 11 + 11); j++) {
								cols.push(td.replace('{char}', chars[j]));
							}
							rows.push(tr.replace('{cols}', cols.join('')));
						}
						
						content.innerHTML = table.replace('{rows}', rows.join(''));
						
					},
					events: {
						onclick: function(e, fields) {
							this.paste((e.target || e.srcElement).innerHTML);
							this.hideDialog();
						}
					}
				}
			}, //end special chars
			colorPicker: {
				name: 'color',
				dialog: {
					
					content: function(content) {
						
						var r = g = b = 0,
							values = ['00', '33', '66', '99', 'CC', 'FF'];
				  
						var res = [];
						for (i = 0; i < 9; i++) {						    
							for (j = 0; j < 24; j++) {				
								res.push(values[r] + values[g] + values[b++]);
								if (b == values.length) {
									g++;
									b = 0;
								}
								if (g == 6) {
									r++;
									g = 0;
								}									
							}								
						}
						
						var rowCount = res.length / 18,
							table 	 = '<table cellspacing="0" class="smarkup-special-colors"><tbody>{rows}</tbody></table>',
							tr  	 = '<tr>{cols}</tr>',
							td 		 = '<td><a href="#" style="background-color: #{color};" rel="#{color}"></a></td>',
							rows	 = [];
						
						for (var i = 0; i < rowCount; i++) {
							var cols = [];
							for (var j = i * 18; j < (i * 18 + 18); j++) {
								cols.push(td.replace(new RegExp('{color}', 'g'), res[j]));
							}
							rows.push(tr.replace('{cols}', cols.join('')));
						}
						
						content.innerHTML = table.replace('{rows}', rows.join(''));
						
					},
					events: {
						onclick: function(e) {
							this.paste((e.target || e.srcElement).rel);
							this.hideDialog();
							return false;
						}
					}
				}
			}
		}
	}
);