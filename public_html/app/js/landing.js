// WOW

if($(window).width() > 992) {
    new WOW().init();
}

$(function () {
    // SMOOTH SCROLL

    var $root = $('html, body');
    $('.navbar a').click(function() {
        $root.animate({
            scrollTop: $($.attr(this, 'href')).offset().top -100}, 500);
        return false;
    });

    // MENU UPDATE WHEN SCROLLILNG
    $(document).scroll(function () {
        $('#home, #advantages, #functions, #mobile-hr, #contacts').each(function () {
            var top = window.pageYOffset;
            var distance = top - $(this).offset().top;
            var id = $(this).attr('id');
            if (distance < 101 && distance > -101) {
                $('.navbar a').removeClass('active');
                $("#link-" + id + " a").addClass('active');
            }
            if($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
                $('.navbar a').removeClass('active');
                $("#link-contacts a").addClass('active');
            }
        });
    });

    // PRESENTATION
    $(".presentation .to-slide").click(function() {
        var id = $(this).data("slide");
        var slide = $("#slide" + id);
        if(slide.css("display") == "none") {
            $(".presentation li").removeClass("active");
            $("a[data-slide='" + id + "']").parent().addClass("active");
            $(".presentation .slide").fadeOut();
            slide.fadeIn();
        }
    });

    $('.functions .presentation ul').slimScroll({
        height: 'auto',
        position: 'left',
        alwaysVisible: true,
        railVisible: true
    });

    //SCROLL SLIDE LIST WHEN VIEW
    $(".to-slide").bind("click", function(e) {
        var slideNumber = $(e.target).parent().data("slide");
        var currentPosition = parseInt($(".functions .presentation .slimScrollBar").css("top"));
        var slideLinkHeight = $(".slide-link").height();
        var blockBottomLine = 440 + currentPosition*2;
        var blockTopLine = currentPosition*2;
        var bottomLine = slideNumber * slideLinkHeight;
        var topLine = (slideNumber-1) * slideLinkHeight + 1;
        if(bottomLine > blockBottomLine) {
            $('.functions .presentation ul').slimScroll({
                height: 'auto',
                position: 'left',
                alwaysVisible: true,
                railVisible: true,
                scrollTo: currentPosition*2 + 111 + "px"
            });
        } else if(topLine < blockTopLine) {
            $('.functions .presentation ul').slimScroll({
                height: 'auto',
                position: 'left',
                alwaysVisible: true,
                railVisible: true,
                scrollTo: currentPosition*2 - 111 + "px"
            });
        }
    });

    // SLIDE CAROUSEL ON SWIPE
    var carousel = $(".carousel");
    carousel.swiperight(function() {
        $(this).carousel('prev');
    });
    carousel.swipeleft(function() {
        $(this).carousel('next');
    });

    // COLLAPSE MENU WHEN CLICK ON LINK

    $(".nav a").click(function() {
        $('.navbar-collapse.collapse').collapse("hide");
    });
});
