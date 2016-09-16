new WOW().init();

/* MENU */

$(function () {
    var $root = $('html, body');
    $('.navbar a').click(function() {
        $root.animate({
            scrollTop: $($.attr(this, 'href')).offset().top -100}, 500);
        return false;
    });

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
});

/* Presentation */
