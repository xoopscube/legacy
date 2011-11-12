function xoopsGetElementById(id){
    if (document.getElementById) {
        return (document.getElementById(id));
    } else if (document.all) {
        return (document.all[id]);
    } else {
        if ((navigator.appname.indexOf("Netscape") != -1) && parseInt(navigator.appversion == 4)) {
            return (document.layers[id]);
        }
    }
}

function xoopsSetElementProp(name, prop, val) {
    var elt=xoopsGetElementById(name);
    if (elt) elt[prop]=val;
}

function xoopsSetElementStyle(name, prop, val) {
    var elt=xoopsGetElementById(name);
    if (elt && elt.style) elt.style[prop]=val;
}

function xoopsGetFormElement(fname, ctlname) {
    var frm=document.forms[fname];
    return frm?frm.elements[ctlname]:null;
}

function justReturn() {
    return;
}

function openWithSelfMain(url, name, width, height, returnwindow) {
    var options = "width=" + width + ",height=" + height + "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no";
    new_window = window.open(url, name, options);
    window.self.name = "main";
    new_window.document.clear();
    new_window.focus();
    if (returnwindow != null) {
        return new_window;
    }
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
function xoopsCheckAll(formname, switchid) {
    var ele = document.forms[formname].elements;
    var switch_cbox = xoopsGetElementById(switchid);
    for (var i = 0; i < ele.length; i++) {
        var e = ele[i];
        if ( (e.name != switch_cbox.name) && (e.type == 'checkbox') ) {
            e.checked = switch_cbox.checked;
        }
    }
}

function xoopsCheckGroup(formname, switchid, groupid) {
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

function xoopsCheckAllElements(elementIds, switchId) {
    var switch_cbox = xoopsGetElementById(switchId);
    for (var i = 0; i < elementIds.length; i++) {
        var e = xoopsGetElementById(elementIds[i]);
        if ((e.name != switch_cbox.name) && (e.type == 'checkbox')) {
            e.checked = switch_cbox.checked;
        }
    }
}

function xoopsSavePosition(id)
{
    var textareaDom = xoopsGetElementById(id);
    if (textareaDom.createTextRange) {
        textareaDom.caretPos = document.selection.createRange().duplicate();
    }
}

function xoopsInsertText(domobj, text)
{
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

function xoopsCodeSmilie(id, smilieCode) {
    var revisedMessage;
    var textareaDom = xoopsGetElementById(id);
    xoopsInsertText(textareaDom, smilieCode);
    textareaDom.focus();
    return;
}

function showImgSelected(imgId, selectId, imgDir, extra, xoopsUrl) {
    if (xoopsUrl == null) {
        xoopsUrl = "./";
    }
    imgDom = xoopsGetElementById(imgId);
    selectDom = xoopsGetElementById(selectId);
    imgDom.src = xoopsUrl + "/"+ imgDir + "/" + selectDom.options[selectDom.selectedIndex].value + extra;
}

function showThemeScreenshot(imgId, selectId, xoopsUrl) {
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

function xoopsValidate(subjectId, textareaId, submitId, plzCompletePhrase, msgTooLongPhrase, allowedCharPhrase, currCharPhrase) {
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
