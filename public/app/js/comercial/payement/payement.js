$(document).ready(function () {
    onchangeMantant();
    savePayement();
})




function savePayement() {
    $('.check-payer').click(function () {
        $('#formPayement').submit();

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