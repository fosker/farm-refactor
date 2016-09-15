$( document ).ready(function() {

    $(".container-fields").on('change', 'select', function() {
        if(this.value == 2 || this.value == 3) {
            $($(this).parents()[6]).find("td:nth-child(4) *").prop("disabled", true);
        } else {
            $($(this).parents()[6]).find("td:nth-child(4) *").prop("disabled", false);
        }
    });

});

