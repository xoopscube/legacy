/**
 * X-UTILS JavaScript functions
 *
 * Distribution : XOOPSCube XCL 2.3.x
 * Version : 2.3.2
 * Author : Nuno Luciano aka Gigamaster
 * Date : 2023-03-20
 * URL : https://github.com/xoopscube/
 *
 * Summary
 *
 * —————> 1. Helper Functions
 *
 * - Render inline SVG images
 * - elcopy (admin generated code)
 * - slideToggle ( e.g. accordion. options )
 * - Toggle ( e.g. side panel )
 * - openWithSelfMain ( e.g. XelFinder )
 * - xPrintag (e.g. message print/pdf )
 *
 * —————> 2. Document Ready
 *
 * - Layout View grid or list
 * - Dropdown Menu
 * - Context Menu
 * - Scroll Smooth
 * - Scroll to Top (ui-nav-top)
 * - Alert user of Unsaved Changes
 *
 * —————> 3. XOOPS + Script Loader
 * - XOOPS.js functions (e.g. bbcode editor )
 * - xScriptLoader
*/

/* —————> 1. Place any JavaScript Helper function below
        to can call from any template, or call a function method
        eg.: functionName.call(obj); */

/* Render inline SVG images
    <img class="svg" ...> */
const cache = {};

(function renderVector($) {
    $.fn.renderClassSvg = function fnRenderClassSvg() {
        this.each(classSvg);
        return this;
    };
    function classSvg() {
        const $img = $(this);
        const src = $img.attr('src');
        // fill cache by src with promise
        if (!cache[src]) {
            const d = $.Deferred();
            $.get(src, (data) => {
                d.resolve($(data).find('svg'));
            });
            cache[src] = d.promise();
        }
        // replace img with svg when cached promise resolves
        cache[src].then((svg) => {
            const $svg = $(svg).clone();
            if ($img.attr('id')) $svg.attr('id', $img.attr('id'));
            if ($img.attr('class')) $svg.attr('class', $img.attr('class'));
            if ($img.attr('style')) $svg.attr('style', $img.attr('style'));
            if ($img.attr('width')) {
                $svg.attr('width', $img.attr('width'));
                if (!$img.attr('height')) $svg.removeAttr('height');
            }
            if ($img.attr('height')) {
                $svg.attr('height', $img.attr('height'));
                if (!$img.attr('width')) $svg.removeAttr('width');
            }
            $svg.attr('role', 'img');
            $svg.insertAfter($img);
            $img.trigger('svgRendered', $svg[0]);
            $img.remove();
        });
    }
}(jQuery));

/**
 * Copy element
 * admin Usage line 190
 */
$.fn.elcopy = function() {
    $('div[class^="alert-notify"]').remove();
    this.select();
    $(this).focus();
    document.execCommand("copy");
    document.getSelection().removeAllRanges();
    $(this).after('<div class="alert-notify"><div class="success">Copied to clipboard</div></div>').fadeIn( 500 );
    $('div.alert-notify').delay( 3000 ).fadeOut("500", function() {
        $(this).remove();
    });
};
// Copy source to clipboard
$('.clipboard').click(function(){
    $(this).parents('.textarea-wrap').find('textarea').elcopy();
});

/* Reusable Toggle
    - How To Use
    Call it from any clickable element with the className to display:
    1) HTML element (eg. input) with onclick="slideToggle('.className', this)"
        <input class="switch" type="checkbox" onclick="slideToggle('.className', this)">
    2) Target HTML element with the "className" and style="display:none"
        <div class="className" style="display:none"></div>
    3) Customize : ( time, "effect", )
        slideToggle(500,"easeInOutCubic" */
function slideToggle(className, obj) {
    $(className).slideToggle(500,"easeInOutCubic", obj.checked );
}

/* Function Toggle Class */
function toggleClass(el, className) {
    if (el.hasClass(className)) {
        el.removeClass(className);
    } else {
        el.addClass(className);
    }
}

/*
    Usage Example:
    openWithSelfMain('https://github.com/xoopscube','XOOPSCube','900','500');
    Location = null is useless because modern browsers now prevent, by default, hiding the address bar for security reasons (phishing)
*/
function openWithSelfMain(url, title, w, h) {
    // Fixes dual-screen position                         Most browsers      Firefox
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : window.screenX;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : window.screenY;

    width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, title,
        'scrollbars=yes, ' +
        'width=' + w + ', ' +
        'height=' + h + ', ' +
        'top=' + top + ', ' +
        'left=' + left + ',' +
        'titlebar=no,toolbar=no,directories=no,status=no,menubar=no,resizable=yes,copyhistory=no');

    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.focus();
    }
}

/*
    Print any HTML element using a link to an id e.g.:
    <a class="print-friendly" href="#" onclick="xPrintag('printhis');">
    then add the id="printhis" e.g.:
    <div class="module-content" id="printhis">...</div>
 */
function xPrintag(tagid) {
    var hashid = "#"+ tagid;
    var tagname =  $(hashid).prop("tagName").toLowerCase() ;
    var attributes = "";
    var attrs = document.getElementById(tagid).attributes;
    $.each(attrs,function(i,elem){
        attributes +=  " "+  elem.name+" ='"+elem.value+"' " ;
    })
    var tagToPrint= $(hashid).html() ;
    var head = "<html><head>"+ $("head").html() + "</head>" ;
    var allcontent = head + "<body  onload='window.print()' >"+ "<" + tagname + attributes + ">" +  tagToPrint + "</" + tagname + ">" +  "</body></html>"  ;
    var newWin=window.open('','Print-Window');
    newWin.document.open();
    newWin.document.write(allcontent);
    newWin.document.close();
    // setTimeout(function(){newWin.close();},10);
}

/* —————> 2. Document Ready
            Place any instance in document ready function bellow */

$(function () {

    /* Layout View List or Grid
        - How To Use
        1) Add two buttons or icons
            <button id="list">
            <button id="grid">
        2) You can specify any number of selectors to combine into a single result.
            https://api.jquery.com/multiple-selector/
            separated by a comma e.g.:
            $('.view, section.itemNameView, article#itemNameView');
    */

    // Grid
    const $content = $('.view');
    //const $view = localStorage.getItem("view") || "";
    const $view = localStorage.getItem("view");
    const $grid = 'grid';
    const $list = 'list';

    if ($view !== null) {
        $content.addClass($view);
    } else {
        $content.addClass($grid); //default view grid
        localStorage.setItem("view", $grid)
    }
    // if trigger IS needed
    // if (view === $list) {
    //     $('#list').trigger("click");
    // }

    $('#grid').click(function () {
        $content.removeClass($list);
        $content.addClass($grid);
        localStorage.setItem("view", $grid); // "clear" choice
    });

    $('#list').click(function () {
        $content.removeClass(grid);
        $content.addClass($list);
        localStorage.setItem("view", $list); // save choice
    });

    /*
        Dropdown Menu
         - How to use
         1) Add an element with class="ui-dropdown"
         2) Add a link with class="ui-dropdown-toggle"
         3) Add an element with class="ui-dropdown-content"
            e.g.:
         <div class="dropdown">
         <a href="#" class="dropdown-toggle"> Menu</a>
         <div class="dropdown-content">
         <a href="#">Link</a>
         </div>
         </div>
    */
    $(".dropdown").on('click', '.dropdown-toggle', function (event) {
        event.preventDefault();
        $('.dropdown').removeClass('isopen');
        $(this).parent().toggleClass('isopen');
    });

    // Close on click document
    $(document).on("click", function (event) {
        var $trigger = $(".dropdown");
        if ($trigger !== event.target && !$trigger.has(event.target).length) {
            $(".dropdown").removeClass("isopen");
        }
    });

    /**
     * Scroll To Top
     * - How To Use
     * 1. Add a div with id=ui-scroll-top
     * 2. Customize CSS
     */
    var btop = $('#ui-scroll-top');

    $(window).scroll(function () {
        if ($(window).scrollTop() > 300) {
            btop.addClass('show');
        } else {
            btop.removeClass('show');
        }
    });
    btop.on('click', function (e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, 300);
    });

    /**
     * Arrow Container Toggle
     */
    $("#arrow_container").click(function (event) {
        event.preventDefault();
        if ($(this).hasClass("isDown")) {
            $("#nav-admin").stop().animate({marginTop: "-100px"}, 200);
        } else {
            $("#nav-admin").stop().animate({marginTop: "0px"}, 200);
        }
        $(this).toggleClass("isDown");
        return false;
    });

    /**
     *  Alert Unsaved Changes
     *
     * Alert user of unsaved changes to form before unload !
     */
    var unsaved = false;

    // Alert to bind the event before unload page
    /* $(window).bind('beforeunload', function() {
        if(unsaved){
        // Most modern browsers (Chrome 51+, Safari 9.1+ etc) ignore custom messages and only display a generic message
        return "You have unsaved changes on this page. Do you want to <{$smarty.const._AD_LEGACY_LANG_UPDATE}> or discard your changes and leave this page?";
        }
    }); */

    // Triggers change in all input fields including text type
    $(document).on('change', ':input', function(){
        unsaved = true;
    });

    // Submit button exception
    $('input[type="submit"]').click(function() {
        unsaved = false;
    });

    function unloadPage(){
        // Most modern browsers (Chrome 51+, Safari 9.1+ etc) ignore custom messages and only display a generic message
        if(unsaved){
            return "You have unsaved changes on this page. Do you want to <{$smarty.const._AD_LEGACY_LANG_UPDATE}> or discard your changes and leave this page?";
        }
    }
    window.onbeforeunload = unloadPage;
});
//--- document ready function

function xoopsGetElementById(id){
    if (document.getElementById) {
        return (document.getElementById(id));
    }
}

function xoopsSetElementProp(name, prop, val) {
    var elt=xoopsGetElementById(name);
    if (elt) elt[prop]=val;
}

function xoopsSetElementStyle(name, prop, val){
    var elt=xoopsGetElementById(name);
    if (elt && elt.style) elt.style[prop]=val;
}

function xoopsGetFormElement(fname, ctlname){
    var frm=document.forms[fname];
    return frm?frm.elements[ctlname]:null;
}

function justReturn(){
    return;
}

function setElementColor(id, color){
    xoopsGetElementById(id).style.color = "#" + color;
}

function setElementFont(id, font){
    xoopsGetElementById(id).style.fontFamily = font;
}

function setElementSize(id, size){
    xoopsGetElementById(id).style.fontSize = size;
}

function changeDisplay(id){
    var elestyle = xoopsGetElementById(id).style;
    if (elestyle.display == "") {
        elestyle.display = "none";
    } else {
        elestyle.display = "block";
    }
}

function setVisible(id){
    xoopsGetElementById(id).style.visibility = "visible";
}

function setHidden(id){
    xoopsGetElementById(id).style.visibility = "hidden";
}

function makeBold(id){
    var eleStyle = xoopsGetElementById(id).style;
    if (eleStyle.fontWeight != "bold" && eleStyle.fontWeight != "700") {
        eleStyle.fontWeight = "bold";
    } else {
        eleStyle.fontWeight = "normal";
    }
}

function makeItalic(id){
    var eleStyle = xoopsGetElementById(id).style;
    if (eleStyle.fontStyle != "italic") {
        eleStyle.fontStyle = "italic";
    } else {
        eleStyle.fontStyle = "normal";
    }
}

function makeUnderline(id){
    var eleStyle = xoopsGetElementById(id).style;
    if (eleStyle.textDecoration != "underline") {
        eleStyle.textDecoration = "underline";
    } else {
        eleStyle.textDecoration = "none";
    }
}

function makeLineThrough(id){
    var eleStyle = xoopsGetElementById(id).style;
    if (eleStyle.textDecoration != "line-through") {
        eleStyle.textDecoration = "line-through";
    } else {
        eleStyle.textDecoration = "none";
    }
}

function appendSelectOption(selectMenuId, optionName, optionValue){
    var selectMenu = xoopsGetElementById(selectMenuId);
    var newoption = new Option(optionName, optionValue);
    selectMenu.options[selectMenu.length] = newoption;
    selectMenu.options[selectMenu.length].selected = true;
}

function disableElement(target){
    var targetDom = xoopsGetElementById(target);
    if (targetDom.disabled != true) {
        targetDom.disabled = true;
    } else {
        targetDom.disabled = false;
    }
}

function xoopsCheckAll(formname, switchid){
    var ele = document.forms[formname].elements;
    var switch_cbox = xoopsGetElementById(switchid);
    for (var i = 0; i < ele.length; i++) {
        var e = ele[i];
        if ( (e.name != switch_cbox.name) && (e.type == 'checkbox') ) {
            e.checked = switch_cbox.checked;
        }
    }
}

function xoopsCheckGroup(formname, switchid, groupid){
    var ele = document.forms[formname].elements;
    var switch_cbox = xoopsGetElementById(switchid);
    for (var i = 0; i < ele.length; i++) {
        var e = ele[i];
        if ( (e.type == 'checkbox') && (e.id.substr(0,groupid.length) == groupid) ) {
            e.checked = switch_cbox.checked;
//            e.click(); e.click();  // Click to activate subgroups
                                    // Twice so we don't reverse effect
        }
    }
}

function xoopsCheckAllElements(elementIds, switchId){
    var switch_cbox = xoopsGetElementById(switchId);
    for (var i = 0; i < elementIds.length; i++) {
        var e = xoopsGetElementById(elementIds[i]);
        if ((e.name != switch_cbox.name) && (e.type == 'checkbox')) {
            e.checked = switch_cbox.checked;
        }
    }
}

function xoopsSavePosition(id){
    var textareaDom = xoopsGetElementById(id);
    if (textareaDom.createTextRange) {
        textareaDom.caretPos = document.selection.createRange().duplicate();
    }
}

function xoopsInsertText(domobj, text){
    if (document.selection) { //for IE
        domobj.focus();
        obj = document.selection.createRange();
        obj.text = text;
        obj.select(); // Display caret when text was replaced
    } else if (domobj.setSelectionRange) { // for Fx, Chrome, Opera
        var startPos = domobj.selectionStart;
        var endPos = domobj.selectionEnd;
        var lastPos = startPos + text.length;
        domobj.value = domobj.value.substring(0, startPos) + text
                       + domobj.value.substring(endPos, domobj.value.length);
        domobj.setSelectionRange(lastPos, lastPos); // Move caret to inserted text end
    } else { // for Other Browser
        domobj.value = domobj.value + text;
    }
}

function xoopsCodeSmilie(id, smilieCode){
    var revisedMessage;
    var textareaDom = xoopsGetElementById(id);
    xoopsInsertText(textareaDom, smilieCode);
    textareaDom.focus();
    return;
}

function showImgSelected(imgId, selectId, imgDir, extra, xoopsUrl){
    if (xoopsUrl == null) {
        xoopsUrl = "./";
    }
    imgDom = xoopsGetElementById(imgId);
    selectDom = xoopsGetElementById(selectId);
    imgDom.src = xoopsUrl + "/"+ imgDir + "/" + selectDom.options[selectDom.selectedIndex].value + extra;
}

function showThemeScreenshot(imgId, selectId, xoopsUrl){
    if (xoopsUrl == null) {
        xoopsUrl = "./";
    }
    imgDom = xoopsGetElementById(imgId);
    selectDom = xoopsGetElementById(selectId);
    serctValues = selectDom.options[selectDom.selectedIndex].value.split('!-!');
    imgDom.src = xoopsUrl + "/themes/" + serctValues[0] + "/" + serctValues[1];
}

function xoopsCodeUrl(id, enterUrlPhrase, enterWebsitePhrase){
    if (enterUrlPhrase == null) {
        enterUrlPhrase = "Enter the URL of the link you want to add:";
    }
    var text = prompt(enterUrlPhrase, "");
    var domobj = xoopsGetElementById(id);
    if ( text != null && text != "" ) {
        if (enterWebsitePhrase == null) {
            enterWebsitePhrase = "Enter the web site title:";
        }
        var text2 = prompt(enterWebsitePhrase, "");
        if ( text2 != null ) {
            if ( text2 == "" ) {
                var result = "[url=" + text + "]" + text + "[/url]";
            } else {
                var pos = text2.indexOf(unescape('%00'));
                if(0 < pos){
                    text2 = text2.substr(0,pos);
                }
                var result = "[url=" + text + "]" + text2 + "[/url]";
            }
            xoopsInsertText(domobj, result);
        }
    }
    domobj.focus();
}

function xoopsCodeImg(id, enterImgUrlPhrase, enterImgPosPhrase, imgPosRorLPhrase, errorImgPosPhrase){
    if (enterImgUrlPhrase == null) {
        enterImgUrlPhrase = "Enter the URL of the image you want to add:";
    }
    var text = prompt(enterImgUrlPhrase, "");
    var domobj = xoopsGetElementById(id);
    if ( text != null && text != "" ) {
        if (enterImgPosPhrase == null) {
            enterImgPosPhrase = "Now, enter the position of the image.";
        }
        if (imgPosRorLPhrase == null) {
            imgPosRorLPhrase = "'R' or 'r' for right, 'L' or 'l' for left, or leave it blank.";
        }
        if (errorImgPosPhrase == null) {
            errorImgPosPhrase = "ERROR! Enter the position of the image:";
        }
        var text2 = prompt(enterImgPosPhrase + "\n" + imgPosRorLPhrase, "");
        while ( ( text2 != "" ) && ( text2 != "r" ) && ( text2 != "R" ) && ( text2 != "l" ) && ( text2 != "L" ) && ( text2 != null ) ) {
            text2 = prompt(errorImgPosPhrase + "\n" + imgPosRorLPhrase,"");
        }
        if ( text2 == "l" || text2 == "L" ) {
            text2 = " align=left";
        } else if ( text2 == "r" || text2 == "R" ) {
            text2 = " align=right";
        } else {
            text2 = "";
        }
        var result = "[img" + text2 + "]" + text + "[/img]";
        xoopsInsertText(domobj, result);
    }
    domobj.focus();
}

function xoopsCodeEmail(id, enterEmailPhrase){
    if (enterEmailPhrase == null) {
        enterEmailPhrase = "Enter the email address you want to add:";
    }
    var text = prompt(enterEmailPhrase, "");
    var domobj = xoopsGetElementById(id);
    if ( text != null && text != "" ) {
        var result = "[email]" + text + "[/email]";
        xoopsInsertText(domobj, result);
    }
    domobj.focus();
}

function xoopsCodeQuote(id, enterQuotePhrase){
    if (enterQuotePhrase == null) {
        enterQuotePhrase = "Enter the text that you want to be quoted:";
    }
    var text = prompt(enterQuotePhrase, "");
    var domobj = xoopsGetElementById(id);
    if ( text != null && text != "" ) {
        var pos = text.indexOf(unescape('%00'));
        if(0 < pos){
            text = text.substr(0,pos);
        }
        var result = "[quote]" + text + "[/quote]";
        xoopsInsertText(domobj, result);
    }
    domobj.focus();
}

function xoopsCodeCode(id, enterCodePhrase){
    if (enterCodePhrase == null) {
        enterCodePhrase = "Enter the codes that you want to add.";
    }
    var text = prompt(enterCodePhrase, "");
    var domobj = xoopsGetElementById(id);
    if ( text != null && text != "" ) {
        var result = "[code]" + text + "[/code]";
        xoopsInsertText(domobj, result);
    }
    domobj.focus();
}

function xoopsCodeText(id, hiddentext, enterTextboxPhrase){
    var textareaDom = xoopsGetElementById(id);
    var textDom = xoopsGetElementById(id + "Addtext");
    var fontDom = xoopsGetElementById(id + "Font");
    var colorDom = xoopsGetElementById(id + "Color");
    var sizeDom = xoopsGetElementById(id + "Size");
    var xoopsHiddenTextDomStyle = xoopsGetElementById(hiddentext).style;
    var textDomValue = textDom.value;
    var fontDomValue = fontDom.options[fontDom.options.selectedIndex].value;
    var colorDomValue = colorDom.options[colorDom.options.selectedIndex].value;
    var sizeDomValue = sizeDom.options[sizeDom.options.selectedIndex].value;
    if ( textDomValue == "" ) {
        if (enterTextboxPhrase == null) {
            enterTextboxPhrase = "Please input text into the textbox.";
        }
        alert(enterTextboxPhrase);
        textDom.focus();
    } else {
        if ( fontDomValue != "FONT") {
            textDomValue = "[font=" + fontDomValue + "]" + textDomValue + "[/font]";
            fontDom.options[0].selected = true;
        }
        if ( colorDomValue != "COLOR") {
            textDomValue = "[color=" + colorDomValue + "]" + textDomValue + "[/color]";
            colorDom.options[0].selected = true;
        }
        if ( sizeDomValue != "SIZE") {
            textDomValue = "[size=" + sizeDomValue + "]" + textDomValue + "[/size]";
            sizeDom.options[0].selected = true;
        }
        if (xoopsHiddenTextDomStyle.fontWeight == "bold" || xoopsHiddenTextDomStyle.fontWeight == "700") {
            textDomValue = "[b]" + textDomValue + "[/b]";
            xoopsHiddenTextDomStyle.fontWeight = "normal";
        }
        if (xoopsHiddenTextDomStyle.fontStyle == "italic") {
            textDomValue = "[i]" + textDomValue + "[/i]";
            xoopsHiddenTextDomStyle.fontStyle = "normal";
        }
        if (xoopsHiddenTextDomStyle.textDecoration == "underline") {
            textDomValue = "[u]" + textDomValue + "[/u]";
            xoopsHiddenTextDomStyle.textDecoration = "none";
        }
        if (xoopsHiddenTextDomStyle.textDecoration == "line-through") {
            textDomValue = "[d]" + textDomValue + "[/d]";
            xoopsHiddenTextDomStyle.textDecoration = "none";
        }
        xoopsInsertText(textareaDom, textDomValue);
        textDom.value = "";
        xoopsHiddenTextDomStyle.color = "#000000";
        xoopsHiddenTextDomStyle.fontFamily = "";
        xoopsHiddenTextDomStyle.fontSize = "12px";
        xoopsHiddenTextDomStyle.visibility = "hidden";
        textareaDom.focus();
    }
}

function xoopsValidate(subjectId, textareaId, submitId, plzCompletePhrase, msgTooLongPhrase, allowedCharPhrase, currCharPhrase){
    var maxchars = 65535;
    var subjectDom = xoopsGetElementById(subjectId);
    var textareaDom = xoopsGetElementById(textareaId);
    var submitDom = xoopsGetElementById(submitId);
    if (textareaDom.value == "" || subjectDom.value == "") {
        if (plzCompletePhrase == null) {
            plzCompletePhrase = "Please complete the subject and message fields.";
        }
        alert(plzCompletePhrase);
        return false;
    }
    if (maxchars != 0) {
        if (textareaDom.value.length > maxchars) {
            if (msgTooLongPhrase == null) {
                msgTooLongPhrase = "Your message is too long.";
            }
            if (allowedCharPhrase == null) {
                allowedCharPhrase = "Allowed max chars length: ";
            }
            if (currCharPhrase == null) {
                currCharPhrase = "Current chars length: ";
            }
            alert(msgTooLongPhrase + "\n\n" + allowedCharPhrase + maxchars + "\n" + currCharPhrase + textareaDom.value.length + "");
            textareaDom.focus();
            return false;
        } else {
            submitDom.disabled = true;
            return true;
        }
    } else {
        submitDom.disabled = true;
        return true;
    }
}

/**
    Load CSS and JS from modules templates. Usage :
    var ScriptLoader = new xScriptLoader([
        XOOPS_URL+"/common/js/simplemodal/css/basic.css",
        XOOPS_URL+"/common/js/simplemodal/js/jquery.simplemodal.js",
        XOOPS_URL+"/common/js/simplemodal/js/basic.js",
    ]);
    ScriptLoader.loadFiles();
 */
var xScriptLoader = (function () {
    function xScriptLoader(files)
    {
        var _this = this;
        this.log = function (t)
        {
            console.log("ScriptLoader: " + t);
        };
        this.withNoCache = function (filename)
        {
            if (filename.indexOf("?") === -1)
                filename += "?no_cache=" + new Date().getTime();
            else
                filename += "&no_cache=" + new Date().getTime();
            return filename;
        };
        this.loadStyle = function (filename)
        {
            // HTMLLinkElement
            var link = document.createElement("link");
            link.rel = "stylesheet";
            link.type = "text/css";
            link.href = _this.withNoCache(filename);
            _this.log('Loading style ' + filename);
            link.onload = function ()
            {
                _this.log('Loaded style "' + filename + '".');
            };
            link.onerror = function ()
            {
                _this.log('Error loading style "' + filename + '".');
            };
            _this.m_head.appendChild(link);
        };
        this.loadScript = function (i)
        {
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = _this.withNoCache(_this.m_js_files[i]);
            var loadNextScript = function ()
            {
                if (i + 1 < _this.m_js_files.length)
                {
                    _this.loadScript(i + 1);
                }
            };
            script.onload = function ()
            {
                _this.log('Loaded script "' + _this.m_js_files[i] + '".');
                loadNextScript();
            };
            script.onerror = function ()
            {
                _this.log('Error loading script "' + _this.m_js_files[i] + '".');
                loadNextScript();
            };
            _this.log('Loading script "' + _this.m_js_files[i] + '".');
            _this.m_head.appendChild(script);
        };
        this.loadFiles = function ()
        {
            // this.log(this.m_css_files);
            // this.log(this.m_js_files);
            for (var i = 0; i < _this.m_css_files.length; ++i)
                _this.loadStyle(_this.m_css_files[i]);
            _this.loadScript(0);
        };
        this.m_js_files = [];
        this.m_css_files = [];
        this.m_head = document.getElementsByTagName("head")[0];
        // this.m_head = document.head; // IE9+ only
        function endsWith(str, suffix)
        {
            if (str === null || suffix === null)
                return false;
            return str.indexOf(suffix, str.length - suffix.length) !== -1;
        }
        for (var i = 0; i < files.length; ++i)
        {
            if (endsWith(files[i], ".css"))
            {
                this.m_css_files.push(files[i]);
            }
            else if (endsWith(files[i], ".js"))
            {
                this.m_js_files.push(files[i]);
            }
            else
                this.log('Error unknown filetype "' + files[i] + '".');
        }
    }
    return xScriptLoader;
})
(); /* xScriptLoader */
