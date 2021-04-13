jQ(document).ready(function () {
    var AJAX_PATH = WEB_ROOT + "/ajax/inventory.php";
    function searchResource() {
        var form = jQ(this).parents('form:first');
        var fd = new FormData(form[0]);
        jQ.ajax({
            url: AJAX_PATH,
            method: 'post',
            data: fd,
            processData: false,
            contentType: false,
            type: 'POST',
            beforeSend: function () {
                jQ("#loading").show();
                jQ('#btnSearch').hide();
            },
            success: function (response) {
                var splitResp = response.split('[#]')
                jQ('#loading').hide();
                jQ("#btnSearch").show();
                window.location = splitResp[1];
            }
        });
    }
    jQ("#btnSearch").on("click", searchResource);
});
