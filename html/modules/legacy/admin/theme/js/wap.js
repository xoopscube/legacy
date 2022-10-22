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
         * Inline SVG icons ex.:
         * <img class="svg" src="..." width="24px" height="24px" alt="...">
         */
        $('.svg').renderClassSvg();

        // Sidebar - Theme Options
        // .theme-options .theme-options-toggle
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
            console.log(originalScrollTop);
            $(this).prop('selected', !$(this).prop('selected'));
            var self = this;
            $(this).parent().focus();
            setTimeout(function() {
                $(self).parent().scrollTop(originalScrollTop);
            }, 0);

            return false;
        });


        $("a").click(function () {
            $("a").removeClass("selected");
            $(this).addClass("selected");
        });
        // !TODO merge block and module switch
        $('input[name^=uninstall]').on('change', function () {
            $(this).parent('.ui-checkbox').next('a').toggleClass('ui-update-change');
            // Switch only the svg background !
            // $(this).closest(".ui-card-block").find('.ui-card-block-image').toggleClass('ui-update-change');
            // Switch color of elements: <a> (affects border bottom), svg icon and text !
            $(this).closest(".ui-card-block").find('.ui-block-type').toggleClass('ui-update-change');
            // alert('Clik <{$smarty.const._AD_LEGACY_LANG_UPDATE}> to apply changes!');
        });
        // !TODO merge block and module switch
        // Module Management State Switch
        $('input[name^=isactive]').on('change', function () {
            $(this).parent('.ui-checkbox').next('a').toggleClass('ui-update-change');
            $(this).closest(".ui-card-block").find('.ui-card-block-image,.ui-module-state').toggleClass('ui-update-change');
            // alert('Clik <{$smarty.const._AD_LEGACY_LANG_UPDATE}> to apply changes!');
        });



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
