$(document).ready(function () {

    saveBlWithDevis();
})


function saveBlWithDevis() {
    $('.addBLWithDevis').click(function () {
        var id_devis = $(this).data('id_devis');
        if ($('.type_payement').val() == null) {
            toastr.error('Choisir le type de paiement');
            return false;
        }
        //apel ajax save bl and facture
        $.ajax({
            url: Routing.generate('perso_api_saveBlAndFacture_bl'),
            type: "POST",
            data: {id_devis: id_devis, 'type_payement': $('.type_payement').val()},
            success: function (data) {
                $(this).hide();
                if (data.status = false) {
                    toastr.error(data.message);
                    return false;

                }
                toastr.success(data.message);
                //generate facture et bl and message toastr

                window.location.href = '../detail/'+data.idBl;


            },
            error: function () {
                alert('something wrong')
            }
        })


    })

}