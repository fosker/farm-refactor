new WOW().init();

$(function () {
    var $root = $('html, body');
    $('.navbar a').click(function() {
        $root.animate({
            scrollTop: $($.attr(this, 'href')).offset().top -30}, 500);
        return false;
    });

    $(document).scroll(function () {
        $('#info, #advantages, #functions, #mobile-hr, #contacts').each(function () {
            var top = window.pageYOffset;
            var distance = top - $(this).offset().top;
            var id = $(this).attr('id');
            if (distance < 31 && distance > -31) {
                $('.navbar a').removeClass('active');
                $("#link-" + id + " a").addClass('active');
            }
            if($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
                $('.navbar a').removeClass('active');
                $("#link-contacts a").addClass('active');
            }
        });
    });
});