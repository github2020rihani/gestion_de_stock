$(document).ready(function () {

    addLingeArticleBl();
    saveInvoice();
    changeTypePayement();
})
var countArticle = 0;
var selectAricle = [];
var articleToDelete = [];

var error = false;
var index;
var lingArt = [];

function addLingeArticleBl() {
    $('.addLingeBL').click(function () {
        countArticle++;
        var contentListArticle = '';
        lingArt.push(1);
        index = lingArt.length;
        $.ajax({
            url: Routing.generate('api_get_articles_from_prix'),
            type: "POST",
            success: function (data) {
                if (data) {
                    for (var i = 0; i < data.length; i++) {
                        contentListArticle += `<option value="${data[i]['article']['id']}">${data[i]['article']['ref']}</option>`;
                        pvente = data[i].puVenteTTC.toFixed(3);


                    }
                    //appel function ajax get articles
                    $('.tbodyLingeArticleBL').append(`
       <tr class="ligne_article">
                                                <td scope="row">
                                                    <button class="btn btn-danger mr-2 delete_ligneArticle_${index}" data-index="${index}"  data-toggle="tooltip" type="button"
                                                       data-placement="top" title="" data-original-title="Supprimer"><i
                                                                class="fa fa-trash"></i></button>
                                                                </td>
                                                <td>
                                                    <select class="js-example-basic-single selectArticle selectArticle_${index} article" data-index="${index}" name="article[]">
                                                        <option value="0" selected readonly>Coisir un article</option>
                                                       ${contentListArticle}

                                                    </select>
                                                </td>
                                                     <td>
                                                    <input type="text" name="puht" data-index = "${index}" value="0.000" class="form-control  puht puht_${index}"  readonly>
                                                </td>
                                                <td>
                                                    <input type="text" min="1" name="qte[]" pattern="[0-9]"  data-index = "${index}" value="" class="form-control qte qte_${index}"  >
                                                </td>
                                                <td> 
                                                <input  disabled class="form-control stock stock_${index}">
                                                </td>
                                                <td>
                                                    <input type="text" value="" name="remise[]" data-index = "${index}"  class="form-control remise remise_${index}" >
                                                    <input type="hidden" name="totalht[]" value="0.000" data-index = "${index}" class="form-control totalht totalht_${index}"  readonly>

                                                </td>
                                                <td>
                                                    <input type="text" name="puhtnet" data-index = "${index}" value="0.000" class="form-control  puhtnet puhtnet_${index}" readonly  >
                                                </td>
                                        
                                            
                                                   <td>
                                                    <input type="text" name="puttc[]" value="0.000" data-index = "${index}" class="form-control puttc puttc_${index}" readonly >

                                                </td>   
                                                  <td>
                                                    <input type="text" name="totalttc[]" value="0.000" data-index = "${index}" class="form-control totalttc totalttc_${index}" readonly >

                                                </td>    
                                                                                 
                                                                                        </tr>`)

                    // console.log('second'+     k);


                    $('.js-example-basic-single').select2();
                    //remove ligne achat
                    $(".delete_ligneArticle_" + index).click(function (event) {
                        countArticle--;
                        var totalHtGlobal = 0;
                        var totalTTCGlobal = 0;
                        $('.total_ht_global').text(0.000);

                        const indexArticle = selectAricle.indexOf(parseInt($(this).data('index')));
                        if (indexArticle > -1) {
                            selectAricle.splice(indexArticle, 1);
                        }
                        $(this).parent().parent().remove();


                        //total ht global
                        $('.totalht').each(function () {
                            totalHtGlobal = totalHtGlobal + parseFloat($(this).val());
                            $('.total_ht_global').text((totalHtGlobal).toFixed(3))
                        })

                        sommeTotalRem();
                        var remises = parseInt($('.remise_tot').text());
                        if (remises !=  0) {
                            NewTotaolHt = parseFloat(totalHtGlobal) - ((parseFloat(totalHtGlobal * remises) / 100)) ;
                            $('.total_ht_global').text(NewTotaolHt);
                        }

                        //totalttcglobal

                        totalTTCGlobal = parseFloat(NewTotaolHt) + 0.19+0.600;
                        $('.total_ttc_global').text(parseFloat(totalTTCGlobal).toFixed(3));


                    });
                    //select article
                    selectArticleBl(index);

                    changeQteArtBl();
                    changeRemArtBl();



                }
            },
            error: function () {
                alert('something wrong')
            }
        })


    })

}

function changeQteArtBl() {
    $('.qte').blur("input", function (e) {
        var index = $(this).data('index');
        var totalTTC = 0;
        var puttc = 0;
        var totalHt = 0;
        var totalHtGlobal = 0;
        var totalTTCGlobal = 0;
        $('.remise_'+index).val(0);


        if (parseInt(($(this).val())) > parseInt(($('.stock_' + index).val()))) {
            toastr.error('La quatité est depassé le stock');
            $(this).val('');
            return false;
        }

        $(this).attr('value', $(this).val());
        totalHt = parseFloat($(this).val()) * parseFloat($('.puhtnet_' + index).val());
        puttc = parseFloat(1.19) * parseFloat($('.puhtnet_' + index).val());
        totalTTC = parseFloat((puttc).toFixed(3)) * parseFloat(($(this).val()));
        $('.totalht_' + index).val((totalHt).toFixed(3));
        $('.puttc_' + index).val((puttc).toFixed(3));
        $('.totalttc_' + index).val((totalTTC).toFixed(3));

        //total ht global
        $('.totalht').each(function () {
            totalHtGlobal = totalHtGlobal + parseFloat($(this).val());
            $('.total_ht_global').text((totalHtGlobal).toFixed(3))
        })

        //totalttcglobal
        totalTTCGlobal = parseFloat(totalHtGlobal) + 0.19+0.600;
        $('.total_ttc_global').text(parseFloat(totalTTCGlobal).toFixed(3));

    })
}
var TOTREM= 0 ;
var NewTotaolHt= 0 ;
function selectArticleBl(index) {
    $('.selectArticle_' + index).change(function () {

        error = false;
        $('.qte_' + index).attr('readonly', false);
        var articleExiste = 0;
        var art = $(this).val();


        $('.selectArticle').each(function () {
            if ($(this).val() == art) {
                articleExiste ++;
            }
        })
        if (parseInt(articleExiste) >= 2){
            toastr.error('cet article a été choisir , veuillez choisir un autre');
            $(this).parent().parent().remove();

            var totalHtGlobal = 0;
            var totalTTCGlobal = 0;

            //total ht global
            $('.totalht').each(function () {
                totalHtGlobal = totalHtGlobal + parseFloat($(this).val());
                $('.total_ht_global').text((totalHtGlobal).toFixed(3))
            })
            sommeTotalRem();

            var remises = parseInt($('.remise_tot').text());
            if (remises !=  0) {
                NewTotaolHt = parseFloat(totalHtGlobal) - ((parseFloat(totalHtGlobal * remises) / 100)) ;
                $('.total_ht_global').text(NewTotaolHt);
            }

            //totalttcglobal
            totalTTCGlobal = parseFloat(NewTotaolHt) + 0.19+0.600;
            $('.total_ttc_global').text(parseFloat(totalTTCGlobal).toFixed(3));

            return false;
        }

        $.ajax({
            url: Routing.generate('perso_get_articles_byId'),
            type: "POST",
            data: {id_article: $(this).val()},
            success: function (data) {
                if (data) {
                    var totalHtGlobal = 0;
                    var totalTTCGlobal = 0;
                    $('.puht_' + index).val((data[0].puVenteHT));
                    $('.stock_' + index).val((data[0].qte));
                    $('.remise_' + index).val(data[0].article.remise);
                    $('.puhtnet_' + index).val((data[0].puVenteHT));
                    $('.delete_ligneArticle_'+index).attr('data-id_art',data[0].article.id )
                    $('.totalht_' +  index).val(0.000);
                    $('.puttc_' +  index).val(0.000);
                    $('.totalttc_' +  index).val(0.000);
                    $('.qte_' + index).val(0);
                    $('.totalht_' + index).attr('data-id_art',  data[0].article.id);
                    $('.puttc_' + index).attr('data-id_art',  data[0].article.id);
                    $('.totalttc_' + index).attr('data-id_art',  data[0].article.id);
                    $('.qte_' + index).attr('data-id_art',  data[0].article.id);

                    //total ht global
                    $('.totalht').each(function () {
                        totalHtGlobal = totalHtGlobal + parseFloat($(this).val());
                        $('.total_ht_global').text((totalHtGlobal).toFixed(3))
                    })
                    var remises = parseInt($('.remise_tot').text());
                    if (remises !=  0) {
                        NewTotaolHt = parseFloat(totalHtGlobal) - ((parseFloat(totalHtGlobal * remises) / 100)) ;
                        $('.total_ht_global').text(NewTotaolHt);
                    }
                    //totalttcglobal
                    // totalTTCGlobal = parseFloat(totalHtGlobal) + 0.19+0.600;
                    // $('.total_ttc_global').text(parseFloat(totalTTCGlobal).toFixed(3));

                    totalTTCGlobal = parseFloat(NewTotaolHt) + 0.19+0.600;
                    $('.total_ttc_global').text(parseFloat(totalTTCGlobal).toFixed(3));

                }

            },
            error: function () {
                alert('something wrong')
            }
        })

    })


}
function changeRemArtBl() {
    $('.remise').blur("input", function (e) {
        var index = $(this).data('index');



        var totalHtGlobal = 0;
        var totalTTCGlobal = 0;

        //total ht global
        $('.totalht').each(function () {
            totalHtGlobal = totalHtGlobal + parseFloat($('.totalht').val());
            $('.total_ht_global').text((totalHtGlobal).toFixed(3))
        })
        sommeTotalRem();

        var remises = parseInt($('.remise_tot').text());
        if (remises !=  0) {
            NewTotaolHt = parseFloat(totalHtGlobal) - ((parseFloat(totalHtGlobal * remises) / 100)) ;
            $('.total_ht_global').text(NewTotaolHt);
        }

        //totalttcglobal
        totalTTCGlobal = parseFloat(NewTotaolHt) + 0.19+0.600;
        $('.total_ttc_global').text(parseFloat(totalTTCGlobal).toFixed(3));

    })
}

function sommeTotalRem() {
    var totRem = 0 ;

    $('.remise').each(function () {
        totRem = totRem + (parseFloat($(this).val()));
    })
    $('.remise_tot').text(totRem);

}
function saveInvoice() {
    $('.addBL').click(function () {

        var type_payement = $('.type_payement').val();

        $('.customers').each(function () {
            if ($(this).val() == "0" || $(this).val() == null) {
                error = true;

                toastr.error('Veuillez choisir un client');

                return true;
            }
        })

        // if (selectAricle.length == 0) {
        //     toastr.error('Aucun Article Ajouter')
        //     error = true;
        //     return true;
        // }

        $('.article').each(function () {
            if ($(this).val() == 0) {
                error = true;

                toastr.error('Veuillez Choisir un article');

                return true;
            }
        })
        $('.qte').each(function () {
            if ($(this).val() == '') {
                error = true;

                toastr.error('Veuillez entrer le quantité');

                return true;
            }
        })
        // if (type_payement == null) {
        //     error = true;
        //
        //     toastr.error('Veuillez choisir le type de payement le quantité');
        //     return true;
        // }

        if (error) {
            return false;
        } else {
            $(this).hide();
            $('.formAddBL').submit();



        }
    })
}

function changeTypePayement() {
    $('.type_payement').change(function () {
        error = false;
        $('.typePayement').val($(this).val())
    })
}
