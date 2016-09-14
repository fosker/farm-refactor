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

    var options = {
        useEasing : true,
        useGrouping: true,
        separator : ',',
        decimal : '.',
        prefix : '',
        suffix : ''
    };

    var counters = $('.counter');

    $.each(counters, function() {
        if($(this).isVisible()) {
            var counter = new CountUp(this, 0, $(this).text(), 0, 5, options);
            counter.start(afterCount(this));
        }
    });
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


