var AJAX_PATH = WEB_ROOT+'/ajax/search-invoice.php'
jQ(document).ready(function(){

    jQ('#btnSearch').on('click',function() {
        var id = this.id;
        var form = jQ(this).parents('form:first');
        var fd = new FormData(form[0]);
        jQ.ajax({
            url: AJAX_PATH,
            data: fd,
            processData: false,
            contentType: false,
            type: 'POST',
            beforeSend: function () {
                jQ('#loading-img').show();
                jQ('#' + id).hide();
            },
            success: function (response) {
                jQ('#' + id).show();
                jQ('#loading-img').hide();
                window.location = response;
            },
            error: function () {
                jQ('#' + id).show();
                alert('error')
            }
        });
    });

});