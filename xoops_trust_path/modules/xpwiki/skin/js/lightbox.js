// -----------------------------------------------------------------------------------
//
//	Lightbox v2.02
//	by Lokesh Dhakar - http://www.huddletogether.com
//	3/31/06
//
//	For more information on this script, visit:
//	http://huddletogether.com/projects/lightbox2/
//
//	Licensed under the Creative Commons Attribution 2.5 License - http://creativecommons.org/licenses/by/2.5/
//
//	Credit also due to those who have helped, inspired, and made their code available to the public.
//	Including: Scott Upton(uptonic.com), Peter-Paul Koch(quirksmode.org), Thomas Fuchs(mir.aculo.us), and others.
//
//
// -----------------------------------------------------------------------------------
//
//  edited by nao-pon - http://hypweb.net/
//  $Id: lightbox.js,v 1.19 2012/01/03 04:49:34 nao-pon Exp $
//
// -----------------------------------------------------------------------------------

if (!Lightbox) {

//
//	Configuration
//

var resizeSpeed = 9;	// controls the speed of the image resizing (1=slowest and 10=fastest)

var borderSize = 10;	//if you adjust the padding in the CSS, you will need to update this variable

var lightbox_timeout = 30000; // Timeout for load a image.(ms)
// -----------------------------------------------------------------------------------

//
//	Global Variables
//
var imageArray = new Array;
var activeImage;

if(resizeSpeed > 10){ resizeSpeed = 10;}
if(resizeSpeed < 1){ resizeSpeed = 1;}
resizeDuration = (11 - resizeSpeed) * 0.15;

// -----------------------------------------------------------------------------------

//
//	Additional methods for Element added by SU, Couloir
//	- further additions by Lokesh Dhakar (huddletogether.com)
//
Object.extend(Element, {
	getWidth: function(element) {
	   	element = $(element);
	   	return element.offsetWidth;
	},
	setWidth: function(element,w) {
	   	element = $(element);
    	element.style.width = w +"px";
	},
	setHeight: function(element,h) {
   		element = $(element);
    	element.style.height = h +"px";
	},
	setTop: function(element,t) {
	   	element = $(element);
    	element.style.top = t +"px";
	},
	setSrc: function(element,src) {
    	element = $(element);
    	element.src = src;
	},
	setHref: function(element,href) {
    	element = $(element);
    	element.href = href;
	},
	setInnerHTML: function(element,content) {
		element = $(element);
		element.innerHTML = content;
	}
});

// -----------------------------------------------------------------------------------

//
//	Extending built-in Array object
//	- array.removeDuplicates()
//	- array.empty()
//
Array.prototype.removeDuplicates = function () {
	for(i = 1; i < this.length; i++){
		if(this[i][0] == this[i-1][0]){
			this.splice(i,1);
		}
	}
}

// -----------------------------------------------------------------------------------

Array.prototype.empty = function () {
	for(i = 0; i <= this.length; i++){
		this.shift();
	}
}

// -----------------------------------------------------------------------------------

var Lightbox = Class.create();

Lightbox.prototype = {

	initialize: function() {
		if (!document.getElementsByTagName){ return; }

		this.timer = null;
		this.imgPreloader = null;
		this.myAjax = null;

		//regexp my host name
		this.myhost = location.protocol+"//"+location.host;
		var reg = /([\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:\\])/g;
		//for NN4.x bug
		this.myhost = this.myhost.replace(reg, "##__BACK_SLASH__##$1").replace(/##__BACK_SLASH__##/g, '\\');
		this.myhost = new RegExp("^"+this.myhost,"i");

		var objBody = document.getElementsByTagName('body')[0];
		var r = document.evaluate('descendant::a[@type="img"][@href!=""]', objBody, null, 7, null);
		for (var i=0; i<r.snapshotLength; i++){
			var anchor = r.snapshotItem(i);
			anchor.setAttribute("rel", "lightbox[stack]");
			anchor.onclick = function () {myLightbox.start(this); return false;}
		}

		if ($('lightbox')) {
			Element.remove($('lightbox'));
		}

		var objOverlay = document.createElement("div");
		objOverlay.setAttribute('id','overlay');
		objOverlay.style.display = 'none';
		objOverlay.onclick = function() { myLightbox.end(); return false; }
		objBody.appendChild(objOverlay);

		var objLightbox = document.createElement("div");
		objLightbox.setAttribute('id','lightbox');
		objLightbox.style.display = 'none';
		objBody.appendChild(objLightbox);

		var objOuterImageContainer = document.createElement("div");
		objOuterImageContainer.setAttribute('id','outerImageContainer');
		objLightbox.appendChild(objOuterImageContainer);

		var objImageContainer = document.createElement("div");
		objImageContainer.setAttribute('id','imageContainer');
		objOuterImageContainer.appendChild(objImageContainer);

		var objLightboxImage = document.createElement("img");
		objLightboxImage.setAttribute('id','lightboxImage');
		objImageContainer.appendChild(objLightboxImage);

		var objHoverNav = document.createElement("div");
		objHoverNav.setAttribute('id','hoverNav');
		objImageContainer.appendChild(objHoverNav);

		var objPrevLink = document.createElement("a");
		objPrevLink.setAttribute('id','prevLink');
		objPrevLink.setAttribute('href','#');
		objHoverNav.appendChild(objPrevLink);

		var objNextLink = document.createElement("a");
		objNextLink.setAttribute('id','nextLink');
		objNextLink.setAttribute('href','#');
		objHoverNav.appendChild(objNextLink);

		var objLoading = document.createElement("div");
		objLoading.setAttribute('id','loading');
		objImageContainer.appendChild(objLoading);

		var objLoadingLink = document.createElement("a");
		objLoadingLink.setAttribute('id','loadingLink');
		objLoadingLink.setAttribute('href','#');
		objLoadingLink.onclick = function() { myLightbox.end(); return false; }
		objLoading.appendChild(objLoadingLink);

		var objLoadingImage = document.createElement("img");
		objLoadingImage.setAttribute('src', wikihelper_root_url + '/skin/loader.php?src=loading.gif');
		objLoadingLink.appendChild(objLoadingImage);

		var objImageDataContainer = document.createElement("div");
		objImageDataContainer.setAttribute('id','imageDataContainer');
		objImageDataContainer.className = 'clearfix';
		objLightbox.appendChild(objImageDataContainer);

		var objImageData = document.createElement("div");
		objImageData.setAttribute('id','imageData');
		objImageDataContainer.appendChild(objImageData);

		var objBottomNav = document.createElement("div");
		objBottomNav.setAttribute('id','bottomNav');
		objImageData.appendChild(objBottomNav);

		var objBottomNavCloseLink = document.createElement("a");
		objBottomNavCloseLink.setAttribute('id','bottomNavClose');
		objBottomNavCloseLink.setAttribute('href','#');
		objBottomNavCloseLink.onclick = function() { myLightbox.end(); return false; }
		objBottomNavCloseLink.innerHTML = '&#38281;&#12376;&#12427;';
		objBottomNavCloseLink.setAttribute('title',objBottomNavCloseLink.innerHTML);
		objBottomNav.appendChild(objBottomNavCloseLink);

		var objBottomNavOriginalLinkNew = document.createElement("a");
		objBottomNavOriginalLinkNew.setAttribute('id','originalLinkNew');
		objBottomNavOriginalLinkNew.setAttribute('href','#');
		objBottomNavOriginalLinkNew.onclick = function() { myLightbox.viewOriginalNew(); return false; };
		objBottomNavOriginalLinkNew.style.display = 'none';
		objBottomNavOriginalLinkNew.innerHTML = '&#26032;&#12375;&#12356;&#12454;&#12452;&#12531;&#12489;&#12454;&#12391;&#38283;&#12367;';
		objBottomNavOriginalLinkNew.setAttribute('title',objBottomNavOriginalLinkNew.innerHTML);
		objBottomNav.appendChild(objBottomNavOriginalLinkNew);

		var objBottomNavOriginalLink = document.createElement("a");
		objBottomNavOriginalLink.setAttribute('id','originalLink');
		objBottomNavOriginalLink.setAttribute('href','#');
		objBottomNavOriginalLink.onclick = function() { myLightbox.viewOriginal(); return false; };
		objBottomNavOriginalLink.style.display = 'none';
		objBottomNavOriginalLink.innerHTML = '&#21516;&#12376;&#12454;&#12452;&#12531;&#12489;&#12454;&#12391;&#12458;&#12522;&#12472;&#12490;&#12523;&#30011;&#20687;&#12434;&#38283;&#12367;';
		objBottomNavOriginalLink.setAttribute('title',objBottomNavOriginalLink.innerHTML);
		objBottomNav.appendChild(objBottomNavOriginalLink);

		var objImageDetails = document.createElement("div");
		objImageDetails.setAttribute('id','imageDetails');
		objImageData.appendChild(objImageDetails);

		var objCaption = document.createElement("span");
		objCaption.setAttribute('id','caption');
		objImageData.appendChild(objCaption);

		var objNumberDisplay = document.createElement("span");
		objNumberDisplay.setAttribute('id','numberDisplay');
		objImageData.appendChild(objNumberDisplay);

	},

	//
	//	start()
	//	Display overlay and lightbox. If image is part of a set, add siblings to imageArray.
	//
	start: function(imageLink) {

		hideSelectBoxes();

		// stretch overlay to fill page and fade in
		var arrayPageSize = getPageSize();
		Element.setHeight('overlay', arrayPageSize[1]);
		new Effect.Appear('overlay', { duration: 0.2, from: 0.0, to: 0.8 });

		imageArray = [];
		imageNum = 0;

		if (!document.getElementsByTagName){ return; }

		var myrel = imageLink.getAttribute('rel');
		if((myrel == 'lightbox')){
			// add single image to imageArray
			imageArray.push(new Array(imageLink.getAttribute('href'), imageLink.getAttribute('title')));
		} else if (myrel) {
			// if image is part of a set..

			// loop through anchors, find other images in set, and add them to imageArray
			var r = document.evaluate('descendant::a[@rel="'+myrel+'"]', document, null, 7, null);
			for (var i=0; i<r.snapshotLength; i++){
				var anchor = r.snapshotItem(i);
				if (anchor.getAttribute('href')) {
					imageArray.push(new Array(anchor.getAttribute('href'), anchor.getAttribute('title')));
				}
			}

			imageArray.removeDuplicates();
			while(imageArray[imageNum][0] != imageLink.getAttribute('href')) { imageNum++;}
		}

		// calculate top offset for the lightbox and display
		var arrayPageSize = getPageSize();
		var arrayPageScroll = getPageScroll();
		var lightboxTop = arrayPageScroll[1] + (arrayPageSize[3] / 50);

		Element.setTop('lightbox', lightboxTop);
		Element.show('lightbox');

		this.changeImage(imageNum);
	},

	//
	//	changeImage()
	//	Hide most elements and preload image in preparation for resizing image container.
	//
	changeImage: function(imageNum) {

		activeImage = imageNum;	// update global var

		// hide elements during transition
		Element.show('loading');
		Element.hide('lightboxImage');
		Element.hide('hoverNav');
		Element.hide('prevLink');
		Element.hide('nextLink');
		Element.hide('imageDataContainer');
		Element.hide('numberDisplay');

		this.imgPreloader = new Image();

		// once image is preloaded, resize image container
		this.imgPreloader.onload=function(){
			if (this.timer) {clearTimeout(this.timer);this.timer=null;};
			this.myAjax = null;
			Element.setSrc('lightboxImage', this.imgPreloader.src);
			this.resizeImageContainer(this.imgPreloader.width, this.imgPreloader.height);
		}.bind(this);

		this.imgPreloader.onerror=function(){
			if (this.timer) {clearTimeout(this.timer);this.timer=null;};
			this.myAjax = null;
			this.imgPreloader.src = wikihelper_root_url + '/skin/loader.php?src=notfound.gif';
		}.bind(this);

		if (this.timer) {clearTimeout(this.timer);this.timer=null;}
			this.timer = setTimeout(function(){
			this.imgPreloader.src = wikihelper_root_url + '/skin/loader.php?src=timeout.gif';
		}.bind(this),lightbox_timeout);

		// Check URL found or notfound?
		//if (this.imgPreloader.src.match(/^http/i) && !this.imgPreloader.src.match(this.myhost))
		//{
		//	this.checkUrl(this.imgPreloader.src);
		//}

		this.imgPreloader.src = imageArray[activeImage][0];

		// prefetch image
		var prefetchPrev;
		var prefetchNext;

		if (! prefetchPrev) prefetchPrev = new Image();
		if (! prefetchNext) prefetchNext = new Image();

		if (activeImage != 0) {
			prefetchPrev.src = imageArray[activeImage - 1][0];
		}
		if (activeImage != (imageArray.length - 1)) {
			prefetchNext.src = imageArray[activeImage + 1][0];
		}
	},


	// Check URL by ajax
	checkUrl: function(url) {
		var url = './plugin_data/lightbox/checkurl.php?q=' + encodeURIComponent(url);
		this.myAjax = new Ajax.Request(
			url,
			{
				method: 'GET',
				requestHeaders: ['Referer',document.location],
				onComplete: this.onCheckedUrl.bind(this)
			});
	},

	// Checked URL
	onCheckedUrl: function(Req) {
		var rc = eval(Req.responseText);
		if (rc != 200)
		{
			this.imgPreloader.src = wikihelper_root_url + '/skin/loader.php?src=notfound.gif';
		}
	},

	//
	//	resizeImageContainer()
	//
	resizeImageContainer: function( imgWidth, imgHeight ) {

		document.getElementById('bottomNavClose').style.display = 'block';
		document.getElementById('originalLinkNew').style.display = 'none';
		document.getElementById('originalLink').style.display = 'none';
		var boxHeight = imgHeight + 36 + (borderSize * 3);
		var boxWidth = imgWidth + (borderSize * 2);
		var ratio = 0.96;

		if(arrayPageSize[3] <= boxHeight | arrayPageSize[2] <= boxWidth){
			if(arrayPageSize[3] <= boxHeight) {
				imgRatio = imgWidth / imgHeight;
				imgHeight = (arrayPageSize[3] - 36 - (borderSize * 3)) * ratio;
				imgWidth = imgHeight * imgRatio;
				if(arrayPageSize[2] <= boxWidth) {
					imgRatio = imgHeight / imgWidth;
					imgWidth = (arrayPageSize[2] - (borderSize * 2)) * ratio;
					imgHeight = imgWidth * imgRatio;
					if(arrayPageSize[3] <= boxHeight) {
						imgRatio = imgWidth / imgHeight;
						imgHeight = (arrayPageSize[3] - 36 - (borderSize * 3)) * ratio;
						imgWidth = imgHeight * imgRatio;
					}
				}
			} else if(arrayPageSize[2] <= boxWidth) {
				imgRatio = imgHeight / imgWidth;
				imgWidth = (arrayPageSize[2] - (borderSize * 2)) * ratio;
				imgHeight = imgWidth * imgRatio;
				if(arrayPageSize[3] <= boxHeight) {
					imgRatio = imgWidth / imgHeight;
					imgHeight = (arrayPageSize[3] - 36 - (borderSize * 3)) * ratio;
					imgWidth = imgHeight * imgRatio;
					if(arrayPageSize[2] <= boxWidth) {
						imgRatio = imgHeight / imgWidth;
						imgWidth = (arrayPageSize[2] - (borderSize * 2)) * ratio;
						imgHeight = imgWidth * imgRatio;
					}
				}
			}
		document.getElementById('originalLinkNew').style.display = 'block';
		document.getElementById('originalLink').style.display = 'block';
		}


		document.getElementById('lightboxImage').width = imgWidth;
		document.getElementById('lightboxImage').height = imgHeight;

		imageDetails_w = imgWidth - 220;
		if (imageDetails_w < 1) imageDetails_w = imgWidth;
		document.getElementById('imageDetails').style.width = imageDetails_w + 'px';

		// get current height and width
		this.wCur = Element.getWidth('outerImageContainer');
		this.hCur = Element.getHeight('outerImageContainer');

		// scalars based on change from old to new
		this.xScale = ((imgWidth  + (borderSize * 2)) / this.wCur) * 100;
		this.yScale = ((imgHeight  + (borderSize * 2)) / this.hCur) * 100;

		// calculate size difference between new and old image, and resize if necessary
		wDiff = (this.wCur - borderSize * 2) - imgWidth;
		hDiff = (this.hCur - borderSize * 2) - imgHeight;

		if(!( hDiff == 0)){ new Effect.Scale('outerImageContainer', this.yScale, {scaleX: false, duration: resizeDuration, queue: 'front'}); }
		if(!( wDiff == 0)){ new Effect.Scale('outerImageContainer', this.xScale, {scaleY: false, delay: resizeDuration, duration: resizeDuration}); }

		// if new and old image are same size and no scaling transition is necessary,
		// do a quick pause to prevent image flicker.
		if((hDiff == 0) && (wDiff == 0)){
			if (navigator.appVersion.indexOf("MSIE")!=-1){ pause(250); } else { pause(100);}
		}

		Element.setHeight('prevLink', imgHeight);
		Element.setHeight('nextLink', imgHeight);
		Element.setWidth( 'imageDataContainer', imgWidth + (borderSize * 2));

		this.showImage();
	},

	//
	//	showImage()
	//	Display image and begin preloading neighbors.
	//
	showImage: function(){
		Element.hide('loading');
		new Effect.Appear('lightboxImage', { duration: 0.5, queue: 'end', afterFinish: function(){	myLightbox.updateDetails(); } });
		this.preloadNeighborImages();
	},

	//
	//	updateDetails()
	//	Display caption, image number, and bottom nav.
	//
	updateDetails: function() {

		Element.show('caption');
		Element.setInnerHTML( 'caption', imageArray[activeImage][1]);

		// if image is part of set display 'Image x of x'
		if(imageArray.length > 1){
			Element.show('numberDisplay');
			Element.setInnerHTML( 'numberDisplay', "<a href=\""+imageArray[activeImage][0]+"\" title=\"Open this window\">Image " + eval(activeImage + 1) + "</a> of " + imageArray.length);
		}

		new Effect.Parallel(
			[ new Effect.SlideDown( 'imageDataContainer', { sync: true, duration: resizeDuration + 0.25, from: 0.0, to: 1.0 }),
			  new Effect.Appear('imageDataContainer', { sync: true, duration: 1.0 }) ],
			{ duration: 0.65, afterFinish: function() { myLightbox.updateNav();} }
		);
	},

	//
	//	updateNav()
	//	Display appropriate previous and next hover navigation.
	//
	updateNav: function() {

		Element.show('hoverNav');

		// if not first image in set, display prev image button
		if(activeImage != 0){
			Element.show('prevLink');
			document.getElementById('prevLink').onclick = function() {
				myLightbox.changeImage(activeImage - 1); return false;
			}
		}

		// if not last image in set, display next image button
		if(activeImage != (imageArray.length - 1)){
			Element.show('nextLink');
			document.getElementById('nextLink').onclick = function() {
				myLightbox.changeImage(activeImage + 1); return false;
			}
		}

		this.enableKeyboardNav();
	},

	//
	//	enableKeyboardNav()
	//
	enableKeyboardNav: function() {
		document.onkeydown = this.keyboardAction;
	},

	//
	//	disableKeyboardNav()
	//
	disableKeyboardNav: function() {
		document.onkeydown = '';
	},

	//
	//	keyboardAction()
	//
	keyboardAction: function(e) {
		if (e == null) { // ie
			keycode = event.keyCode;
		} else { // mozilla
			keycode = e.which;
		}

		key = String.fromCharCode(keycode).toLowerCase();

		if((key == 'x') || (key == 'o') || (key == 'c')){	// close lightbox
			myLightbox.end();
		} else if(key == 'p'){	// display previous image
			if(activeImage != 0){
				myLightbox.disableKeyboardNav();
				myLightbox.changeImage(activeImage - 1);
			}
		} else if(key == 'n'){	// display next image
			if(activeImage != (imageArray.length - 1)){
				myLightbox.disableKeyboardNav();
				myLightbox.changeImage(activeImage + 1);
			}
		}


	},

	//
	//	preloadNeighborImages()
	//	Preload previous and next images.
	//
	preloadNeighborImages: function(){

		if((imageArray.length - 1) > activeImage){
			preloadNextImage = new Image();
			preloadNextImage.src = imageArray[activeImage + 1][0];
		}
		if(activeImage > 0){
			preloadPrevImage = new Image();
			preloadPrevImage.src = imageArray[activeImage - 1][0];
		}

	},

	//
	//	end()
	//
	end: function() {
		if (this.timer) {clearTimeout(this.timer);this.timer=null;}
		document.getElementById('bottomNavClose').style.display = 'none';
		document.getElementById('originalLinkNew').style.display = 'none';
		document.getElementById('originalLink').style.display = 'none';
		this.disableKeyboardNav();
		Element.hide('lightbox');
		new Effect.Fade('overlay', { duration: 0.2});
		showSelectBoxes();
	},

	viewOriginalNew: function() {
		window.open(document.getElementById('lightboxImage').src);
	},

	viewOriginal: function() {
		window.location.href = document.getElementById('lightboxImage').src;
	}
}

// -----------------------------------------------------------------------------------

//
// getPageScroll()
// Returns array with x,y page scroll values.
// Core code from - quirksmode.org
//
function getPageScroll(){

	var yScroll;

	if (self.pageYOffset) {
		yScroll = self.pageYOffset;
	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
		yScroll = document.documentElement.scrollTop;
	} else if (document.body) {// all other Explorers
		yScroll = document.body.scrollTop;
	}

	arrayPageScroll = new Array('',yScroll)
	return arrayPageScroll;
}

// -----------------------------------------------------------------------------------

//
// getPageSize()
// Returns array with page width, height and window width, height
// Core code from - quirksmode.org
// Edit for Firefox by pHaez
//
function getPageSize(){

	var xScroll, yScroll;

	var documentBody = (document.documentElement || document.body);

	if (window.innerHeight && window.scrollMaxY) {
		xScroll = documentBody.scrollWidth;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (documentBody.scrollHeight > documentBody.offsetHeight){ // all but Explorer Mac
		xScroll = documentBody.scrollWidth;
		yScroll = documentBody.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = documentBody.offsetWidth;
		yScroll = documentBody.offsetHeight;
	}

	var windowWidth, windowHeight;
	if (self.innerHeight) {	// all except Explorer
		windowWidth = self.innerWidth;
		windowHeight = self.innerHeight;
	} else {
		windowWidth = documentBody.clientWidth;
		windowHeight = documentBody.clientHeight;
	}

	// for small pages with total height less then height of the viewport
	if(yScroll < windowHeight){
		pageHeight = windowHeight;
	} else {
		pageHeight = yScroll;
	}

	// for small pages with total width less then width of the viewport
	if(xScroll < windowWidth){
		pageWidth = windowWidth;
	} else {
		pageWidth = xScroll;
	}

	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight)
	return arrayPageSize;
}

// -----------------------------------------------------------------------------------

//
// getKey(key)
// Gets keycode. If 'x' is pressed then it hides the lightbox.
//
function getKey(e){
	if (e == null) { // ie
		keycode = event.keyCode;
	} else { // mozilla
		keycode = e.which;
	}
	key = String.fromCharCode(keycode).toLowerCase();

	if(key == 'x'){
	}
}

// -----------------------------------------------------------------------------------

//
// listenKey()
//
function listenKey () {	document.onkeypress = getKey; }

// ---------------------------------------------------

function showSelectBoxes(){
	selects = document.getElementsByTagName("select");
	for (i = 0; i != selects.length; i++) {
		if (!selects[i].id.match(/^edit_/))  // for fusen plugin by nao-pon
		{
			selects[i].style.visibility = "visible";
		}
	}
}

// ---------------------------------------------------

function hideSelectBoxes(){
	selects = document.getElementsByTagName("select");
	for (i = 0; i != selects.length; i++) {
		if (!selects[i].id.match(/^edit_/))  // for fusen plugin by nao-pon
		{
			selects[i].style.visibility = "hidden";
		}
	}
}

// ---------------------------------------------------

//
// pause(numberMillis)
// Pauses code execution for specified time. Uses busy code, not good.
// Code from http://www.faqts.com/knowledge_base/view.phtml/aid/1602
//
function pause(numberMillis) {
	var now = new Date();
	var exitTime = now.getTime() + numberMillis;
	while (true) {
		now = new Date();
		if (now.getTime() > exitTime)
			return;
	}
}

// ---------------------------------------------------

function initLightbox() { myLightbox = new Lightbox(); }

XpWiki.domInitFunctions.push(initLightbox);

}