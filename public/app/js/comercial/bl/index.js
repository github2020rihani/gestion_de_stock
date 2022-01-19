$(document).ready(function () {


    $('.printBL').on('click', function () {
        var id_bl = $(this).data('id_bl');
        //appel ajx
        $.ajax({
            url: Routing.generate('perso_print_bl'),
            cache: false,
            data: {id_bl: id_bl},
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
                    let filename ='bl.pdf';
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



    $('.table_bl').on('click', '.printBL', function () {
        var id_bl = $(this).data('id_bl');
        //appel ajx
        $.ajax({
            url: Routing.generate('perso_print_bl'),
            cache: false,
            data: {id_bl: id_bl},
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
                    let filename ='bl.pdf';
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