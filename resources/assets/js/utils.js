/*
 *  Document   : app.js
 *  Author     : pixelcave
 *  Description: Custom scripts and plugin initializations (available to all pages)
 *
 *  Feel free to remove the plugin initilizations from uiInit() if you would like to
 *  use them only in specific pages. Also, if you remove a js plugin you won't use, make
 *  sure to remove its initialization from uiInit().
 */

var App = function() {

    /* Helper variables - set in uiInit() */
    var page, pageContent, header, sidebar, sBrand, sExtraInfo, sidebarAlt, sScroll, sScrollAlt, currentUrl;

    // sidebar.find('a[href="' + CURRENT_URL + '"]').parent('li').addClass('current-page');;

    /* Initialization UI Code */
    var uiInit = function() {

        // Set variables - Cache some often used Jquery objects in variables */
        page            = $('#page-container');
        header          = $('header');
        pageContent     = $('#page-content');

        sidebar         = $('#sidebar');
        sBrand          = $('#sidebar-brand');
        sExtraInfo      = $('#sidebar-extra-info');
        sScroll         = $('#sidebar-scroll');

        sidebarAlt      = $('#sidebar-alt');
        sScrollAlt      = $('#sidebar-scroll-alt');

        currentUrl      = window.location.href.split('?')[0];

        // Initialize sidebars functionality
        handleSidebar('init');

        // Sidebar navigation functionality
        handleNav();

        // Add active link on sidebar
        $('ul.sidebar-nav', sidebar).find('a').filter(function () {
            return this.href == currentUrl;
        }).addClass('active');

        // Header glass effect on scrolling
        if ((header.hasClass('navbar-fixed-top') || header.hasClass('navbar-fixed-bottom'))) {
            $(window).on('scroll', function(){
                if ($(this).scrollTop() > 50) {
                    header.addClass('navbar-glass');
                } else {
                    header.removeClass('navbar-glass');
                }
            });
        }

        // Resize #page-content to fill empty space if exists
        $(window).on('resize orientationchange', function(){ resizePageContent(); }).resize();

        // Intialize ripple effect on buttons
        rippleEffect($('.btn-effect-ripple'), 'btn-ripple');

        // Initialize Tabs
        $('[data-toggle="tabs"] a, .enable-tabs a').click(function(e){ e.preventDefault(); $(this).tab('show'); });

        // Initialize Tooltips
        $('[data-toggle="tooltip"], .enable-tooltip').tooltip({container: 'body', animation: false});

        // Initialize Popovers
        $('[data-toggle="popover"], .enable-popover').popover({container: 'body', animation: true});

        // Initialize Image Lightbox
        $('[data-toggle="lightbox-image"]').magnificPopup({type: 'image', image: {titleSrc: 'title'}});

        // Initialize image gallery lightbox
        $('[data-toggle="lightbox-gallery"]').each(function(){
            $(this).magnificPopup({
                delegate: 'a',
                type: 'image',
                gallery: {
                    enabled: true,
                    navigateByImgClick: true,
                    arrowMarkup: '<button type="button" class="mfp-arrow mfp-arrow-%dir%" title="%title%"></button>',
                    tPrev: 'Previous',
                    tNext: 'Next',
                    tCounter: '<span class="mfp-counter">%curr% of %total%</span>'
                },
                image: {titleSrc: 'title'}
            });
        });

        // Initialize Chosen
        $('.select-chosen').chosen({width: "100%", no_results_text: "Oops, nada encontrado!"});

        // Easy Pie Chart
        $('.pie-chart').easyPieChart({
            barColor: $(this).data('bar-color') ? $(this).data('bar-color') : '#777777',
            trackColor: $(this).data('track-color') ? $(this).data('track-color') : '#eeeeee',
            lineWidth: $(this).data('line-width') ? $(this).data('line-width') : 3,
            size: $(this).data('size') ? $(this).data('size') : '80',
            animate: 800,
            scaleColor: false
        });

        // Toggles 'open' class on toggle menu
        $('.toggle-menu .submenu').on('click', function(){
           $(this)
               .parent('li')
               .toggleClass('open');
        });

        // Initialize Placeholder (for IE9)
        $('input, textarea').placeholder();
    };

    /* Sidebar Navigation functionality */
    var handleNav = function() {
        // Get all vital links
        var allLinks        = $('.sidebar-nav a', sidebar);
        var menuLinks       = $('.sidebar-nav-menu', sidebar);
        var submenuLinks    = $('.sidebar-nav-submenu', sidebar);

        // Add ripple effect to all navigation links
        allLinks.on('click', function(e){
            var link = $(this), ripple, d, x, y;

            // Remove .animate class from all ripple elements
            sidebar.find('.sidebar-nav-ripple').removeClass('animate');

            // If the ripple element doesn't exist in this link, add it
            if(link.children('.sidebar-nav-ripple').length === 0) {
                link.prepend('<span class="sidebar-nav-ripple"></span>');
            }

            // Get the ripple element
            var ripple = link.children('.sidebar-nav-ripple');

            // If the ripple element doesn't have dimensions set them accordingly
            if(!ripple.height() && !ripple.width()) {
                d = Math.max(link.outerWidth(), link.outerHeight());
                ripple.css({height: d, width: d});
            }

            // Get coordinates for our ripple element
            x = e.pageX - link.offset().left - ripple.width()/2;
            y = e.pageY - link.offset().top - ripple.height()/2;

            // Position the ripple element and add the class .animate to it
            ripple.css({top: y + 'px', left: x + 'px'}).addClass('animate');
        });

        // Primary Accordion functionality
        menuLinks.on('click', function(e){
            var link = $(this);
            var windowW = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

            // If we are in mini sidebar mode
            if (page.hasClass('sidebar-visible-lg-mini') && (windowW > 991)) {
                if (link.hasClass('open')) {
                    link.removeClass('open');
                }
                else {
                    $('#sidebar .sidebar-nav-menu.open').removeClass('open');
                    link.addClass('open');
                }
            }
            else if (!link.parent().hasClass('active')) {
                if (link.hasClass('open')) {
                    link.removeClass('open');
                }
                else {
                    $('#sidebar .sidebar-nav-menu.open').removeClass('open');
                    link.addClass('open');
                }

                // Resize Page Content
                setTimeout(resizePageContent, 50);
            }

            return false;
        });

        // Submenu Accordion functionality
        submenuLinks.on('click', function(e){
            var link = $(this);

            if (link.parent().hasClass('active') !== true) {
                if (link.hasClass('open')) {
                    link.removeClass('open');
                }
                else {
                    link.closest('ul').find('.sidebar-nav-submenu.open').removeClass('open');
                    link.addClass('open');
                }

                // Resize Page Content
                setTimeout(resizePageContent, 50);
            }

            return false;
        });
    };

    /* Ripple effect on click functionality */
    var rippleEffect = function(element, cl){
        // Add required classes to the element
        element.css({
            'overflow': 'hidden',
            'position': 'relative'
        });

        // On element click
        element.on('click', function(e){
            var elem = $(this), ripple, d, x, y;

            // If the ripple element doesn't exist in this element, add it..
            if(elem.children('.' + cl).length === 0) {
                elem.prepend('<span class="' + cl + '"></span>');
            }
            else { // ..else remove .animate class from ripple element
                elem.children('.' + cl).removeClass('animate');
            }

            // Get the ripple element
            var ripple = elem.children('.' + cl);

            // If the ripple element doesn't have dimensions set them accordingly
            if(!ripple.height() && !ripple.width()) {
                d = Math.max(elem.outerWidth(), elem.outerHeight());
                ripple.css({height: d, width: d});
            }

            // Get coordinates for our ripple element
            x = e.pageX - elem.offset().left - ripple.width()/2;
            y = e.pageY - elem.offset().top - ripple.height()/2;

            // Position the ripple element and add the class .animate to it
            ripple.css({top: y + 'px', left: x + 'px'}).addClass('animate');
        });
    };

    /* Sidebars Functionality */
    var handleSidebar = function(mode){
        if (mode === 'init') {
            // Init sidebars scrolling functionality
            handleSidebar('sidebar-scroll');
            handleSidebar('sidebar-alt-scroll');

            // Handle main sidebar's scrolling functionality on resize or orientation change
            var sScrollTimeout;

            $(window).on('resize orientationchange', function(){
                clearTimeout(sScrollTimeout);

                sScrollTimeout = setTimeout(function(){
                    handleSidebar('sidebar-scroll');
                }, 150);
            });
        } else {
            var windowW = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

            if (mode === 'toggle-sidebar') {
                if ( windowW > 991) { // Toggle main sidebar in large screens (> 991px)
                    if (page.hasClass('sidebar-visible-lg-full')) {
                        page.removeClass('sidebar-visible-lg-full').addClass('sidebar-visible-lg-mini');
                    } else if (page.hasClass('sidebar-visible-lg-mini')) {
                        page.removeClass('sidebar-visible-lg-mini').addClass('sidebar-visible-lg-full');
                    } else {
                        page.addClass('sidebar-visible-lg-mini');
                    }

                    // Resize Page Content
                    setTimeout(resizePageContent, 50);
                } else { // Toggle main sidebar in small screens (< 992px)
                    page.toggleClass('sidebar-visible-xs');

                    if (page.hasClass('sidebar-visible-xs')) {
                        handleSidebar('close-sidebar-alt');
                    }
                }

                // Handle main sidebar scrolling functionality
                handleSidebar('sidebar-scroll');
            }
            else if (mode === 'open-sidebar') {
                if ( windowW > 991) { // Open main sidebar in large screens (> 991px)
                    page.removeClass('sidebar-visible-lg-mini').addClass('sidebar-visible-lg-full');
                } else { // Open main sidebar in small screens (< 992px)
                    page.addClass('sidebar-visible-xs');
                    handleSidebar('close-sidebar-alt');
                }
                // Handle main sidebar scrolling functionality
                handleSidebar('sidebar-scroll');

                // Resize Page Content
                setTimeout(resizePageContent, 50);
            }
            else if (mode === 'close-sidebar') {
                if ( windowW > 991) { // Close main sidebar in large screens (> 991px)
                    page.removeClass('sidebar-visible-lg-full').addClass('sidebar-visible-lg-mini');
                } else { // Close main sidebar in small screens (< 992px)
                    page.removeClass('sidebar-visible-xs');
                }

                // Handle main sidebar scrolling functionality
                handleSidebar('sidebar-scroll');
            }
            else if (mode === 'toggle-sidebar-alt') {
                if ( windowW > 991) { // Toggle alternative sidebar in large screens (> 991px)
                    page.toggleClass('sidebar-alt-visible-lg');
                } else { // Toggle alternative sidebar in small screens (< 992px)
                    page.toggleClass('sidebar-alt-visible-xs');

                    if (page.hasClass('sidebar-alt-visible-xs')) {
                        handleSidebar('close-sidebar');
                    }
                }
            }
            else if (mode === 'open-sidebar-alt') {
                if ( windowW > 991) { // Open alternative sidebar in large screens (> 991px)
                    page.addClass('sidebar-alt-visible-lg');
                } else { // Open alternative sidebar in small screens (< 992px)
                    page.addClass('sidebar-alt-visible-xs');
                    handleSidebar('close-sidebar');
                }
            }
            else if (mode === 'close-sidebar-alt') {
                if ( windowW > 991) { // Close alternative sidebar in large screens (> 991px)
                    page.removeClass('sidebar-alt-visible-lg');
                } else { // Close alternative sidebar in small screens (< 992px)
                    page.removeClass('sidebar-alt-visible-xs');
                }
            }
            else if (mode === 'sidebar-scroll') { // Handle main sidebar scrolling
                if (page.hasClass('sidebar-visible-lg-mini') && (windowW > 991)) { // Destroy main sidebar scrolling when in mini sidebar mode
                    if (sScroll.length && sScroll.parent('.slimScrollDiv').length) {
                        sScroll
                            .slimScroll({destroy: true});
                        sScroll
                            .attr('style', '');
                    }
                }
                else if ((header.hasClass('navbar-fixed-top') || header.hasClass('navbar-fixed-bottom'))) {
                    var sHeight = $(window).height() - ((sBrand.css('display') === 'none' ? 0 : sBrand.outerHeight()));

                    if ( windowW < 992) { sHeight = sHeight - 50; }

                    if (sScroll.length && (!sScroll.parent('.slimScrollDiv').length)) { // If scrolling does not exist init it..
                        sScroll
                            .slimScroll({
                                height: sHeight,
                                color: '#bbbbbb',
                                size: '3px',
                                touchScrollStep: 100,
                                railVisible: false,
                                railOpacity: 1
                            });
                    }
                    else { // ..else resize scrolling height
                        sScroll
                            .add(sScroll.parent())
                            .css('height', sHeight);
                    }
                }
            }
            else if (mode === 'sidebar-alt-scroll') { // Init alternative sidebar scrolling
                if (sScrollAlt.length && (!sScrollAlt.parent('.slimScrollDiv').length)) { // If scrolling does not exist init it..
                    sScrollAlt.slimScroll({
                        height: sidebarAlt.outerHeight(),
                        color: '#bbbbbb',
                        size: '3px',
                        touchScrollStep: 100,
                        railVisible: false,
                        railOpacity: 1
                    });

                    // Resize alternative sidebar scrolling height on window resize or orientation change
                    var sScrollAltTimeout;

                    $(window).on('resize orientationchange', function(){
                        clearTimeout(sScrollAltTimeout);

                        sScrollAltTimeout = setTimeout(function(){
                            handleSidebar('sidebar-alt-scroll');
                        }, 150);
                    });
                }
                else { // ..else resize scrolling height
                    sScrollAlt.add(sScrollAlt.parent()).css('height', sidebarAlt.outerHeight());
                }
            }
        }
    };

    /* Resize #page-content to fill empty space if exists */
    var resizePageContent = function() {
        var windowH     = $(window).height();
        var headerH     = header.outerHeight();
        var sidebarH    = sidebar.outerHeight();

        if (header.hasClass('navbar-fixed-top') || header.hasClass('navbar-fixed-bottom')) {
            pageContent.css('min-height', windowH);
        } else if (sidebarH > windowH) {
            pageContent.css('min-height', sidebarH - headerH);
        } else {
            pageContent.css('min-height', windowH - headerH);
        }
    };

    /* Print functionality - Hides all sidebars, prints the page and then restores them (To fix an issue with CSS print styles in webkit browsers)  */
    var handlePrint = function() {
        // Store all #page-container classes
        var pageCls = page.prop('class');

        // Remove all classes from #page-container
        page.prop('class', '');

        // Print the page
        window.print();

        // Restore all #page-container classes
        page.prop('class', pageCls);
    };

    return {
        init: function() {
            uiInit(); // Initialize UI
        },
        sidebar: function(mode, extra) {
            handleSidebar(mode, extra); // Handle sidebars - access functionality from everywhere
        },
        pagePrint: function() {
            handlePrint(); // Print functionality
        }
    };
}();

/* Initialize App when page loads */
$(function(){
    FastClick.attach(document.body);
    App.init();
    // $("body").swipe({
    //     swipe: function (event, direction, distance, duration, fingerCount, fingerData) {
    //         if (direction === "left") {
    //             App.sidebar('close-sidebar');
    //         } else if (direction === "right") {
    //             App.sidebar('open-sidebar');
    //         }
    //     },
    //     excludedElements: $.fn.swipe.defaults.excludedElements + ", .table-responsive, button, input, select, textarea, a",
    // });
});