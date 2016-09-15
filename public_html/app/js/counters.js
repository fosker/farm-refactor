$.fn.isVisible = function()
{
    var rect = this[0].getBoundingClientRect();
    return (
    (rect.height > 0 || rect.width > 0) &&
    rect.bottom >= 0 &&
    rect.right >= 0 &&
    rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
    rect.left <= (window.innerWidth || document.documentElement.clientWidth)
    );
};

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

var numbers = [];

var counters = [];

$.each($('.counter'), function() {
    numbers.push($(this).text());
    $(this).text("0");
});

$.each($('.counter'), function(index) {
    counters.push(new CountUp(this, 0, numbers[index], 0, 4, options));
});


function count()
{
    var elements = $('.counter');

    if($(elements[0]).isVisible()) {
        setTimeout(function () {
            $.each(counters, function(index) {
                this.start(afterCount(elements[index]));
            });
        }, 1500);
    }
}

function afterCount(item)
{
    $(item).attr('class', '');
}

$(document).ready(function(e)
{
    count();
});

$(window).scroll(function(e)
{
    count();
});


