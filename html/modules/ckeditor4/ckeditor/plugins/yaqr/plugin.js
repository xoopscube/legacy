/**
 * Licensed under the terms of the MIT License (see LICENSE.md).
 *
 * Basic  plugin inserting QR elements into the CKEditor editing area.
 *
 */
'use strict';
function rgbToHex(a) {
    if (a.startsWith("#")) {
        return a;
    }
    a = a.replace(/[^\d,]/g, "").split(",");
    var rgb = (1 << 24) + (+a[0] << 16) + (+a[1] << 8) + +a[2];
    var res = "#" + rgb.toString(16).slice(1);
    return res;
}
var isHandlingData;
// Register the plugin within the editor.
CKEDITOR.plugins.add("yaqr", {
    requires: "widget",
    lang: "en,fr", // %REMOVE_LINE_CORE%
    // Register the icons.
    icons: "yaqr",
    hidpi: true, // %REMOVE_LINE_CORE%
    // The plugin initialization logic goes inside this method.
    init: function (editor) {
        ////console.log( "init" );
        var lang = editor.lang.yaqr;
        var defaultConfig = {
            // default accepted tags, can be overriden by config file
            defQrUrl: "https://ckeditor.com/",
            defTargetBlank: true,
            defQRSize: "100px",
            defCellSize: 6,
            defMargin: 10,
            colorPt: "#000000",
            colorBg: "#ffffff",
            borderWidth: 2,
            borderColor: "#000000",
            correctionLevel: "Q"
        };
        var config = CKEDITOR.tools.extend(defaultConfig, editor.config.yaqr || {}, true);
        editor.config.defQrUrl = config.defQrUrl;
        editor.config.targetBlank = config.defTargetBlank;
        editor.config.defQRSize = config.defQRSize;
        editor.config.defCellSize = config.defCellSize;
        editor.config.defMargin = config.defMargin;
        editor.config.colorPt = config.colorPt;
        editor.config.colorBg = config.colorBg;
        editor.config.borderWidth = config.borderWidth;
        editor.config.borderColor = config.borderColor;
        editor.config.correctionLevel = config.correctionLevel;
        var targetStr = "_self";
        if (editor.config.targetBlank) {
        	targetStr = "_blank";
        };
        var jsScripts = [];
        //jsScripts.push(CKEDITOR.getUrl(CKEDITOR.plugins.getPath("yaqr") + "3rdParty/qrcode.js"));
        jsScripts.push(CKEDITOR.getUrl(CKEDITOR.plugins.getPath("yaqr") + "3rdParty/qrcode-min.js"));
        CKEDITOR.scriptLoader.queue(jsScripts, function (completed, failed) {
            //alert( 'Number of scripts loaded: ' + completed.length );
            //alert( 'Number of failures: ' + failed.length );
        });
//        CKEDITOR.dialog.add("yaqr", this.path + "dialogs/yaqr.js");
        CKEDITOR.dialog.add("yaqr", this.path + "dialogs/yaqr-min.js");
        // Register the yaqr widget.
        editor.widgets.add("yaqr", {
            allowedContent: "a(!yaqr)[href,data-*]; img[id,data-*,src,alt]{width,border}",
            // Require the yaqr tag to be allowed for the feature to work.
            requiredContent: "a(yaqr)",
            //draggable: false,
            // Define two nested editable areas.
            editables: {
                title: {
                    // Define CSS selector used for finding the element inside widget element.
                    selector: ".simplebox-title",
                    // Define content allowed in this nested editable. Its content will be
                    // filtered accordingly and the toolbar will be adjusted when this editable
                    // is focused.
                    allowedContent: "br strong em"
                }
            },
            // Define the template of a new Simple Box widget.
            // The template will be used when creating new instances of the Simple Box widget.
            template: '<a class="yaqr"  href="' + editor.config.defQrUrl + '" target="'+targetStr+'">' +
                    '<img alt="' + editor.config.defQrUrl + '" style="border:' + editor.config.borderWidth + 'px solid ' + editor.config.borderColor +
                    '; width:' + editor.config.defQRSize + '"' +
                    'data-cke_qr_bg_color="' + editor.config.colorBg + '"' +
                    'data-cke_qr_pt_color="' + editor.config.colorPt + '"' +
                    'data-cke_qr_defcellsize="' + editor.config.defCellSize + '"' +
                    'data-cke_qr_borderspace="' + editor.config.defMargin + '"' +
                    'data-cke_qr_cor_level="' + editor.config.correctionLevel + '"' +
                    'data-cke_qr_image="' + "" + '"' +
                    '/>' +
                    '</a>',
            // Define the label for a widget toolbar button which will be automatically
            // created by the Widgets System. This button will insert a new widget instance
            // created from the template defined above, or will edit selected widget
            // (see second part of this tutorial to learn about editing widgets).
            //
            // Note: In order to be able to translate your widget you should use the
            // editor.lang.yaqr.* property. A string was used directly here to simplify this tutorial.
            button: lang.createQr,
            // Set the widget dialog window name. This enables the automatic widget-dialog binding.
            // This dialog window will be opened when creating a new widget or editing an existing one.
            dialog: "yaqr",
            // Check the elements that need to be converted to widgets.
            //
            // Note: The "element" argument is an instance of http://docs.ckeditor.com/#!/api/CKEDITOR.htmlParser.element
            // so it is not a real DOM element yet. This is caused by the fact that upcasting is performed
            // during data processing which is done on DOM represented by JavaScript objects.
            upcast: function (element) {
                //console.log("UPCAST", element);
                return element.name === "a" && element.hasClass("yaqr");
            },
            // When a widget is being initialized, we need to read the data ("align" and "width")
            // from DOM and set it by using the widget.setData() method.
            // More code which needs to be executed when DOM is available may go here.
            init: function () {
                //console.log("INIT", this.element);
                var el = this.element;
                var target = el.getAttribute('target');
                if (target === null) target = "_blank";
                this.setData('QrUrlTarget', target);
                var qrImg = el.getFirst();
                var attr = qrImg.getAttributes();
                this.setData('QrUrl', attr['alt']);
                this.setData('QRSize', qrImg.getStyle('width'));
                this.setData('QrCellSize', attr['data-cke_qr_defcellsize']);
                this.setData('QrMargin', attr['data-cke_qr_borderspace']);
                this.setData('QrColorPt', attr['data-cke_qr_pt_color']);
                this.setData('QrColorBg', attr['data-cke_qr_bg_color']);
                this.setData('QrBorderWidth', parseInt(qrImg.getStyle('borderWidth'), 10));
                this.setData('QrBorderColor', rgbToHex(qrImg.getStyle('borderColor')));
                this.setData('QrCorLevel', attr['data-cke_qr_cor_level']);
                //qrImg.setAttribute('src', attr['data-cke_qr_image']);
                //this.setData('QrImage', "");
            },
            // Listen on the widget#data event which is fired every time the widget data changes
            // and updates the widget's view.
            // Data may be changed by using the widget.setData() method, which we use in the
            // Simple Box dialog window.
            data: function () {
                //console.log("DATA", this.data["QrUrl"]);
                var el = this.element;
                el.setAttribute('href', this.data["QrUrl"]);
                el.data('cke-saved-href', this.data["QrUrl"]);
                el.setAttribute('target', this.data["QrUrlTarget"]);
                el.data('cke-saved-target', this.data["QrUrlTarget"]);
                var qrImg = el.getFirst();
                //var style= "border:2px solid #000000; height:100px; width:100px";
                qrImg.setAttribute('alt', this.data["QrUrl"]);
                qrImg.setStyle('borderWidth', this.data["QrBorderWidth"] + "px");
                qrImg.setStyle('borderColor', this.data["QrBorderColor"]);
                qrImg.setStyle('width', this.data["QRSize"]);
//                qrImg.setStyle('height', this.data["QRSize"]);
                qrImg.data('cke_qr_bg_color', this.data["QrColorBg"]);
                qrImg.data('cke_qr_pt_color', this.data["QrColorPt"]);
                qrImg.data('cke_qr_defcellsize', this.data["QrCellSize"]);
                qrImg.data('cke_qr_borderspace', this.data["QrMargin"]);
                qrImg.data('cke_qr_cor_level', this.data["QrCorLevel"]);
                if (typeof(getQrImage) == 'function') {
                    var img = getQrImage(this.data);
                    //console.log("SET SRC ", img);
                    qrImg.setAttribute('src', img);
                    //qrImg.data('cke_qr_image', img);
                    qrImg.data('cke-saved-src', img);
                }
            }
        });
    }
});