/*
	XOOPSCube Theme : XCL Admin Flex Grid
	Distribution : XOOPSCube XCL 2.3.3
	Version : 2.3.3
	Author : Nuno Luciano aka Gigamaster
	Date : 2023-03-27
	URL : https://github.com/xoopscube/
*/

/* Do something on document ready */
    $(function () {

        // const setTheme = theme => document.documentElement.className = theme;
        const setTheme = (theme) => {
            document.documentElement.className = theme;
            localStorage.setItem('theme-select', theme);
        }
        document.getElementById('theme-select').addEventListener('change', function() {
            setTheme(this.value);
        });
        const getTheme = () => {
            const theme = localStorage.getItem('theme-select');
            theme && setTheme(theme);
        }
        getTheme();

        /*
         * Inline SVG icons with the class "svg" ex.:
         * <img class="svg" src="..." width="1em" height="1em" alt="...">
         */
        $('.svg').renderClassSvg();

        sideNavControl();
        mobileNavControl();

        // Alert Notification e.g. install and mainfile.php
        $(".alert-close").on("click", function () {
            $(this).parent("div").fadeOut();
        });

        // Alert Notification on change ui-card-block
        // Visual notice : change the background color
        $(function(){
            $(".ui-card-block").on("change","input,select", function() {
                $(this).parent().closest(".ui-card-block").addClass('ui-update-change');
                $('div.alert-submit').addClass("alert-view");
            });
        });

        $('input[name^=delete]').on('change', function () {
            $('div.alert-submit').addClass("alert-view");
        });

        // Panel Right Sidebar - Panel control
        // Toggle controls .panel-control and .panel-control-close
        $(".panel-control,.panel-control-close").click(function () {
            $(".right-sidebar").slideDown(50);
            $(".right-sidebar").toggleClass("right-panel-show");
        });

        // Select Multiple options without keyboard
        // CSS ref. ui-form.css
        $('select[multiple] option').mousedown(function(e) {
            e.preventDefault();
            var originalScrollTop = $(this).parent().scrollTop();
            $(this).prop('selected', !$(this).prop('selected'));
            var self = this;
            $(this).parent().focus();
            $(this).parent().closest(".ui-card-block").addClass('ui-update-change');
            $('div.alert-submit').addClass("alert-view");
            setTimeout(function() {
                $(self).parent().scrollTop(originalScrollTop);
            }, 0);
            return false;
        });

        // Overview Tabs
        $( "#tabs" ).tabs({
            collapsible: true,
            select: function(event, ui) {
                window.location.hash = ui.tab.hash;
            }
        });
        $("#taboard").tabs({
            // collapsible: true,
            // heightStyle: "fill",
            select: function(event, ui) {
                window.location.hash = ui.tab.hash;
            }
        });
        $("#taboard div").on("click", function() {
            $("#taboard div").removeClass("active");
            $(this).addClass("active");
        });

        // Overview Tabs > Accordion
        $(".accordion").accordion({
            heightStyle: "content",
            collapsible: true,
            animated: 'slide',
            navigation: true,
            active: 1
        });


        // Load overview from the module's help file
        var str = window.location.pathname;
        //console.log(str);
        var rest = str.substring(0, str.lastIndexOf("/") + 1);
        //console.log(rest);
        // Dashboard - Tabs
        $('#tab-start').load(rest +'modules/legacy/admin/index.php?action=Help&dirname=legacy #help-overview');


        $( document ).tooltip({
            //disabled: false,
            position: {
                my: "center bottom-20",
                at: "center top",
                using: function( position, feedback ) {
                    $( this ).css( position );
                    $( "<div>" )
                        // .addClass( "arrow" )
                        .addClass( feedback.vertical )
                        .addClass( feedback.horizontal )
                        .appendTo( this );
                }
            },
            // track: true
        });

        /* Code block Soure */
        Prism.highlightAll();

    });

    /** ---------- Navigation elements
     *
     * Layout Navigation
     * sideNavControl();
     * userDropdownMenu();
     * mobileNavControl();
    */
    const MAIN_LAYOUT = $('.layout-grid');
    const MAIN_SCROLL = 'scrollbar';
    const MOBILE_ACTIVE = '.nav-mobile';
    const SIDE_NAV = $('.nav-aside');
    const SIDE_ACTIVE = 'nav-aside-active';
    const userMenu = $('.nav-user-control');
    const sideNavBlock = $('.nav-block'); // console.log('sideNavBlock: ', sideNavBlock);
    const SIDE_MENU_BLOCK_OPEN = 'nav-block-open';
    const SIDE_MENU_BLOCK_CLOSE = 'nav-block-close';
    // Function Side Nav Control
    function sideNavControl() {
        sideNavBlock.each((i, sideMenuTitleToggle) => {
            $(sideMenuTitleToggle).on('click', (e) => {
                const SIDE_MENU_LINK = $(sideMenuTitleToggle).siblings();
                // Toggle class of side menu title
                if (sideMenuTitleToggle) {
                    toggleClass($(sideMenuTitleToggle), SIDE_MENU_BLOCK_OPEN);
                }
                // Switch view of side menu
                if (SIDE_MENU_LINK && SIDE_MENU_LINK.length === 1) {
                    toggleClass($(SIDE_MENU_LINK), SIDE_MENU_BLOCK_CLOSE);
                }
            });
        });
    }

    // Function toggle nav-aside
    function mobileNavControl() {
        $(MOBILE_ACTIVE).on('click', function (e) {
            toggleClass($(MOBILE_ACTIVE), 'nav-mobile-close');
            toggleClass(SIDE_NAV, SIDE_ACTIVE);
            toggleClass(MAIN_LAYOUT, MAIN_SCROLL);
        });
    }

// ADMIN THEME OPTIONS
const adminblockcontrol = 'input[class*="block-control"]:checkbox';
$(adminblockcontrol).each(function() {
    let blockid =$(this).attr('id');
    let blockname = $(this).attr("name");

    const t = function () {

        const t = $('#'+blockid).is(':checked');

            $('#'+blockname).toggle(t)

            if (blockid == 'block-tips') {
                $('.'+blockname).toggle(t)
            }
            if (blockid == 'block-tooltip') {
                $( document ).tooltip(  "option", "disabled", false ).toggle(t)
                //$( document ).tooltip(  "option", "disabled", false ).toggle(t)
                //$(document).tooltip("disable").tooltip("hide");

                $(document).tooltip().show().toggle(t)
            }

            localStorage.setItem(blockid, t ? "true" : "false"),

            $("#"+blockname).addClass("show", t);

            if (blockid == 'block-tooltip') {
                $( document ).tooltip( "option", "disabled", t);
            }

        // console.log('blockname:', blockname)
    };
    $(function () {
        $("#"+blockid)
            .on("click", t)
            .prop("checked", "true" === localStorage.getItem( blockid )),
            t();
    });
});
const clearLS = document.getElementById("clearLS");
clearLS.onclick = function () {
    localStorage.clear();
    if(localStorage.length === 0)
    $(this).after('<div class="alert-notify"><div class="success">Copied to clipboard !</div></div>').fadeIn( 500 );
    $('div.alert-notify').html( '<div class="success">Clear LocalStorage !</div>' );
    $('div.alert-notify').delay( 3000 ).fadeOut("500", function() {
        $(this).remove();
    });
};
// UI toggle view options
function toggle(className, obj) {
    $(className).toggle(750,"easeOutQuint", obj.checked )
}
