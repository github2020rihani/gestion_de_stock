$(document).ready(function () {

    var date ;

    $('.date_selected').change(function () {

        date = $(this).val();
        $.ajax({
            url: Routing.generate('change_caisse'),
            data: {date: date},
            cache: false,
            success: function (data) {
                console.log(data);
                if (data.status){
                    $('.twig').empty();
                    $('.twig').append(data.twig);
                    $('.printCaisse').show();

                }else{
                    toastr.error('Pas de enregistrement');
                    $('.twig').empty();

                    $('.printCaisse').hide();

                }

            },
            error: function () {
                alert('something wrong')

            }
        });


    })


    $('.printCaisse').click(function () {
        if ($('#dataTable1').length == 0){
            toastr.error('Pas de enregistrement');
            return false;

        }
        date =  $('.date_selected').val();
        $.ajax({
            url: Routing.generate('print_caisse'),
            data: {date: date},
            cache: false,
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
                    let filename ='caise_'+date+'.pdf';
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
                toastr.error('Pas de enregistrement');
            }
        });
    })


})