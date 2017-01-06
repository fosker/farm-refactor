$( document ).ready(function() {

    $('#delete-survey').click(function(){
        var ids = $('#w0').yiiGridView('getSelectedRows');
        $.ajax({
            type: 'POST',
            url : 'index.php?r=surveys/answer/multiple-delete',
            data : {row_id: ids},
            success : function() {
                $(this).closest('tr').remove();
            }
        });
    });

    $('#delete-presentation').click(function(){
        var ids = $('#w0').yiiGridView('getSelectedRows');
        $.ajax({
            type: 'POST',
            url : 'index.php?r=presentations/answer/multiple-delete',
            data : {row_id: ids},
            success : function() {
                $(this).closest('tr').remove();
            }
        });
    });

    $('#delete-user').click(function(){
        var ids = $('#w1').yiiGridView('getSelectedRows');
        $.ajax({
            type: 'POST',
            url : 'index.php?r=user/multiple-delete',
            data : {row_id: ids},
            success : function() {
                $(this).closest('tr').remove();
            }
        });
    });
});
