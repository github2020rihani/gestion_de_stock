$(document).ready(function () {
    changeInventaire();


    $('.downloadInv').click(function () {
        var numInv =   $('.numInv').text();
        $.ajax({
            url: Routing.generate('imprime_inventaire'),
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
                    let filename =numInv+'.pdf';
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
        });


    })
    $('.downloadEcel').click(function () {
        var numInv =   $('.numInv').text();
        $.ajax({
            url: Routing.generate('download_excel_inventaire'),
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
                    let filename =numInv+'.xlsx';
                    // var pdfFile = new Blob([data], { type: "application/octetstream" });
                    var excelFile = new Blob([data], {type: "application/xlsx"});

                    let downloadLink = document.createElement("a");
                    downloadLink.download = filename;
                    downloadLink.href = url.createObjectURL(excelFile);
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                }else{
                    alert('something wrong')

                }


            },
            error: function () {
                alert('something wrong')

            }
        });


    })


})


function changeInventaire() {
    $('#selectNumInv').change(function () {
        var id_inv = $(this).val();
        $.ajax({
            url: Routing.generate('get_inventaire'),
            type: "post",
            data: {id_inv: id_inv},
            success: function (data) {
                if (data.status === 'true') {
                    $('#contentInv').html(data.content);


                } else {
                    toastr.error(data.message);
                }


            },
            error: function () {
                alert('something wrong')
            }
        })
    })

}