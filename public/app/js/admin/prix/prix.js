$(document).ready(function () {
    onChangePrixAchatNet();
    onchangePrixVenteTTC();
    modifierPrix();
    annuler();



})
var newPAchatHT = 0;
var newPventeTTc = 0;
var oldpuAchatTTc = 0;
var oldPuVenteHT = 0;


function modifierPrix() {
    $('.table_prix').on('click', '.modifierPrix', function () {
        var index = $(this).data('index');
        var id_article_prix = $(this).data('id_article');
        //check exist update or nn
        if (newPAchatHT != 0 || newPventeTTc != 0) {
            if (oldpuAchatTTc !=  $('.pachatttc_' + index).val() || oldPuVenteHT !=  $('.pventeht_' + index).val()){
                //openModal
                $('#modalConfirmChangePrix').modal('show');
                //appel function che oui
                ConfirmeUpdatePrix(id_article_prix, index, newPAchatHT , newPventeTTc);
                //appel function check non
                $('.AnnulerModif').on('click', function () {
                    location.reload();

                })
            }else {
                toastr.info('Aucun Modification exécuter');

            }

        } else {
//aucun modif
            toastr.info('Aucun Modification exécuter');
        }





    })


}

function annuler() {
    $('.table_prix').on('click', '.AnnulerModif', function () {
        location.reload();

    })

}


function onChangePrixAchatNet() {
    $('.pachatnet').on("input", function (e) {
        var index = $(this).data('index');
        oldpuAchatTTc = $('.pachatttc_' + index);
        newPAchatHT = parseFloat($(this).val());

        $(this).attr('value', $(this).val());
        $('.pachatttc_' + index).val((parseFloat($(this).val()) * 1.19).toFixed(3));
        //calcule taux

        pventettc = parseFloat($('.pventettc_' + index).val()).toFixed(3);
        pachattc = parseFloat($('.pachatttc_' + index).val()).toFixed(3);

        taux = parseFloat(((pventettc - pachattc) / pachattc) * 100).toFixed(2)
        $('.taux_' + index).val(taux);

    })

}

function onchangePrixVenteTTC() {
    $('.pventettc').on("input", function (e) {
        var index = $(this).data('index');
        oldPuVenteHT = $('.pventeht_' + index).val();
        newPventeTTc = parseFloat($(this).val());

        $(this).attr('value', $(this).val());
        $('.pventeht_' + index).val(parseFloat($(this).val() / 1.19).toFixed(3));
        //calcule taux
        taux = parseFloat((($(this).val() - $('.pachatttc_' + index).val()) / $('.pachatttc_' + index).val()) * 100).toFixed(2);
        $('.taux_' + index).val(taux);


    })

}

function ConfirmeUpdatePrix(id_article_prix, index, pachatht, pventettc) {

    $('#confirmeChangePrix').click(function () {

        $.ajax({
            url: Routing.generate('achat_confirmeUpdatePrix'),
            type: "POST",
            data: {id_article_prix: id_article_prix, pachatht: pachatht, pventettc: pventettc},
            success: function (data) {
                if (data.status == "true") {
                    $('#modalConfirmChangePrix').modal('hide');
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