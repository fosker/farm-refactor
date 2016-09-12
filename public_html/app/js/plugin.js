$( document ).ready(function() {
    $(document).click(function() {
        var el = $(this);
        var text = el.text();
        translate(text, function(data) {
            p.text(data.text[0]);
        });
    });
});

function translate(text, handle) {
    $.ajax({
        type: "POST",
        url: "https://translate.yandex.net/api/v1.5/tr.json/translate",
        data: {
            key: "trnsl.1.1.20160908T104728Z.4ba24ab35cbb285a.7bfabe0288925ead321a0645c82cfb63d9cc74c3",
            text: text,
            lang: "en"
        },
        success: function(data){
            handle(data);
        }
    });
}