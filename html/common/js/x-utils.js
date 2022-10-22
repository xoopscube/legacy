/**
 * X-UTILS
 *
 * XOOPCube Theme : XCL Admin Flex Grid
 * Distribution : XCL 2.3.1
 * Version : 2.3.1
 * Author : Nuno Luciano aka Gigamaster
 * Date : 2021-05-28
 * URL : https://github.com/xoopscube/xcl/
 *
 * Summary
 *
 * —————> 1. Helper Functions
 *
 * - LocalStorage : Prefers-color-scheme
 * - Render inline SVG images
 * - Reusable slide Toggle
 * - Toggle ClassName
 *
 * —————> 2. Document Ready
 *
 * - Layout View grid or list
 * - Dropdown Menu
 * - Context Menu
 * - Scroll Smooth
 * - Scroll to Top (ui-nav-top)
 * - Alert user of Unsaved Changes
*/

/* —————> 1. Place any Helper JavaScript Helper function above
        to can call from any template, or call a function method
        eg.: functionName.call(obj); */




    /* Render inline SVG images
        <img class="svg" ...> */
    const cache = {};

    (function renderVector($) {
        $.fn.renderClassSvg = function fnRenderClassSvg() {
            this.each(classSvg);
            return this;
        };
        function classSvg() {
            const $img = $(this);
            const src = $img.attr('src');
            // fill cache by src with promise
            if (!cache[src]) {
                const d = $.Deferred();
                $.get(src, (data) => {
                    d.resolve($(data).find('svg'));
                });
                cache[src] = d.promise();
            }
            // replace img with svg when cached promise resolves
            cache[src].then((svg) => {
                const $svg = $(svg).clone();
                if ($img.attr('id')) $svg.attr('id', $img.attr('id'));
                if ($img.attr('class')) $svg.attr('class', $img.attr('class'));
                if ($img.attr('style')) $svg.attr('style', $img.attr('style'));
                if ($img.attr('width')) {
                    $svg.attr('width', $img.attr('width'));
                    if (!$img.attr('height')) $svg.removeAttr('height');
                }
                if ($img.attr('height')) {
                    $svg.attr('height', $img.attr('height'));
                    if (!$img.attr('width')) $svg.removeAttr('width');
                }
                $svg.attr('role', 'img');
                $svg.insertAfter($img);
                $img.trigger('svgRendered', $svg[0]);
                $img.remove();
            });
        }
    }(jQuery));


    /* Reusable Toggle
        - How To Use
        Call it from any clickable element with the className to display:
        1) HTML element (eg. input) with onclick="slideToggle('.className', this)"
            <input class="switch" type="checkbox" onclick="slideToggle('.className', this)">
        2) Target HTML element with the "className" and style="display:none"
            <div class="className" style="display:none"></div>
        3) Customize : ( time, "effect", )
            slideToggle(500,"easeInOutCubic" */
    function slideToggle(className, obj) {
        $(className).slideToggle(500,"easeInOutCubic", obj.checked );
    }


    /* Function Toggle Class */
    function toggleClass(el, className) {
        if (el.hasClass(className)) {
            el.removeClass(className);
        } else {
            el.addClass(className);
        }
    }


/* —————> 2. Document Ready
            Place any instance in document ready function bellow */


$(function () {

    /* Layout View List or Grid
        - How To Use
        1) Add two buttons or icons
            Button icon with id="list"
            Button icon with id="grid"
        2) You can specify any number of selectors to combine into a single result.
            https://api.jquery.com/multiple-selector/
            separated by a comma e.g.:
            $('.view, section.itemNameView, article#itemNameView');

    */
    const $content = $('.view');
    //const $view = localStorage.getItem("view") || "";
    const $view = localStorage.getItem("view");
    const $grid = 'grid';
    const $list = 'list';

    if ($view !== null) {
        $content.addClass($view);
    } else {
        $content.addClass($grid); //default view grid
        localStorage.setItem("view", $grid)
    }
    // if trigger IS needed
    // if (view === $list) {
    //     $('#list').trigger("click");
    // }

    $('#grid').click(function () {
        $content.removeClass($list);
        $content.addClass($grid);
        localStorage.setItem("view", $grid); // "clear" choice
    });

    $('#list').click(function () {
        $content.removeClass(grid);
        $content.addClass($list);
        localStorage.setItem("view", $list); // save choice
    });

    /**
     *  Dropdown Menu
         - How to use
         1) Add an element with class="ui-dropdown"
         2) Add a link with class="ui-dropdown-toggle"
         3) Add an element with class="ui-dropdown-content"
            e.g.:
         <div class="dropdown">
         <a href="#" class="dropdown-toggle"> Menu</a>
         <div class="dropdown-content">
         <a href="#">Link</a>
         </div>
         </div>
    */
    $(".dropdown").on("click", ".dropdown-toggle", function (event) {
        event.preventDefault();
        $('.dropdown').removeClass('isopen');
        $(this).parent().toggleClass('isopen');
    });

    $(document).on("click", function (event) {
        var $trigger = $(".dropdown");
        if ($trigger !== event.target && !$trigger.has(event.target).length) {
            $(".dropdown").removeClass("isopen");
        }
    });





    /**
     * Scroll To Top
     * - How To Use
     * 1. Add a div with id=ui-scroll-top
     * 2. Customize CSS
     */
    var btop = $('#ui-scroll-top');

    $(window).scroll(function () {
        if ($(window).scrollTop() > 300) {
            btop.addClass('show');
        } else {
            btop.removeClass('show');
        }
    });
    btop.on('click', function (e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, '300');
    });

    /**
     * Arrow Container Toggle
     */
    $("#arrow_container").click(function (event) {
        event.preventDefault();
        if ($(this).hasClass("isDown")) {
            $("#nav-admin").stop().animate({marginTop: "-100px"}, 200);
        } else {
            $("#nav-admin").stop().animate({marginTop: "0px"}, 200);
        }
        $(this).toggleClass("isDown");
        return false;
    });


    /**
     *  Alert Unsaved Changes
     *
     * Alert user of unsaved changes to form before unload !
     */
    var unsaved = false;

    // Alert to bind the event before unload page
    /* $(window).bind('beforeunload', function() {
        if(unsaved){
        // Most modern browsers (Chrome 51+, Safari 9.1+ etc) ignore custom messages and only display a generic message
        return "You have unsaved changes on this page. Do you want to <{$smarty.const._AD_LEGACY_LANG_UPDATE}> or discard your changes and leave this page?";
        }
    }); */

    // Triggers change in all input fields including text type
    $(document).on('change', ':input', function(){
        unsaved = true;
    });

    // Submit button exception
    $('input[type="submit"]').click(function() {
        unsaved = false;
    });

    function unloadPage(){
        // Most modern browsers (Chrome 51+, Safari 9.1+ etc) ignore custom messages and only display a generic message
        if(unsaved){
            return "You have unsaved changes on this page. Do you want to <{$smarty.const._AD_LEGACY_LANG_UPDATE}> or discard your changes and leave this page?";
        }

    }

    window.onbeforeunload = unloadPage;


//--- document ready function
});
