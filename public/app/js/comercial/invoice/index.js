$(document).ready(function () {
    changePaiement();
    saveNewType();
    printInvoice();

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


function printInvoice() {

    $('.table_invoice').on('click', '.printInvoice', function () {
        var id_invoice = $(this).data('id_invoice');
        //appel ajx
        $.ajax({
            url: Routing.generate('perso_print_invoice'),
            cache: false,
            data: {id_invoice: id_invoice},
            xhr: function () {
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 2) {
                        if (xhr.status == 200) {
                            xhr.responseType = "blob";
                        } else {
                            xhr.responseType = "text";
                        }
                    }
                };

                return xhr;
            },
            success: function (data) {
                if (data) {
                    var url = window.URL || window.webkitURL;
                    let filename ='invoice.pdf';
                    // var pdfFile = new Blob([data], { type: "application/octetstream" });
                    var pdfFile = new Blob([data], {type: "application/pdf"});

                    let downloadLink = document.createElement("a");
                    downloadLink.download = filename;
                    downloadLink.href = url.createObjectURL(pdfFile);
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                }else{
                    alert('something wrong')

                }


            },
            error: function () {
                alert('something wrong')
            }
        })
    })
}