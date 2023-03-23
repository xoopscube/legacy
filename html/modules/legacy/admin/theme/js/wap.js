/*
	XOOPSCube Theme : XCL Admin Flex Grid
	Distribution : XOOPSCube XCL 2.3.1
	Version : 2.3.1
	Author : Nuno Luciano aka Gigamaster
	Date : 2023-01-27
	URL : https://github.com/xoopscube/
*/

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
 * <img class="svg" src="..." width="24px" height="24px" alt="...">
 */
$('.svg').renderClassSvg();

    $(function () {

        sideNavControl();
        mobileNavControl();

        /* Do something on document ready
         * Inline SVG icons with the class "svg" ex.:
         * <img class="svg" src="..." width="24px" height="24px" alt="...">
         */
        //$('.svg').renderClassSvg();

        // Sidebar - Theme Options
        // .theme-options .theme-options-close
        // $(".right-side-toggle").click(function() {
        $(".theme-options").click(function () {
            $(".right-sidebar").slideDown(50), $(".right-sidebar").toggleClass("right-panel-show");
        });

        // Alert Notify e.g. install and mainfile
        $(".alert-close").on("click", function () {
            $(this).parent("div").fadeOut();
        });

        // Select Multiple options without keyboard
        // CSS ui-form.css
        $('select[multiple] option').mousedown(function(e) {
            e.preventDefault();
            var originalScrollTop = $(this).parent().scrollTop();
            // console.log(originalScrollTop);
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

        // Visual notice for all ui-card-block
        // Change the background color and notify admin
        $(function(){
            $("body").on("change","input,select", function() {
                $(this).parent().closest(".ui-card-block").addClass('ui-update-change');
                $('div.alert-submit').addClass("alert-view");
            });
        });

        $('input[name^=delete]').on('change', function () {
            $('div.alert-submit').addClass("alert-view");
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
