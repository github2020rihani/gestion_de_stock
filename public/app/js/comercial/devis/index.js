$(document).ready(function () {
    $('.table_devis').on('click', '.printDevis', function () {
        var id_devis = $(this).data('id_devis');
        //appel ajx
        $.ajax({
            url: Routing.generate('perso_print_devis'),
            cache: false,
            data: {id_devis: id_devis},
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
                    let filename ='devis.pdf';
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

})