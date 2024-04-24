/**
 * Licensed under the terms of the MIT License (see LICENSE.md).
 *
 * The yaqr plugin dialog window definition.
 *
 * Created out of the CKEditor Plugin SDK:
 * http://docs.ckeditor.com/#!/guide/plugin_sdk_sample_1
 */
// GLOBALS
var domPreviewImg = null;
var domPreviewImgAdv = null;
var myWidgetData = null;
function clean_css_value(text) {
    ////console.log( "clean_css_value value ", text );
    var re = /^(\d+(?:\.\d*)?|\.\d+)([^\d\s]+)?$/m;
    // Case 0: String does not match pattern at all.
    if (!(m = text.match(re))) return defSize;
    // Case 1: No number at all? return '';
    if (!m[1]) return "";
    var i = Math.floor(m[1]); // Compute integer portion.
    // Case 2: Nothing after number? return integer pixels
    if (!m[2]) return i + "px";
    switch (m[2]) {
        case "em":
        case "%":
            return m[1] + m[2];
        case "px":
        default:
            return i + "px";
    }
}
function getColorBgVal(val) {
    myWidgetData['QrColorBg'] = val.value;
    var img = getQrImage(myWidgetData);
    domPreviewImg.src = img;
    domPreviewImgAdv.src = img;
}
function getColorPtVal(val) {
    myWidgetData['QrColorPt'] = val.value;
    var img = getQrImage(myWidgetData);
    domPreviewImg.src = img;
    domPreviewImgAdv.src = img;
}
function getBorderColor(val) {
    domPreviewImg.setAttribute('cke_qr_borderColor', val.value);
    domPreviewImg.style["borderColor"] = val.value;
    domPreviewImgAdv.style["borderColor"] = val.value;
}
function getborderWidth(val) {
    var w = val.value + "px";
    var defCellSize = domPreviewImg.getAttribute('cke_qr_defCellSize');
    domPreviewImg.setAttribute('cke_qr_borderWidth', val.value);
    domPreviewImg.style["borderWidth"] = w;
    domPreviewImgAdv.style["borderWidth"] = w;
}
function getborderSpace(val) {
    myWidgetData['QrMargin'] = val.value;
    var img = getQrImage(myWidgetData);
    domPreviewImg.src = img;
    domPreviewImgAdv.src = img;
}
function qrStrChanged(elt, msg) {
    var qrStr = elt.value.trim();
    if (qrStr == "") {
        alert(msg);
        return false;
    } else {
        myWidgetData['QrUrl'] = elt.value;
        var img = getQrImage(myWidgetData);
        domPreviewImg.src = img;
        domPreviewImgAdv.src = img;
    }
}
function getHtmlPreview(id, label) {
    var out = "";
    out += '<div style="width: 70%; border-radius: 10px; text-align: center;margin: auto;border: 2px solid gray;">';
    out += '<p style="font-weight: bold;text-align: center;">' + label + '</p>';
    out += '<img id="previewImg_' + id + '" src="" alt="QR Code" style="width: 50%; border:5px solid black">';
    out += '</div>';
    return out;
}
function getHtmlBorder(label, name, min, max) {
    var idValue = name + "_vId";
    var html = '<form style="text-align: center;">';
    html += '<input type="range" style="vertical-align: middle; width: 50%; " name="' + name + '" min="' + min + '" max="' + max + '" ';
    html += 'onchange="get' + name + '(this);" oninput="document.getElementById(\'' + idValue + '\').innerText = this.value;" value="5">';
    html += '<span style="font-weight: bold; margin-left: 15px;">' + label + '</span>';
    html += '<span id="' + idValue + '" style="font-weight: bold; margin-left: 15px; color: tomato;">VALUE</span>';
    html += '<span style="font-weight: bold; color: tomato;"> Px</span>';
    html += '</form>';
    return html;
}
function getHtmlColor(functionName, label) {
    var html = '<form style="text-align: center;">';
    html += '<input type="color" style=" width: 50px; height: 20px; border:1px black solid;  vertical-align: middle;" onchange="';
    html += functionName + '" name="colorBg" value="#000000"><span style="font-weight: bold; margin-left: 15px;">' + label + '</span>';
    html += '</form>';
    return html;
}
function getHtmlCorrectionLevel(label, low, medium, quality, high) {
    var html = '<div style="border: 1px solid red; padding:5px; border-radius: 10px;">';
    html += '<p style="text-align: center; font-weight: bold;color: tomato;">' + label + '</p>';
    html += '<form style="text-align: center;">';
    html += '<input type="radio" name="cor_level" value="L" onclick="handleCLClick(this);">&nbsp;&nbsp;' + low + '&nbsp;&nbsp;';
    html += '<input type="radio" name="cor_level" value="M" onclick="handleCLClick(this);">&nbsp;&nbsp;' + medium + '&nbsp;&nbsp;';
    html += '<input type="radio" name="cor_level" value="Q" onclick="handleCLClick(this);">&nbsp;&nbsp;' + quality + '&nbsp;&nbsp;';
    html += '<input type="radio" name="cor_level" value="H" onclick="handleCLClick(this);">&nbsp;&nbsp;' + high + '&nbsp;&nbsp;';
    html += '</form><div>';
    return html;
}
function getQrStrHtml(label, alertMsg) {
    var html = label + '<br>';
    html += '<input class="cke_dialog_ui_input_text" id="cke_qstring" type="text" value=""';
    html += 'name="QR String" onchange="qrStrChanged(this,\'' + alertMsg + '\')">';
    return html;
}
function setSelectedValue(d, checked) {
    var form = d.getElement('tab-adv', 'correctionLevel').getElementsByTag('form').$[0];
    //    var form = d.getElement('tab-adv', 'correctionLevel').getFirst();
    var elts = form.elements.namedItem('cor_level');
    for (var i = 0; i < elts.length; i++) {
        if (elts[i].value == checked) {
            elts[i].checked = true;
            break;
        }
    }
}
function getSelectedValue(d) {
    var form = d.getElement('tab-adv', 'correctionLevel').getElementsByTag('form').$[0];
    var elts = form.elements.namedItem('cor_level');
    for (var i = 0; i < elts.length; i++) {
        if (elts[i].checked) {
            return elts[i].value;
        }
    }
}
function handleCLClick(myRadio) {
    myWidgetData['QrCorLevel'] = myRadio.value;
    var img = getQrImage(myWidgetData);
    domPreviewImg.src = img;
    domPreviewImgAdv.src = img;
}
// Our dialog definition.
CKEDITOR.dialog.add('yaqr', function(editor) {
    return {
        // Basic properties of the dialog window: title, minimum size.
        title: editor.lang.yaqr.qrProp,
        minWidth: 400,
        minHeight: 200,
        // Dialog window content definition.
        contents: [{
                // Definition of the Basic Settings dialog tab (page).
                id: 'tab-basic',
                label: editor.lang.yaqr.basicSettings,
                // The tab content.
                elements: [{
                        type: 'html',
                        id: 'preview',
                        html: getHtmlPreview('ref', editor.lang.yaqr.preview),
                        // Called by the main setupContent method call on dialog initialization.
                        setup: function(widget) {
                            myWidgetData = Object.assign({}, widget.data);
                            var dialog = this.getDialog();
                            var previewImage = dialog.getContentElement('tab-basic', 'preview');
                            previewImg_ref = previewImage.domId;
                            var domPreview = document.getElementById(previewImg_ref);
                            domPreviewImg = domPreview.getElementsByTagName('img')[0];
                            domPreviewImg.src = getQrImage(myWidgetData);
                        },
                        // Called by the main commitContent method call on dialog confirmation.
                        commit: function(widget) {
                        }
                    },
                    {
                        type: 'html',
                        id: 'qrString',
                        label: 'qrString',
                        html: getQrStrHtml(editor.lang.yaqr.qrString, editor.lang.yaqr.qrString + ' ' + editor.lang.yaqr.badQRmsg),
                        // Called by the main setupContent method call on dialog initialization.
                        setup: function(widget) {
                            document.getElementById('cke_qstring').value = widget.data['QrUrl'];
                        },
                        // Called by the main commitContent method call on dialog confirmation.
                        commit: function(widget) {
                            widget.setData('QrUrl', document.getElementById('cke_qstring').value);
                        }
                    },
                    {
                    	type : 'checkbox',
                    	id : 'target',
                    	label : editor.lang.yaqr.targetText,
                    	'default' : 'checked',
//                    	onClick : function() {
//                    		// this = CKEDITOR.ui.dialog.checkbox
//                    		alert( 'Checked: ' + this.getValue() );
//                    	},
	                    setup: function(widget) {
	                    	this.setValue(widget.data['QrUrlTarget'] === "_blank");
	                    },
	                    // Called by the main commitContent method call on dialog confirmation.
	                    commit: function(widget) {
	                    	var target = "_self";
	                        if (this.getValue()) {
	                        	target = "_blank";
	                        }
	                        widget.setData('QrUrlTarget', target);
	                    }
                    },
                    //                    {
                    //                        type: 'button',
                    //                        id: 'buttonId',
                    //                        label: 'Reset QR to Local Page URL',
                    //                        title: 'My title',
                    //                        onClick: function() {
                    //                            // this = CKEDITOR.ui.dialog.button
                    //                            //alert( 'Clicked: ' + this.id );
                    //                            var qrStr = window.location.href;
                    //                            var elt = document.getElementById('cke_qstring');
                    //                            elt.value = qrStr;
                    //                            qrStrChanged(elt);
                    //                            //element.setAttribute('alt',  document.getElementById('cke_qstring').value );
                    //                        }
                    //                    },
                    {
                        type: 'text',
                        id: 'qrSize',
                        label: editor.lang.yaqr.qrSizeLabel,
                        // Validation checking whether the field is not empty.
                        validate: CKEDITOR.dialog.validate.notEmpty(editor.lang.yaqr.qrSizeLabel + " " + editor.lang.yaqr.badQRmsg),
                        // Called by the main setupContent method call on dialog initialization.
                        setup: function(widget) {
                            this.setValue(clean_css_value(widget.data['QRSize']));
                        },
                        // Called by the main commitContent method call on dialog confirmation.
                        commit: function(widget) {
                            var size = clean_css_value(this.getValue());
                            widget.setData('QRSize', size);
                        }
                    }
                ]
            },
            // Definition of the Advanced Settings dialog tab (page).
            {
                id: 'tab-adv',
                label: editor.lang.yaqr.advSettings,
                elements: [{
                        type: 'html',
                        id: 'preview',
                        html: getHtmlPreview('adv', editor.lang.yaqr.preview),
                        // Called by the main setupContent method call on dialog initialization.
                        setup: function(widget) {
                            var dialog = this.getDialog();
                            var previewImageAdv = dialog.getContentElement('tab-adv', 'preview');
                            previewImg_adv = previewImageAdv.domId;
                            var domPreviewAdv = document.getElementById(previewImg_adv);
                            domPreviewImgAdv = domPreviewAdv.getElementsByTagName('img')[0];
                            domPreviewImgAdv.src = getQrImage(myWidgetData);
                        },
                        // Called by the main commitContent method call on dialog confirmation.
                        commit: function(widget) {
                        }
                    },
                    {
                        type: 'vbox',
                        title: editor.lang.yaqr.setQrTitle,
                        //                    align: 'center',
                        style: 'border: 1px solid tomato; border-radius: 10px; text-align: center;',
                        //                    padding: '5px',
                        children: [{
                                type: 'html',
                                id: 'borderQR',
                                style: 'text-align: center; font-weight: bold;color: tomato;',
                                html: '<p>' + editor.lang.yaqr.qrPropColor + '</p>',
                                // Called by the main setupContent method call on dialog initialization.
                            },
                            {
                                type: 'html',
                                id: 'colorBg',
                                label: 'colorBg',
                                html: getHtmlColor("getColorBgVal(this)", editor.lang.yaqr.backColor),
                                style: 'text-align: center;',
                                // Called by the main setupContent method call on dialog initialization.
                                setup: function(widget) {
                                    var input = this.getElement('tab-adv', 'colorBg').getElementsByTag('input').$[0];
                                    input.value = (widget.data['QrColorBg']);
                                },
                                // Called by the main commitContent method call on dialog confirmation.
                                commit: function(widget) {
                                    var value = this.getElement('tab-adv', 'colorBg').getElementsByTag('input').$[0].value;
                                    widget.setData('QrColorBg', value);
                                }
                            },
                            {
                                type: 'html',
                                id: 'colorPt',
                                label: 'colorPt',
                                html: getHtmlColor("getColorPtVal(this)", editor.lang.yaqr.ptColor),
                                // Called by the main setupContent method call on dialog initialization.
                                setup: function(widget) {
                                    var input = this.getElement('tab-adv', 'colorPt').getElementsByTag('input').$[0];
                                    input.value = (widget.data['QrColorPt']);
                                },
                                // Called by the main commitContent method call on dialog confirmation.
                                commit: function(widget) {
                                    var value = this.getElement('tab-adv', 'colorPt').getElementsByTag('input').$[0].value;
                                    widget.setData('QrColorPt', value);
                                }
                            },
                        ],
                    },
                    {
                        type: 'vbox',
                        title: editor.lang.yaqr.setBPTitle,
                        //                        align: 'center',
                        style: 'border: 1px solid tomato; border-radius: 10px; text-align: center;',
                        //                        padding: '5px',
                        children: [{
                                type: 'html',
                                id: 'borderTitle',
                                style: 'text-align: center; font-weight: bold;color: tomato;',
                                html: '<p>' + editor.lang.yaqr.borderProp + '</p>',
                                // Called by the main setupContent method call on dialog initialization.
                            },
                            {
                                type: 'html',
                                id: 'borderColor',
                                label: 'borderColor',
                                html: '<p><input type="color" style=" width: 50px; height: 20px; border:1px black solid;  vertical-align: middle;" onchange="getBorderColor(this)" name="borderColor" value="#000000"><span style="font-weight: bold; margin-left: 15px;">' + editor.lang.yaqr.borderColor + '</span></p>',
                                style: 'text-align: center;',
                                // Called by the main setupContent method call on dialog initialization.
                                setup: function(widget) {
                                    var input = this.getElement('tab-adv', 'borderColor').getElementsByTag('input').$[0];
                                    input.value = (widget.data['QrBorderColor']);
                                    domPreviewImg.style['borderColor'] = widget.data['QrBorderColor'];
                                    domPreviewImgAdv.style['borderColor'] = widget.data['QrBorderColor'];
                                },
                                // Called by the main commitContent method call on dialog confirmation.
                                commit: function(widget) {
                                    var value = this.getElement('tab-adv', 'borderColor').getElementsByTag('input').$[0].value;
                                    widget.setData('QrBorderColor', value);
                                }
                            },
                            {
                                type: 'html',
                                id: 'borderWidth',
                                label: 'borderWidth',
                                html: getHtmlBorder(editor.lang.yaqr.borderWidth, 'borderWidth', 0, 10),
                                // Called by the main setupContent method call on dialog initialization.
                                setup: function(widget) {
                                    var input = this.getElement('tab-adv', 'borderWidth').getElementsByTag('input').$[0];
                                    input.value = widget.data['QrBorderWidth'];
                                    document.getElementById('borderWidth_vId').innerText = widget.data['QrBorderWidth'];
                                    domPreviewImg.style['borderWidth'] = widget.data['QrBorderWidth'] + "px";
                                    domPreviewImgAdv.style['borderWidth'] = widget.data['QrBorderWidth'] + "px";
                                },
                                // Called by the main commitContent method call on dialog confirmation.
                                commit: function(widget) {
                                    var value = this.getElement('tab-adv', 'borderWidth').getElementsByTag('input').$[0].value;
                                    widget.setData('QrBorderWidth', value);
                                }
                            },
                            {
                                type: 'html',
                                id: 'borderSpace',
                                label: 'borderSpace',
                                html: getHtmlBorder(editor.lang.yaqr.borderSpace, 'borderSpace', 0, 100),
                                // Called by the main setupContent method call on dialog initialization.
                                setup: function(widget) {
                                    var input = this.getElement('tab-adv', 'borderSpace').getElementsByTag('input').$[0];
                                    input.value = widget.data['QrMargin'];
                                    document.getElementById('borderSpace_vId').innerText = widget.data['QrMargin'];
                                },
                                // Called by the main commitContent method call on dialog confirmation.
                                commit: function(widget) {
                                    var value = this.getElement('tab-adv', 'borderSpace').getElementsByTag('input').$[0].value;
                                    widget.setData('QrMargin', value);
                                }
                            }
                        ],
                    },
                    {
                        // errorCorrectionLevel 'L','M','Q','H'
                        type: 'html',
                        id: 'correctionLevel',
                        title: editor.lang.yaqr.setECLTitle,
                        label: 'correctionLevel',
                        html: getHtmlCorrectionLevel(editor.lang.yaqr.corLevel, editor.lang.yaqr.low, editor.lang.yaqr.medium, editor.lang.yaqr.quality, editor.lang.yaqr.high),
                        // Called by the main setupContent method call on dialog initialization.
                        setup: function(widget) {
                            console.log("setup tab-adv correctionLevel ", widget.data['QrCorLevel']);
                            //                            this.setValue( widget.data['QrCorLevel'] );
                            setSelectedValue(this, widget.data['QrCorLevel']);
                        },
                        // Called by the main commitContent method call on dialog confirmation.
                        commit: function(widget) {
                            var form = this.getElement('tab-adv', 'correctionLevel').getElementsByTag('form').$[0];
                            //                            var form = d.getElement('tab-adv', 'correctionLevel').getFirst();
                            var value = getSelectedValue(this);;
                            console.log("commit tab-adv correctionLevel ", value);
                            //                            var form = d.getElement('tab-adv', 'correctionLevel').getFirst();
                            //                            var value = form.$.elements.namedItem('cor_level').value;
                            widget.setData('QrCorLevel', value);
                        }
                    }
                ]
            }
        ],
    };
});