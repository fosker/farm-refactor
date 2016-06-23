
$( document ).ready(function() {

    var buttons = $(".question-item td .del-option");

    buttons.each(function() {
        var length = $($(this).parents()[2]).children().length;
        if(length > 1) {
            $($(this).parents()[6]).find("td:nth-child(3) tr:nth-child(2)").css("display", "none");
        }
    });

    $(".question-item").on("click", ".add-option", function() {
        console.log($(this));
        $($(this).parents()[6]).find("td:nth-child(3) tr:nth-child(2)").css("display", "none");
    });

    $(".question-item td").on("click", ".del-option", function() {
        var length = $($(this).parents()[2]).children().length;
        if(length < 2) {
            $($(this).parents()[6]).find("td:nth-child(3) tr:nth-child(2)").css("display", "block");
        }
    });
});

