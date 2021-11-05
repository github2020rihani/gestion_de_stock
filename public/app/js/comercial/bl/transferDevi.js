$(document).ready(function () {

    saveBlWithDevis();
})


function saveBlWithDevis() {
    $('.addBLWithDevis').click(function () {
        if ($('.type_payement').val() == null ) {
            toastr.error('Choisir le type de paiement');
            return false ;
        }
    })

}