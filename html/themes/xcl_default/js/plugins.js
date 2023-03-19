/*
 * XCL Theme PicoCSS 157
 *
 * @version   1.5.7
 * @date      2023-03-20
 * @author    Nuno Luciano ( https://github.com/gigamaster )
 * @copyright (c) 2005-2023 The XOOPSCube Project, authors
 * @license   MIT
 * @link      https://github.com/xoopscube-themes/xcl-picocss-157
 */
/**
 * 1. Theme Switcher
 * - jQuery Document Ready - helper plugins
 * 2. Render SVG
 * 3. Notification Script Time
 * 4. Dropdown - block options, menu, etc
 * 5. Close on click document
 * 6. Highlight Message nav-tab active
 * 6.1 remove border
 * 6.2 url constructor
 * 7. Preload
 */

// 1. Minimal theme switcher, Pico.css - https://picocss.com - Copyright 2019-2022 - Licensed under MIT
const themeSwitcher = {
    // Config
    _scheme: "auto",
    menuTarget: "details[role='list']",
    buttonsTarget: "a[data-theme-switcher]",
    buttonAttribute: "data-theme-switcher",
    rootAttribute: "data-theme",
    localStorageKey: "picoPreferedColorScheme",
    // Init
    init() {
        this.scheme = this.schemeFromLocalStorage;
        this.initSwitchers();
    },
    // Get color scheme from local storage
    get schemeFromLocalStorage() {
        if (typeof window.localStorage !== "undefined") {
            if (window.localStorage.getItem(this.localStorageKey) !== null) {
                return window.localStorage.getItem(this.localStorageKey);
            }
        }
        return this._scheme;
    },
    // Prefered color scheme
    get preferedColorScheme() {
        return window.matchMedia("(prefers-color-scheme: dark)").matches
            ? "dark"
            : "light";
    },
    // Init switchers
    initSwitchers() {
        const buttons = document.querySelectorAll(this.buttonsTarget);
        buttons.forEach((button) => {
            button.addEventListener("click", event => {
                event.preventDefault();
                // Set scheme
                this.scheme = button.getAttribute(this.buttonAttribute);
                // Close dropdown
                document.querySelector(this.menuTarget).removeAttribute("open");
            }, false);
        });
    },
    // Set scheme
    set scheme(scheme) {
        if (scheme == "auto") {
            this.preferedColorScheme == "dark"
                ? (this._scheme = "dark")
                : (this._scheme = "light");
        } else if (scheme == "dark" || scheme == "light") {
            this._scheme = scheme;
        }
        this.applyScheme();
        this.schemeToLocalStorage();
    },
    // Get scheme
    get scheme() {
        return this._scheme;
    },
    // Apply scheme
    applyScheme() {
        document
            .querySelector("html")
            .setAttribute(this.rootAttribute, this.scheme);
    },
    // Store scheme to local storage
    schemeToLocalStorage() {
        if (typeof window.localStorage !== "undefined") {
            window.localStorage.setItem(this.localStorageKey, this.scheme);
        }
    },
};
// Init
themeSwitcher.init();

// DOCUMENT READY - Place any jQuery/helper plugins below !
/* ---------- ---------- ---------- ---------- ---------- */
// Do something on document ready
$(function () {

    // 2. Inline SVG icons
    $('.svg').renderClassSvg();

    // 3. Notification Script Time
    $('div.runtime').fadeIn( 750 ).delay( 3000 ).fadeOut( 500 );

    // 4. Dropdown - block options, menu, etc
    $(".dropdown").on("click", ".dropdown-toggle", function (event) {
        event.preventDefault();
        $('.dropdown').removeClass('isopen');
        $(this).parent().toggleClass('isopen');
    });

    // 5. Close on click document
    $(document).on("click", function (event) {
        var $trigger = $(".dropdown");
        if ($trigger !== event.target && !$trigger.has(event.target).length) {
            $(".dropdown").removeClass("isopen");
        }
    });

    // 6. Highlight Message nav-tab active
    $("#tabs-mail").tabs({
            active: false,
            collapsible: true,
            classes: {
                "ui-tabs": "taborder"
            },
            beforeActivate: function (event, ui) {
                window.open($(ui.newTab).find('a').attr('href'), '_self');
                return false;
            },

        }
    ).find('.ui-tabs-tab').removeClass('ui-corner-all ui-corner-top');
    // 6.1 Remove border from dropdown UL children
    $(".ui-tabs-tab .dropdown-content ul").children().css( "border", "0" );

    // 6.2 url constructor
    const parseUrl = new URL(window.location.href);
    const msgAction = parseUrl.searchParams.get("action");
    //console.log(parseUrl.searchParams.get("action"));
    //console.log('the action of this url is:', msgAction);
    // If module message index, highlight the first nav-tab
    if (msgAction == null) {
        // $('#tabs-mail ul:first-child li').addClass('ui-state-hover ');
        $('#tabs-mail ul.ui-tabs-nav li:first-child').addClass('ui-state-active');
    } else {
        // Highlight current action nav-tab
        //$('a[href^="index.php?action=' + msgAction + '"]').parent('li').addClass('mail-tab-active');
        $('a[href="index.php?action=' + msgAction + '"]').parent('li').addClass('ui-state-active');
        // console.log('the tab url is :' + )
    }
    if (msgAction == 'settings'|| msgAction == 'blacklist'){
        // Highlight dropdown menu
        $('.dropdown').parent('li').addClass("ui-state-active");
    }
    // 7. preload
    // $(window).on('load', function() {
    //     $("div.preload").removeClass("preload");
    // });

});
