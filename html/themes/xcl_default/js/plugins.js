$(function () {

    // Do something on document ready
    // Inline SVG icons ex.:
    // <img class="svg" src="..." width="24px" height="24px" alt="...">
    $('.svg').renderClassSvg();

    $('div.runtime').fadeIn( 750 ).delay( 3000 ).fadeOut( 500 );
/* ---------------------------------------*/

// Place any jQuery/helper plugins in here.

    // Highlight the active Message nav-tab
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
    // Remove border from dropdown UL children
    $(".ui-tabs-tab .dropdown-content ul").children().css( "border", "0" );

    // url constructor
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

/* ---------------------------------------*/
});
