/*
	XOOPSCube Theme : XCL Admin Flex Grid
	Distribution : XOOPSCube XCL 2.3
	Version : 1.0.0
	Author : Nuno Luciano aka Gigamaster
	Date : 2021-10-28
	URL : https://github.com/xoopscube/
*/

    $(function () {

        sideNavControl();
        mobileNavControl();

        /* Do something on document ready
         * Inline SVG icons with the class "svg" ex.:
         * <img class="svg" src="..." width="24px" height="24px" alt="...">
         */
        $('.svg').renderClassSvg();

        // Sidebar - Theme Options
        // .theme-options .theme-options-close
        // $(".right-side-toggle").click(function() {
        $(".theme-options").click(function () {
            $(".right-sidebar").slideDown(50), $(".right-sidebar").toggleClass("right-panel-show");
        });

        // Select Multiple
        // without keyboard
        // CSS ui-form.css
        $('select[multiple] option').mousedown(function(e) {
            e.preventDefault();
            var originalScrollTop = $(this).parent().scrollTop();
            // console.log(originalScrollTop);
            $(this).prop('selected', !$(this).prop('selected'));
            var self = this;
            $(this).parent().focus();
            $(this).parent().closest(".ui-card-block").addClass('ui-update-change');
            $('div.foot-sticky').addClass("sticky-view");
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
        $("#taboard div").click(function(){
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
        $('#tab3').load(rest +'modules/legacy/admin/index.php?action=Help&dirname=legacy #help-overview');


        // Module is not available with this distribution !
        var target = $("a.set-link[href*='#no'] ")
        target.addClass('not-available')


        $("a").click(function () {
            $("a").removeClass("selected");
            $(this).addClass("selected");
        });

        // Visual notice for all ui-card-block
        // Change the background color and notify
        $(function(){
            $("body").on("change","input,select", function() {
                $(this).parent().closest(".ui-card-block").addClass('ui-update-change');
                $('div.foot-sticky').addClass("sticky-view");
            });
        });

        $('input[name^=delete]').on('change', function () {
            $('div.foot-sticky').addClass("sticky-view");
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
