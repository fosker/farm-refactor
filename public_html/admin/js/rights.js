$( document ).ready(function() {
    $(".all").on("click", function() {
        $("input[type=radio][value='1']").prop("checked",true);
    });
});