$(document).ready(function () {

    stockerAchat();
})
function stockerAchat() {
    $('.tabel_achat').on('click', '.stocker_achat', function () {
        var id_achat = $(this).data('id_achat');
        $.ajax({
            url: Routing.generate('achat_stocker_achat'),
            type: "POST",
            data: {id_achat: id_achat},
            success: function (data) {
                if (data.success) {
                    $('#stocker_achat_'+id_achat).html(`<span class="badge badge-success ml-1">Stocké</span>`);
                    $('#btnStocked_'+id_achat).empty();
                    toastr.success(data.message);


                }else{
                    toastr.error(data.message);
                }


            },
            error: function () {
                alert('something wrong')
            }


        });
    })
}

