$( document ).ready(function() {

    $(".more-cities").on("click", function() {
        if($(this).text() == '+') {
            $($(this).parents()[3]).find(".cities").css("display", "block");
            $(this).html('-');
        } else if($(this).text() == '-') {
            $($(this).parents()[3]).find(".cities").css("display", "none");
            $(this).html('+');
        }
    });

    $(".more-pharmacies").on("click", function() {
        if($(this).text() == '+') {
            $($(this).parents()[3]).find(".pharmacies").css("display", "block");
            $(this).html('-');
        } else if($(this).text() == '-') {
            $($(this).parents()[3]).find(".pharmacies").css("display", "none");
            $(this).html('+');
        }
    });

    $(".more-regions").on("click", function() {
        if($(this).text() == '+') {
            $($(this).parents()[3]).find(".region-months").css("display", "block");
            $(this).html('-');
        } else if($(this).text() == '-') {
            $($(this).parents()[3]).find(".region-months").css("display", "none");
            $(this).html('+');
        }
    });

    $(".more-users").on("click", function() {
        if($(this).text() == '+') {
            $($(this).parents()[2]).find(".user-region-month").css("display", "block");
            $(this).html('-');
        } else if($(this).text() == '-') {
            $($(this).parents()[2]).find(".user-region-month").css("display", "none");
            $(this).html('+');
        }
    });
});
