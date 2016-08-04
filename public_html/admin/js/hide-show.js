$( document ).ready(function() {

    $(".more-cities").on("click", function() {
        if($(this).text() == 'подробнее') {
            $($(this).parents()[3]).find(".cities").css("display", "block");
            $(this).html('скрыть');
        } else if($(this).text() == 'скрыть') {
            $($(this).parents()[3]).find(".cities").css("display", "none");
            $(this).html('подробнее');
        }
    });

    $(".more-pharmacies").on("click", function() {
        if($(this).text() == 'подробнее') {
            $($(this).parents()[3]).find(".pharmacies").css("display", "block");
            $(this).html('скрыть');
        } else if($(this).text() == 'скрыть') {
            $($(this).parents()[3]).find(".pharmacies").css("display", "none");
            $(this).html('подробнее');
        }
    });
});
