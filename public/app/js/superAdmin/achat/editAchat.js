$(document).ready(function () {
    var totalHt = 0;
    var totalTVA = 0;
    var timbre = 0.600;
    var totalTTC = 0;
    var qte = 0;
    var url = window.location.href;
    var id = url.substring(url.lastIndexOf('/') + 1);
    var error = false;

    getArticlesAchat(id);
    removeArticle();
    changePUHTNET();
    changePVenteTTC();
    addLigneAchat();
    CheckedFodec();
    selectArticleInitialAchat();

    $('.editAchat').click(function () {
        if ($('.ligne_achat').length == 0) {
            toastr.error('Aucun Article Ajouter');
            return false;
        }

        $('.numero_achat').each(function () {
            if ($(this).val() == '') {
                error = true;

                toastr.error('Veuillez Entrer numéro d\'achat');

                return true;
            }
        })
        $('.date_creation').each(function () {
            if ($(this).val() == '') {
                error = true;

                toastr.error('Veuillez Entrer la date du création');

                return true;
            }
        })
        $('.article').each(function () {
            if ($(this).val() == 0) {
                error = true;

                toastr.error('Veuillez Choisir un article');

                return true;
            }
        })
        $('.puhtnet').each(function () {
            if ($(this).attr('value') == 0.000) {
                error = true;

                toastr.error('Veuillez remplir tous les champs puhtnet');

                return true;

            }
        })
        $('.pventettc').each(function () {
            if ($(this).attr('value') == 0.000) {
                error = true;

                toastr.error('Veuillez remplir tous les champs pventettc');
                return true;
            }
        })
        if (error) {
            return false;
        } else {
            $(this).hide();
            $('.formEditAchat').submit();

        }

    })
});
var puttc = 0
var marge = 0
var selectAricle = [];
var articleToDelete = [];
var resTotalHt = 0;
var countArticle = 0;
var index;
var lingArt = []

function removeArticle() {
    $(".delete_ligneAchat").click(function (event) {
        var index = $(this).data('index');

        $(this).parent().parent().remove();

        var totalHt = 0;
        var totalTVA = 0;
        var timbre = 0.600;
        var remise = parseFloat($('.remise').text());
        var transport = parseFloat($('.transport').text());
        var totalTTC = 0;
        var qte = 0
        var sommepuhtnet = 0;
        var totalHtNew = 0;
        //calculer totalHT totaltva totalttc
        $('.puhtnet').each(function (index) {
            totalHt += (parseFloat($(this).val()) * parseInt($('.qte_' + ($(this).data('index'))).val()));
        })
        if ($('input.fodec').is(':checked')) {

            totalHtNew = parseFloat((totalHt * 0.99)).toFixed(3);
        } else {

            totalHtNew = parseFloat(totalHt).toFixed(3);

        }
        totalTVA = (totalHtNew * 1.19);
        totalTTC = (totalHtNew + totalTVA + timbre + remise + transport);
        $('.total_ht').text(totalHtNew)
        $('.total_tva').text(totalTVA.toFixed(3))
        $('.total_ttc').text(totalTTC.toFixed(3));
        return false;
    });
}

function getArticlesAchat(id) {
    $.ajax({
        url: Routing.generate('get_articles_achat'),
        type: "get",
        data: {id_achat: id},
        success: function (data) {
            if (data) {
                selectAricle.push((data));
                for (var k = 0; k < data.length; k++) {
                    lingArt.push(data[k]);

                }
                console.log('initial select checked---- '  + selectAricle[0])

            }

        },
        error: function () {
            alert('something wrong')
        }
    })
}
;
function addLigneAchat() {
    $('.addLingeAchat').click(function () {
        countArticle++;

        lingArt.push(1);
        index = lingArt.length;
        var contentListArticle = '';
        $.ajax({
            url: Routing.generate('get_articles'),
            type: "POST",
            success: function (data) {
                if (data) {

                    for (var i = 0; i < data.length; i++) {

                        contentListArticle += `<option value="${data[i]['id']}">${data[i]['ref']}</option>`;
                    }
                    //appel function ajax get articles
                    $('.tbodyLingeAchat').append(`
       <tr class="ligne_achat">
                                                <th scope="row">
                                                  
                                                                    <button class="btn btn-danger mr-2 delete_ligneAchat_${index}" data-toggle="tooltip" type="button" data-index = "${index}"
                                                       data-placement="top" title="" data-original-title="Supprimer"><i
                                                                class="fa fa-trash"></i></button>
                                                                </th>
                                                <td>
                                                    <select class="js-example-basic-single article selectArticle selectArticle_${index}" name="article[]">
                                                        <option value="0" selected readonly>Coisir un article</option>
                                                       ${contentListArticle}

                                                    </select>
                                                </td>
                                                <td class="descriptionarticle_${index}"></td>
                                                <td>
                                                    <input type="text" name="puhtnet[]" data-index = "${index}" value="0.000" class="form-control puhtnet puhtnet_${index}">
                                                </td>
                                                <td>
                                                    <input type="text" min="1" name="qte[]" data-index = "${index}" value="1" class="form-control  qte qte_${index}">

                                                </td>
                                                <td>
                                                    <input type="text" value="19.0" name="tva[]" data-index = "${index}"  class="form-control tva tva_${index}" readonly>

                                                </td>
                                                <td>
                                                    <input type="text" name="puttc[]" value="0.000" data-index = "${index}" class="form-control puttc puttc_${index}" readonly>

                                                </td>
                                                <td>
                                                    <input type="text" name="marge[]" value="0.000" data-index = "${index}" class="form-control marge marge_${index}" readonly>

                                                </td>
                                                <td>
                                                    <input type="text" name="pventettc[]" value="0.000" data-index = "${index}" class="form-control pventettc pventettc_${index}">

                                                </td>
                                            </tr>`);
                    $('.js-example-basic-single').select2();
                    //remove ligne achat
                    $(".delete_ligneAchat_" + index).click(function (event) {
                        $(this).parent().parent().remove();

                        var totalHt = 0;
                        var totalTVA = 0;
                        var timbre = 0.600;
                        var remise = parseFloat($('.remise').text());
                        var transport = parseFloat($('.transport').text());
                        var totalTTC = 0;
                        var qte = 0
                        var sommepuhtnet = 0;
                        var totalHtNew = 0;
                        //calculer totalHT totaltva totalttc
                        $('.puhtnet').each(function (index) {
                            totalHt += (parseFloat($(this).val()) * parseInt($('.qte_' + ($(this).data('index'))).val()));
                        })
                        if ($('input.fodec').is(':checked')) {

                            totalHtNew = parseFloat((totalHt * 0.99)).toFixed(3);
                        } else {

                            totalHtNew = parseFloat(totalHt).toFixed(3);

                        }
                        totalTVA = (totalHtNew * 1.19);
                        totalTTC = (totalHtNew + totalTVA + timbre + remise + transport);
                        $('.total_ht').text(totalHtNew)
                        $('.total_tva').text(totalTVA.toFixed(3))
                        $('.total_ttc').text(totalTTC.toFixed(3));
                        return false;
                    });
                    //select article
                    selectArticle(index);
                    //changePUHTnET
                    changePUHTNET();
                    changePVenteTTC();
                    changeQte();

                }

            },
            error: function () {
                alert('something wrong')
            }
        })


    })

}

function selectArticle(index) {
    $('.selectArticle_' + index).change(function () {
        error = false;
        var articleExiste = 0;
        var art = $(this).val();
        $('.selectArticle').each(function () {
            if ($(this).val() == art) {
                articleExiste ++;
            }
        })
        if (parseInt(articleExiste) >= 2){
            toastr.error('cet article a été choisir , veuillez choisir un autre');
            //check calculer
            $(this).parent().parent().remove();

            var totalHt = 0;
            var totalTVA = 0;
            var timbre = 0.600;
            var remise = parseFloat($('.remise').text());
            var transport = parseFloat($('.transport').text());
            var totalTTC = 0;
            var qte = 0
            var sommepuhtnet = 0;
            var totalHtNew = 0;
            //calculer totalHT totaltva totalttc
            $('.puhtnet').each(function (index) {
                totalHt += (parseFloat($(this).val()) * parseInt($('.qte_' + ($(this).data('index'))).val()));
            })
            if ($('input.fodec').is(':checked')) {

                totalHtNew = parseFloat((totalHt * 0.99)).toFixed(3);
            } else {

                totalHtNew = parseFloat(totalHt).toFixed(3);

            }
            totalTVA = (totalHtNew * 1.19);
            totalTTC = (totalHtNew + totalTVA + timbre + remise + transport);
            $('.total_ht').text(totalHtNew)
            $('.total_tva').text(totalTVA.toFixed(3))
            $('.total_ttc').text(totalTTC.toFixed(3));
            return false;
        }


        $.ajax({
            url: Routing.generate('get_articles_byId'),
            type: "POST",
            data: {id_article: $(this).val()},
            success: function (data) {
                if (data) {
                    $('.descriptionarticle_' + index).text(data[0].description);
                    ('.puhtnet' +  index).val(0.000);
                    $('.qte' +  index).val(0);
                    $('.puttc_' +  index).val(0.000);
                    $('.marge' + index).val(0.000);
                    $('.pventettc ' + index).val(0.000);


                    var totalHt = 0;
                    var totalTVA = 0;
                    var timbre = 0.600;
                    var remise = parseFloat($('.remise').text());
                    var transport = parseFloat($('.transport').text());
                    var totalTTC = 0;
                    var qte = 0
                    var sommepuhtnet = 0;
                    var totalHtNew = 0;
                    //calculer totalHT totaltva totalttc
                    $('.puhtnet').each(function (index) {
                        totalHt += (parseFloat($(this).val()) * parseInt($('.qte_' + ($(this).data('index'))).val()));
                    })
                    if ($('input.fodec').is(':checked')) {

                        totalHtNew = parseFloat((totalHt * 0.99)).toFixed(3);
                    } else {

                        totalHtNew = parseFloat(totalHt).toFixed(3);

                    }
                    totalTVA = (totalHtNew * 1.19);
                    totalTTC = (totalHtNew + totalTVA + timbre + remise + transport);
                    $('.total_ht').text(totalHtNew)
                    $('.total_tva').text(totalTVA.toFixed(3))
                    $('.total_ttc').text(totalTTC.toFixed(3));
                    return false;

                }

            },
            error: function () {
                alert('something wrong')
            }
        })

    })


}

function changePUHTNET() {
    var tva = 1.19;
    puttc = 0;

    $('.puhtnet').blur("input", function (e) {
        $(this).attr('value', $(this).val())
        var index = $(this).data('index');
        var pventettc = $('.pventettc_' + index).val();
        puttc = (parseFloat($(this).val() * tva).toFixed(3));
        $('.puttc_' + index).val((puttc));
        if (pventettc) {
            marge = (((pventettc - puttc) / puttc) * 100).toFixed(2);
            $('.marge_' + index).val(marge);
        }
        calculerTotal(index);

    })

}

function changePVenteTTC() {

    $('.pventettc').blur("input", function (e) {
        $(this).attr('value', $(this).val())

        var index = $(this).data('index');

        puttc = $('.puttc_' + index).val();


        marge = ((($(this).val() - puttc) / puttc) * 100).toFixed(2);


        $('.marge_' + index).val(marge);
        calculerTotal(index);

    })

}

function changeQte() {
    $('.qte').keyup("input", function (e) {
        $(this).attr('value', $(this).val())
        var index = $(this).data('index');
        calculerTotal(index);

    })
}


function calculerTotal(index) {
    var totalHt = 0;
    var totalTVA = 0;
    var timbre = 0.600;
    var remise = parseFloat($('.remise').text());
    var transport = parseFloat($('.transport').text());
    var totalTTC = 0;
    var qte = 0
    var sommepuhtnet = 0;
    var totalHtNew = 0;
    //calculer totalHT totaltva totalttc
    $('.puhtnet').each(function (index) {
        totalHt += (parseFloat($(this).val()) * parseInt($('.qte_' + ($(this).data('index'))).val()));
    })
    if ($('input.fodec').is(':checked')) {

        totalHtNew = parseFloat((totalHt * 0.99)).toFixed(3);
    } else {

        totalHtNew = parseFloat(totalHt).toFixed(3);

    }

    totalTVA = (totalHtNew * 1.19);
    totalTTC = (totalHtNew + totalTVA + timbre + remise + transport);
    $('.total_ht').text(totalHtNew)
    $('.total_tva').text(totalTVA.toFixed(3))
    $('.total_ttc').text(totalTTC.toFixed(3))
}

function CheckedFodec() {


    $('input.fodec').change(function () {
            var totalTVA = 0;
            var totalTTC = 0;
            var totalHtNew = 0;
            var timbre = 0.600;
            $(this).attr('value', true);
            var remise = parseFloat($('.remise').text());
            var transport = parseFloat($('.transport').text());
            var totalHtOld = parseFloat($('.total_ht').text());
            if (parseFloat(totalHtOld) == 0) {
                toastr.info('Veuiller entrer tous les prix et les quantité');
                return false;
            }

            if ($('input.fodec').is(':checked')) {
                $(this).attr('value', true);
                $('.fodecChecked').val(true);


                totalHtNew = parseFloat((totalHtOld * 0.99)).toFixed(3);
            } else {
                $('.fodecChecked').val(false);


                totalHtNew = parseFloat((totalHtOld / 0.99)).toFixed(3);

            }
            totalTVA = (totalHtNew * 1.19);

            totalTTC = parseFloat(parseFloat(totalHtNew) + totalTVA + timbre + remise + transport).toFixed(3);
            $('.total_ht').text(totalHtNew)
            $('.total_tva').text(totalTVA.toFixed(3))
            $('.total_ttc').text(totalTTC);

        }
    )


}

var indexArt;
var OldArt;

function selectArticleInitialAchat() {
    $('.selectArticle').change(function () {
        error = false;

        var articleExiste = 0;
        var art = $(this).val();
        indexArt = parseInt($(this).data('id_index'));
        //delete from select article
        $('.selectArticle').each(function () {
            if ($(this).val() == art) {
                articleExiste ++;
            }
        })
        if (parseInt(articleExiste) >= 2){
            toastr.error('cet article a été choisir , veuillez choisir un autre');
            $(this).parent().parent().remove();

            var totalHt = 0;
            var totalTVA = 0;
            var timbre = 0.600;
            var remise = parseFloat($('.remise').text());
            var transport = parseFloat($('.transport').text());
            var totalTTC = 0;
            var qte = 0
            var sommepuhtnet = 0;
            var totalHtNew = 0;
            //calculer totalHT totaltva totalttc
            $('.puhtnet').each(function (index) {
                totalHt += (parseFloat($(this).val()) * parseInt($('.qte_' + ($(this).data('index'))).val()));
            })
            if ($('input.fodec').is(':checked')) {

                totalHtNew = parseFloat((totalHt * 0.99)).toFixed(3);
            } else {

                totalHtNew = parseFloat(totalHt).toFixed(3);

            }
            totalTVA = (totalHtNew * 1.19);
            totalTTC = (totalHtNew + totalTVA + timbre + remise + transport);
            $('.total_ht').text(totalHtNew)
            $('.total_tva').text(totalTVA.toFixed(3))
            $('.total_ttc').text(totalTTC.toFixed(3));
            return false;
            }



        $.ajax({
            url: Routing.generate('get_articles_byId'),
            type: "POST",
            data: {id_article: $(this).val()},
            success: function (data) {
                if (data) {
                    console.log(data[0].description)
                    $('.descriptionarticle_' + indexArt).text(data[0].description);
                    ('.puhtnetArt_' +  indexArt).val(0.000);
                    $('.qteArt_' +  indexArt).val(0);
                    $('.puttcArt_' +  indexArt).val(0.000);
                    $('.margeArt_' + indexArt).val(0.000);
                    $('.pventettcArt_ ' + indexArt).val(0.000);


                    var totalHt = 0;
                    var totalTVA = 0;
                    var timbre = 0.600;
                    var remise = parseFloat($('.remise').text());
                    var transport = parseFloat($('.transport').text());
                    var totalTTC = 0;
                    var qte = 0
                    var sommepuhtnet = 0;
                    var totalHtNew = 0;
                    //calculer totalHT totaltva totalttc
                    $('.puhtnet').each(function (index) {
                        totalHt += (parseFloat($(this).val()) * parseInt($('.qte_' + ($(this).data('index'))).val()));
                    })
                    if ($('input.fodec').is(':checked')) {

                        totalHtNew = parseFloat((totalHt * 0.99)).toFixed(3);
                    } else {

                        totalHtNew = parseFloat(totalHt).toFixed(3);

                    }
                    totalTVA = (totalHtNew * 1.19);
                    totalTTC = (totalHtNew + totalTVA + timbre + remise + transport);
                    $('.total_ht').text(totalHtNew)
                    $('.total_tva').text(totalTVA.toFixed(3))
                    $('.total_ttc').text(totalTTC.toFixed(3));
                    return false;

                }

            },
            error: function () {
                alert('something wrong')
            }
        })

    })


}




