$.fn.isVisible = function() {
    var rect = this[0].getBoundingClientRect();
    return (
    (rect.height > 0 || rect.width > 0) &&
    rect.bottom >= 0 &&
    rect.right >= 0 &&
    rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
    rect.left <= (window.innerWidth || document.documentElement.clientWidth)
    );
};

function doCheck() {

    $('.counter').css("visibility", "hidden");

    setTimeout(function () {
        var easingFn = function (t, b, c, d) {
            var ts = (t /= d) * t;
            var tc = ts * t;
            return b + c * (tc + -3 * ts + 3 * t);
        };
        var options = {
            useEasing : true,
            easingFn: easingFn,
            useGrouping : true,
            separator : ',',
            decimal : '.',
            prefix : '',
            suffix : ''
        };

        var counters = $('.counter');
        counters.css("visibility", "visible");

        $.each(counters, function() {
            if($(this).isVisible()) {
                var counter = new CountUp(this, 0, $(this).text(), 0, 5, options);
                counter.start(afterCount(this));
            }
        });
    }, 1500);
}

function afterCount(item) {
    $(item).attr('class', '');
}

$(document).ready(function(e){
        doCheck();
    });

$(window).scroll(function(e){
        doCheck();
    });


