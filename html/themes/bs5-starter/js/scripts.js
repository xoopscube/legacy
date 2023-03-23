/*
 * Theme XCL Bootstrap 5 Starter
 *
 * @version   5.3.0
 * @author    Nuno Luciano ( https://github.com/gigamaster )
 * @copyright (c) 2023 The XOOPSCube Project, author
 * @license   MIT license for Bootstrap
 * @license   BSD license for XOOPSCube XCL Theme
 * @link      https://github.com/xoopscube
*/

// Use this file to add JavaScript to your project

// Browser support dark-mode
if (window.matchMedia('(prefers-color-scheme)').media !== 'not all') {
    console.log('🎉 Dark mode is supported');
}

/*
 * Theme mode switch saving user preference to localstorage
 * 1 - The document.documentElement property gives you the html element
 * Note that XCL Bootstrap5.3 starter refers to <html>
 */

// 1 - Theme mode set in html element ( data-theme also used by admin )
let theme = localStorage.getItem("data-bs-theme");

// Navbar
const checkbox = document.getElementById("theme-mode");
// Panel
const inputheme = document.getElementById("theme-select");

const changeThemeToDark = () =>{
    document.documentElement.setAttribute("data-bs-theme", "dark")
    localStorage.setItem("data-bs-theme", "dark")
    console.log("Render dark mode theme !")
}

const changeThemeToLight = () =>{
    document.documentElement.setAttribute("data-bs-theme", "light")
    localStorage.setItem("data-bs-theme", "light")
    console.log("Render light mode theme !")
}

if(theme === "dark"){
    changeThemeToDark()
}
/*
 * Note : All elements (const) of array [ checkbox, inputheme ]
 * must exist in the DOM !
 */
[ checkbox ].forEach(function(element) {
    element.addEventListener("input", function() {
        let theme = localStorage.getItem("data-bs-theme");
        if (theme === "dark"){
            changeThemeToLight()
        }else{
            changeThemeToDark()
        }
    });
});

/**
 * TOASTER
 * Refer to theme guide for settings
 */
window.onload = (event)=> {
    Array.from(document.querySelectorAll('.toast')).forEach(toastNode => new bootstrap.Toast(toastNode).show());
    const progressTime = document.getElementsByClassName('progressTime');
    const timeInvisible = document.getElementById("progressInvisible");
    if (progressTime.length) {
        var timealert = 100;
        var displayTimer = setInterval(function () {
            if (timealert <= 0) {
                clearInterval(displayTimer);
            }
            progressTime[0].value = 100 - timealert;

            if (timeInvisible) {
                timeInvisible.value = 100 - timealert;
            }
            timealert -= 1;
        }, 50); // smooth progress animation
    }
};


/**
 * TOOLTIPS
 * Initialize tooltips before they can be used
 */
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));


/**
 * FORM VALIDATION
 * Example starter JavaScript for disabling form submissions if there are invalid fields
 */
(() => {
    'use strict'
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')
    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()


/**
 * OFF-CANVAS PANEL
 * Refer to theme guide for settings
 */
const offcanvasElementList = document.querySelectorAll('.offcanvas')
const offcanvasList = [...offcanvasElementList].map(offcanvasEl => new bootstrap.Offcanvas(offcanvasEl))

/*
 * Enable popovers
 * 1. You must initialize popovers before they can be used.
 * 2. One way to initialize all popovers on a page would be to select them by their data-bs-toggle attribute, like so
 */
const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))

function toggleModal() {
    getModal().show()
}
/*
 * INLINE SVG
 * The img src must have default values e.g.  class="svg", width="1em" alt=""
 * Credit J.Haynes
 */
!function(a,b)
{"function"==typeof define&&define.amd?define([],b(a)):"object"==typeof exports?module.exports=b(a):a.inlineSVG=b(a)}
("undefined"!=typeof global?global:this.window||this.global,function(a){
var b,c={},d=!!document.querySelector&&!!a.addEventListener,e={initClass:"inlineSVG",svgSelector:"img.svg"},
f=function(a,b){return function(){if(--a<1)return b.apply(this,arguments)}},
g=function(){var a={},b=!1,c=0,d=arguments.length;
"[object Boolean]"===Object.prototype.toString.call(arguments[0])&&(b=arguments[0],c++);
for(var e=function(c){for(var d in c)
Object.prototype.hasOwnProperty.call(c,d)&&(b&&"[object Object]"===Object.prototype.toString.call(c[d])?a[d]=g(!0,a[d],c[d]):a[d]=c[d])};
c<d;c++){e(arguments[c])}return a},h=function(){return document.querySelectorAll(b.svgSelector)},
i=function(){return"_"+Math.random().toString(36).substr(2,9)},
j=function(a){var c=h(),d=f(c.length,a);Array.prototype.forEach.call(c,function(a,c){
var e=a.src||a.getAttribute("data-src"),f=a.attributes,g=new XMLHttpRequest;
g.open("GET",e,!0),g.onload=function(){
if(g.status>=200&&g.status<400){
var c=new DOMParser,e=c.parseFromString(g.responseText,"text/xml"),h=e.getElementsByTagName("svg")[0];
if(h.removeAttribute("xmlns:a"),h.removeAttribute("width"),
h.removeAttribute("height"),h.removeAttribute("x"),
h.removeAttribute("y"),h.removeAttribute("enable-background"),
h.removeAttribute("xmlns:xlink"),h.removeAttribute("xml:space"),
h.removeAttribute("version"),
Array.prototype.slice.call(f).forEach(function(a){
"src"!==a.name&&"alt"!==a.name&&"longdesc"!==a.name&&h.setAttribute(a.name,a.value)}),
h.classList?h.classList.add("i-svg"):h.setAttribute("class",h.getAttribute("class")+" i-svg"),
h.setAttribute("role","img"),f.alt){var j=document.createElementNS("http://www.w3.org/2000/svg","title"),
k=document.createTextNode(f.alt.value);if(j.setAttribute("id",i()),j.appendChild(k),
h.insertBefore(j,h.firstChild),f.id)h.setAttribute("aria-labelledby",f.id.value);
else if(!f.id){var l=function(){if(h.getElementsByTagName("title").length>0){
return h.getElementsByTagName("title")[0].getAttribute("id")}return""};
h.setAttribute("aria-labelledby",l())}}
if(f.alt||(h.setAttribute("aria-hidden","true"),
h.setAttribute("role","presentation")),f.longdesc){
var m=document.createElementNS("http://www.w3.org/2000/svg","desc"),n=document.createTextNode(f.longdesc.value);
m.setAttribute("id",i()),m.appendChild(n),f.alt?h.insertBefore(m,h.firstChild.nextSibling):h.insertBefore(m,h.firstChild);
var o=function(){if(h.getElementsByTagName("desc").length>0){
return h.getElementsByTagName("desc")[0].getAttribute("id")}return""};
if(f.alt){var p=h.getAttribute("aria-labelledby");
h.setAttribute("aria-labelledby",p+=" "+o())}
else h.setAttribute("aria-labelledby",o())}a.parentNode&&a.parentNode.replaceChild(h,a),d&&d(b.svgSelector)}
else console.error("There was an error retrieving the source of the SVG.")},
g.onerror=function(){
console.error("There was an error connecting to the origin server.")},
g.send()})};
return c.init=function(a,c){d&&(b=g(e,a||{}),j(c||function(){}),
document.documentElement.className+=" "+b.initClass)},c});

/*
 * Enable Prismjs
 * Code block syntax highlight
 */

//Prism.highlightAll();
/*
    Usage Example:
    openWithSelfMain('https://github.com/xoopscube','XOOPSCube','900','500');
    Location = null is useless because modern browsers now prevent, by default, hiding the address bar for security reasons (phishing)
*/

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
