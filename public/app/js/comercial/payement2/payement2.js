$(document).ready(function () {
    onchangeMantant2();
    savePayement2();
})




function savePayement22() {
    $('.check-payer2').click(function () {
        if ($('.num_cheque').length <=20){
            toastr.error('numero cheque incorrect ');
            return false;
        }
        $('#formPayement2').submit();
        $('.payer').empty();
        $('.payer').append('    <h1 class="text-center" style="color: green">Paiement  a été effectué avec succès </h1>\n');
    })
}

var montant_reste_payer = $('.montant_reste_payer').data('value');
var rest = 0 ;
var retenue = 0 ;
function onchangeMantant2() {
    $('.montant').blur(function () {

        if ($(this).val() > montant_reste_payer) {
            retenue =((montant_reste_payer - $(this).val()));
            $('.retenue').val((retenue).toFixed(3))
            $('.reste').val(0.000);
        }else if ($(this).val() < montant_reste_payer){
            rest =((montant_reste_payer - $(this).val()));
            $('.reste').val((rest).toFixed(3))
            $('.retenue').val(0.000);
        }else{
            $('.retenue').val(0.000);
            $('.reste').val(0.000);
        }

    })

}