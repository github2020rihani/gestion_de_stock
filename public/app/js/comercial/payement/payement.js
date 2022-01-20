$(document).ready(function () {
    onchangeMantant();
    savePayement();



})




function savePayement() {
    $('.check-payer').click(function () {

        if ($('.type_pay').text() =='Cheque') {
            if ($('.num_cheque').length <=20){
                toastr.error('numero cheque incorrect ');
                return false;
            }
        }


        $('#formPayement').submit();
        $('.payer').empty();
        $('.payer').append('    <h1 class="text-center" style="color: green">Paiement  a été effectué avec succès </h1>\n');



    })
}

var totalTTC = $('.totalTTC').data('value');
var rest = 0 ;
var retenue = 0 ;
function onchangeMantant() {
    $('.montant').blur(function () {

        if ($(this).val() > totalTTC) {
            retenue =((totalTTC - $(this).val()));
            $('.retenue').val((retenue).toFixed(3))
            $('.reste').val(0.000);
        }else if ($(this).val() < totalTTC){
            rest =((totalTTC - $(this).val()));
            $('.reste').val((rest).toFixed(3))
            $('.retenue').val(0.000);
        }else{
            $('.retenue').val(0.000);
            $('.reste').val(0.000);
        }

    })

}