var checked_cities = [];
var checked_companies = [];

function generatePharmacies() {
    $("input:checkbox[name='cities[]']:checked").each(function(){
        checked_cities.push($(this).val());
    });
    $("input:checkbox[name='companies[]']:checked").each(function(){
        checked_companies.push($(this).val());
    });
    $.ajax({
        type: 'POST',
        url: "index.php?r=list/pharmacies",
        data: {'cities[]': checked_cities, 'companies[]' : checked_companies},
        success: function(data) {
            var obj = jQuery.parseJSON(data);
            var modal_body = $("#pharmacies .modal-body");
            modal_body.html("");
            modal_body.append("<div>");
            modal_body.find("div").append("<ul class=list-group>");
            modal_body.find("div ul.list-group").append("<label>");
            modal_body.find("div ul.list-group label").append("<input type=checkbox class=all_pharmacies name=all value=1> Все");
            modal_body.find("div ul.list-group").append("<div>");
            var div_list_group = $("#pharmacies .modal-body div ul.list-group div");
            $.each(obj, function(index, val) {
                div_list_group.append("<li class=list-group-item>");
                div_list_group.find("li")
                    .eq(index)
                    .append("<ul class=list-group>");
                div_list_group.find("li ul")
                    .eq(index)
                    .append("<label>");
                div_list_group.find("li ul label")
                    .eq(index)
                    .append("<input type=checkbox name=pharmacies[] value="+val.id+"> "+val.name);
            });
        },
        async:false
    });
};

$('#pharmacies').on('shown.bs.modal', function () {
    var check_all = $('.all-groups')[0];
    if(check_all.checked == false){
        $("#pharmacies .modal-body").append("<p>Загрузка...");
        checked_cities = [];
        checked_companies = [];
        generatePharmacies();
    }
});

$(".all-groups").on("change", function() {
    var status = this.checked;
    var buttons = $("input[type=checkbox][class='all']");
    buttons.each(function(){
        $(this).prop('checked', status);
        $(this).parent().next().children().each(function(i, elem) {
            var checkbox = $(elem).find('label > input');
            checkbox.prop('checked', status);
        });
    });
    checked_cities = [];
    checked_companies = [];
    generatePharmacies();
    var all_pharmacies = $('.all_pharmacies');
    all_pharmacies.each(function(){
        $(this).prop('checked', status);
        $(this).parent().next().children().each(function(i, elem) {
            var checkbox = $(elem).find('label > input');
            checkbox.prop('checked', status);
        });
    });
});

$("#pharmacies .modal-body").on('change', '.all_pharmacies', function(e) {
    var status = this.checked;
    $(e.target).parent().next().children().each(function(i, elem) {
        var checkbox = $(elem).find('label > input');
        if(status) {
            checkbox.prop('checked', status);
        } else {
            checkbox.removeAttr('checked');
        }
    });
});


$('.list-group > label > .all ').change(function (e) {
    var status = this.checked;
   $(e.target).parent().next().children().each(function(i, elem) {
       var checkbox = $(elem).find('label > input');
       if(status) {
           checkbox.prop('checked', status);
       } else {
           checkbox.removeAttr('checked');
       }
   });
});

$('.list-group-item > .list-group > label > input').change(function (e) {
    var status = this.checked;
    $(e.target).parent().next().each(function(i, elem) {
        var checkbox = $(elem).find('label > input');
        if(status) {
            checkbox.prop('checked', status);
        } else {
            checkbox.removeAttr('checked');
        }
    });
});