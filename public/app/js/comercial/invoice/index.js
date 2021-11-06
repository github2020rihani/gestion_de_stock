$(document).ready(function () {
    changePaiement();
    saveNewType();

})
var id_invoice;
var type;

function changePaiement() {
    $('.table_invoice').on('click', '.changePaiement', function () {
        id_invoice = $(this).data('id_invoice');
    })

}

function saveNewType() {
    $('.saveNewType').click(function () {
        //appel ajax
        var type_paiement = $('.type_payement').val();
        if ($('.type_payement').val() == null) {
            toastr.error('Choisir un type de paiement ');
            return false;
        }
        $.ajax({
            url: Routing.generate('api_change_payement'),
            type: "POST",
            data: {type_paiement: type_paiement, id_invoice: id_invoice},
            success: function (data) {
                if (data.status == true) {
                    toastr.success(data.message)
                } else {
                    toastr.error(data.message)

                }
                if (type_paiement == 1) {
                    type = 'Espece';
                } else if (type_paiement == 2) {
                    type= 'Cheque';

                } else {
                    type= 'Carte';

                }
                $('.table_invoice td span.typePaiement_'+id_invoice).text(type);

                $('#onboardingSlideModal').modal('hide');
                $('.type_payement').val(0);

            },
            error: function () {
                alert('something wrong')
            }
        })

    })

}