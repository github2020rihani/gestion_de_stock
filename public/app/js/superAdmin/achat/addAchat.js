$(document).ready(function () {
    addLigneAchat();
    CheckedFodec();

    $('.addAchat').click(function () {

        if (countArticle == 0) {
            toastr.error('Aucun Article Ajouter');
            return false;
        }
        //validation form
        $('.fournisseur').each(function () {
            if ($(this).val() == "0" || $(this).val() == null) {
                error = true;

                toastr.error('Veuillez choisir un fournisseur');

                return true;
            }
        })
        $('.numero_achat').each(function () {
            if ($(this).val() == '') {
                error = true;

                toastr.error('Veuillez Entrer numéro d\'achat');

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

                toastr.error('Veuillez entre le PRIX.HT.NET');

                return true;

            }
        })
        $('.pventettc').each(function () {
            if ($(this).attr('value') == 0.000) {
                error = true;

                toastr.error('Veuillez entre le PRIX.VENTE.TTC');
                return true;
            }
        })
        if (error) {
            return false;
        } else {
            $(this).hide();
            $('.formAddAchat').submit();

        }

    })
});


function addLigneAchat() {
    $('.addLingeAchat').click(function () {
        countArticle++;

        var index = ($('.ligne_achat').length);
        var contentListArticle = '';
        index++;
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
                                                    <button class="btn btn-danger mr-2 delete_ligneAchat_${index}" data-toggle="tooltip" type="button"
                                                       data-placement="top" title="" data-original-title="Supprimer"><i
                                                                class="fa fa-trash"></i></button>
                                                                </th>
                                                <td>
                                                    <select class="js-example-basic-single selectArticle_${index} article" name="article[]">
                                                        <option value="0" selected readonly>Coisir un article</option>
                                                       ${contentListArticle}

                                                    </select>
                                                </td>
                                                <td class="descriptionarticle_${index}"></td>
                                                <td>
                                                    <input type="text" name="puhtnet[]" data-index = "${index}" value="0.000" class="form-control  puhtnet puhtnet_${index}">
                                                </td>
                                                <td>
                                                    <input type="text" min="1" name="qte[]"  data-index = "${index}" value="1" class="form-control qte qte_${index}">

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
                        const indexArticle = selectAricle.indexOf($('.selectArticle_' + index).val());
                        if (indexArticle > -1) {
                            selectAricle.splice(indexArticle, 1);
                        }

                        var totalHtOld = parseFloat($('.total_ht').text());
                        if ($('input.fodec').is(':checked')) {
                            resTotalHt = (parseFloat(($('.puhtnet_' + index).val()) * parseInt($('.qte_' + (index)).val())) * 0.99).toFixed(3);
                        } else {
                            resTotalHt = (parseFloat(($('.puhtnet_' + index).val()) * parseInt($('.qte_' + (index)).val()))).toFixed(3);
                        }

                        var totalHtNew = (totalHtOld - (parseFloat(resTotalHt)).toFixed(3)).toFixed(3);
                        var totalTVA = 0;
                        var totalTTC = 0;
                        var timbre = 0.600;
                        var remise = parseFloat($('.remise').text());
                        var transport = parseFloat($('.transport').text());

                        totalTVA = (totalHtNew * 1.19);
                        totalTTC = parseFloat(totalHtNew + totalTVA + timbre + remise + transport).toFixed(3);
                        $('.total_ht').text(totalHtNew)
                        $('.total_tva').text(totalTVA.toFixed(3))
                        $('.total_ttc').text(totalTTC)


                        $(this).parent().parent().remove();


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

var puttc = 0
var marge = 0
var resTotalHt = 0
var selectAricle = [];
var countArticle = 0;
var error = false;

function selectArticle(index) {
    $('.selectArticle_' + index).change(function () {
        error = false;
        if (selectAricle.includes($(this).val())) {
            toastr.error('cet article a été choisir , veuillez choisir un autre');

            //check calculer
            var totalHtOld = parseFloat($('.total_ht').text());
            if ($('input.fodec').is(':checked')) {
                resTotalHt = (parseFloat(($('.puhtnet_' + index).val()) * parseInt($('.qte_' + (index)).val())) * 0.99).toFixed(3);
            } else {
                resTotalHt = (parseFloat(($('.puhtnet_' + index).val()) * parseInt($('.qte_' + (index)).val()))).toFixed(3);
            }

            var totalHtNew = (totalHtOld - (parseFloat(resTotalHt)).toFixed(3)).toFixed(3);
            var totalTVA = 0;
            var totalTTC = 0;
            var timbre = 0.600;
            var remise = parseFloat($('.remise').text());
            var transport = parseFloat($('.transport').text());

            totalTVA = (totalHtNew * 1.19);
            totalTTC = parseFloat(totalHtNew + totalTVA + timbre + remise + transport).toFixed(3);
            $('.total_ht').text(totalHtNew)
            $('.total_tva').text(totalTVA.toFixed(3))
            $('.total_ttc').text(totalTTC)









            $(this).parent().parent().remove();
            return false;
        }
        selectAricle.push($(this).val());
        console.log(selectAricle);

        $.ajax({
            url: Routing.generate('get_articles_byId'),
            type: "POST",
            data: {id_article: $(this).val()},
            success: function (data) {
                console.log(data)
                if (data) {
                    $('.descriptionarticle_' + index).text(data[0].description);

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

    $('.puhtnet').keyup("input", function (e) {
        $(this).attr('value', $(this).val())

        var puhtnet = parseFloat($(this).val());
        var index = $(this).data('index');
        var pventettc = $('.pventettc_' + index).val();
        puttc = (parseFloat($(this).val() * tva).toFixed(3));
        $('.puttc_' + index).val((puttc));
        console.log(pventettc);
        if (pventettc) {
            marge = (((pventettc - puttc) / puttc) * 100).toFixed(2);
            $('.marge_' + index).val(marge);
        }

        calculerTotal();


    })

}

function changePVenteTTC() {

    $('.pventettc').keyup("input", function (e) {

        $(this).attr('value', $(this).val())

        var index = $(this).data('index');

        puttc = $('.puttc_' + index).val();
        console.log(puttc);


        marge = ((($(this).val() - puttc) / puttc) * 100).toFixed(2);


        $('.marge_' + index).val(marge);
        calculerTotal();

    })

}

function changeQte() {
    $('.qte').keyup("input", function (e) {
        $(this).attr('value', $(this).val())
        var index = $(this).data('index');
        calculerTotal();

    })
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

function calculerTotal() {
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
        totalHt += (parseFloat($(this).val()) * parseInt($('.qte_' + (index + 1)).val()));
    })
    if ($('input.fodec').is(':checked')) {

        totalHtNew = parseFloat((totalHt * 0.99)).toFixed(3);
    } else {

        totalHtNew = totalHt;

    }

    totalTVA = (totalHtNew * 1.19);
    totalTTC = (totalHtNew + totalTVA + timbre + remise + transport);
    $('.total_ht').text(totalHtNew)
    $('.total_tva').text(totalTVA.toFixed(3))
    $('.total_ttc').text(totalTTC.toFixed(3))
}