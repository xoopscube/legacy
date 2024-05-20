/*
 * XCL Theme Green built with PicoCSS v206
 *
 * @version   2.4.0
 * @update    2024-04-20
 * @Date      2024-02-02
 * @author    Nuno Luciano ( https://github.com/gigamaster )
 * @copyright (c) 2005-2024 The XOOPSCube Project, authors
 * @license   MIT
 * @link      https://github.com/xoopscube
 *
 *
 * 1. Theme Switcher
 * 2. Modal
 * -- jQuery Document Ready - helper plugins
 * 3. Render SVG
 * 4. Notification Time
 * 5. Dropdown - block options, menu, etc
 * 6. Close on click document
 * 7. Highlight Message nav-tab active
 * 8.1 remove border
 * 8.2 url constructor
 * 9. Aria-busy Toogle
 */

/*
 * 1. Minimal theme switcher Pico.css - https://picocss.com
 * Copyright 2019-2024 - Licensed under MIT
 */
const themeSwitcher = {
  // Config
  _scheme: "auto",
  menuTarget: "details.dropdown",
  buttonsTarget: "a[data-theme-switcher]",
  buttonAttribute: "data-theme-switcher",
  rootAttribute: "data-theme",
  localStorageKey: "picoPreferredColorScheme",

  // Init
  init() {
    this.scheme = this.schemeFromLocalStorage;
    this.initSwitchers();
  },

  // Get color scheme from local storage
  get schemeFromLocalStorage() {
    return window.localStorage?.getItem(this.localStorageKey) ?? this._scheme;
  },

  // Preferred color scheme
  get preferredColorScheme() {
    return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
  },

  // Init switchers
  initSwitchers() {
    const buttons = document.querySelectorAll(this.buttonsTarget);
    buttons.forEach((button) => {
      button.addEventListener(
        "click",
        (event) => {
          event.preventDefault();
          // Set scheme
          this.scheme = button.getAttribute(this.buttonAttribute);
          // Close dropdown
          document.querySelector(this.menuTarget)?.removeAttribute("open");
        },
        false
      );
    });
  },

  // Set scheme
  set scheme(scheme) {
    if (scheme == "auto") {
      this._scheme = this.preferredColorScheme;
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
    document.querySelector("html")?.setAttribute(this.rootAttribute, this.scheme);
  },

  // Store scheme to local storage
  schemeToLocalStorage() {
    window.localStorage?.setItem(this.localStorageKey, this.scheme);
  },
};

// Init
themeSwitcher.init();

/*
 * 2. Modal Pico.css - https://picocss.com
 * Copyright 2019-2024 - Licensed under MIT
 */
// Config
const isOpenClass = "modal-is-open";
const openingClass = "modal-is-opening";
const closingClass = "modal-is-closing";
const scrollbarWidthCssVar = "--pico-scrollbar-width";
const animationDuration = 400; // ms
let visibleModal = null;

// Toggle modal
const toggleModal = (event) => {
  event.preventDefault();
  const modal = document.getElementById(event.currentTarget.dataset.target);
  if (!modal) return;
  modal && (modal.open ? closeModal(modal) : openModal(modal));
};

// Open modal
const openModal = (modal) => {
  const { documentElement: html } = document;
  const scrollbarWidth = getScrollbarWidth();
  if (scrollbarWidth) {
    html.style.setProperty(scrollbarWidthCssVar, `${scrollbarWidth}px`);
  }
  html.classList.add(isOpenClass, openingClass);
  setTimeout(() => {
    visibleModal = modal;
    html.classList.remove(openingClass);
  }, animationDuration);
  modal.showModal();
};

// Close modal
const closeModal = (modal) => {
  visibleModal = null;
  const { documentElement: html } = document;
  html.classList.add(closingClass);
  setTimeout(() => {
    html.classList.remove(closingClass, isOpenClass);
    html.style.removeProperty(scrollbarWidthCssVar);
    modal.close();
  }, animationDuration);
};

// Close with a click outside
document.addEventListener("click", (event) => {
  if (visibleModal === null) return;
  const modalContent = visibleModal.querySelector("article");
  const isClickInside = modalContent.contains(event.target);
  !isClickInside && closeModal(visibleModal);
});

// Close with Esc key
document.addEventListener("keydown", (event) => {
  if (event.key === "Escape" && visibleModal) {
    closeModal(visibleModal);
  }
});

// Get scrollbar width
const getScrollbarWidth = () => {
  const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
  return scrollbarWidth;
};

// Is scrollbar visible
const isScrollbarVisible = () => {
  return document.body.scrollHeight > screen.height;
};


// DOCUMENT READY - Place any jQuery/helper plugins below !
/* ---------- ---------- ---------- ---------- ---------- */
// Do something on document ready
$(function () {

  // 3. Inline SVG icons
  $('.svg').renderClassSvg();

  // 4. Notification Time
  $('div.runtime').fadeIn( 750 ).delay( 3000 ).fadeOut( 500 );

  // 5. Dropdown - block options, menu, etc
  $(".dropdown").on("click", ".dropdown-toggle", function (event) {
      event.preventDefault();
      $('.dropdown').removeClass('isopen');
      $(this).parent().toggleClass('isopen');
  });

  // 6. Close on click document
  $(document).on("click", function (event) {
      var $trigger = $(".dropdown");
      if ($trigger !== event.target && !$trigger.has(event.target).length) {
          $(".dropdown").removeClass("isopen");
      }
  });

  // 7. Highlight Message nav-tab active
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
  );
  // .find('.ui-tabs-tab').removeClass('ui-corner-all ui-corner-top');

  // 8.1 Remove border from dropdown UL children
  $(".ui-tabs-tab .dropdown-content ul").children().css( "border", "0" );

  // 8.2 url constructor
  const parseUrl = new URL(window.location.href);
  const msgAction = parseUrl.searchParams.get("action");
  // If module message index, highlight the first nav-tab
  if (msgAction == null) {
      $('#tabs-mail ul.ui-tabs-nav li:first-child').addClass('ui-state-active');
  } else {
      // Highlight current action nav-tab
      $('a[href="index.php?action=' + msgAction + '"]').parent('li').addClass('ui-tabs-active ui-state-active');
  }
  if (msgAction == 'settings'|| msgAction == 'blacklist'){
      // Highlight dropdown menu
      $('.settings ').parent('li').addClass("ui-state-active");
  }
  // 9. Aria-busy Toogle
  $('[aria-busy="false"]').on( "click", function(event) {
    event.stopPropagation();
    event.stopImmediatePropagation();
    $(this).prop("ariaBusy", true);
  });

});
