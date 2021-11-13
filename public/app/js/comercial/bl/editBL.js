$(document).ready(function () {
    var url = window.location.href;
    var id = url.substring(url.lastIndexOf('/') + 1);
    //functionInitial
    getArticlesBL(id);
    selectArticleBlEditInitial();
    removeArticleInitial();
    changeQteArtBlInitial();

    //function charge dom
    /*add linge article */
    addLingeArticleBlEdit();
    changeTypePayement();
    updateBL();
})
var countArticle = 0;
var selectAricle = [];
var articleToDelete = [];

var error = false;
var indexOfSelect;
var index;
var lingArt = [];
var old_article;

function addLingeArticleBlEdit() {
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
                                                    <select class="js-example-basic-single  selectArticle selectArticle_${index} article" data-index="${index}" name="article[]">
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
                                                    <input type="text" value="" name="remise" data-index = "${index}"  class="form-control remise remise_${index}" readonly>

                                                </td>
                                                <td>
                                                    <input type="text" name="puhtnet" data-index = "${index}" value="0.000" class="form-control  puhtnet puhtnet_${index}" readonly  >
                                                </td>
                                        
                                                <td>
                                                    <input type="text" name="totalht[]" value="0.000" data-index = "${index}" class="form-control totalht totalht_${index}"  readonly>

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


                        const indexArticle = selectAricle[0].indexOf(parseInt($(this).data('index')));
                        if (indexArticle > -1) {
                            selectAricle[0].splice(indexArticle, 1);
                        }
                        $(this).parent().parent().remove();
                        //total ht global
                        $('.totalht').each(function () {
                            totalHtGlobal = totalHtGlobal + parseFloat($(this).val());
                            $('.total_ht_global').text((totalHtGlobal).toFixed(3))
                        })

                        //totalttcglobal
                        totalTTCGlobal = parseFloat(totalHtGlobal) + 0.19;
                        $('.total_ttc_global').text(parseFloat(totalTTCGlobal).toFixed(3));

                    });
                    //select article
                    selectArticleBl2(index);

                    changeQteArtBl2();


                }
            },
            error: function () {
                alert('something wrong')
            }
        })


    })

}

function changeQteArtBlInitial() {
    $('.qte').blur("input", function (e) {
        var index = $(this).data('index');
        var totalTTC = 0;
        var puttc = 0;
        var totalHt = 0;
        var totalHtGlobal = 0;
        var totalTTCGlobal = 0;

        if (($(this).val() > ($('.stock_' + index).val()))) {
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
        totalTTCGlobal = parseFloat(totalHtGlobal) + 0.19;
        $('.total_ttc_global').text(parseFloat(totalTTCGlobal).toFixed(3));

    })
}

function selectArticleBlEditInitial() {
    $('.selectArticle').change(function () {
        error = false;
        indexOfSelect = $(this).data('index');

        $('.qte_' + indexOfSelect).attr('readonly', false);
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

            //totalttcglobal
            totalTTCGlobal = parseFloat(totalHtGlobal) + 0.19;
            $('.total_ttc_global').text(parseFloat(totalTTCGlobal).toFixed(3));
            return false;
        }
//if new article  existe
//         if (selectAricle[0].includes(parseInt($(this).val()))) {
//
//             countArticle--;
//             toastr.error('cet article a été choisir , veuillez choisir un autre');
//             if (selectAricle[0].includes(parseInt(($(this).val())))) {
//                 const indexArticle = selectAricle[0].indexOf(parseInt($(this).val()));
//                 if (indexArticle > -1) {
//                     selectAricle[0].splice(indexArticle, 1);
//                 }
//                 articleToDelete.push(parseInt($(this).data('old_article')));
//                 $('.articleToDelete').val(articleToDelete);
//             }
//
//             $(this).parent().parent().remove();
//             //remove old article
//
//
//
//
//
//             //total ht global
//             $('.totalht').each(function () {
//                 totalHtGlobal = totalHtGlobal + parseFloat($(this).val());
//                 $('.total_ht_global').text((totalHtGlobal).toFixed(3))
//             })
//
//             //totalttcglobal
//             totalTTCGlobal = parseFloat(totalHtGlobal) + 0.19;
//             $('.total_ttc_global').text(parseFloat(totalTTCGlobal).toFixed(3));
//
//             console.log('initial select checked---- '  + selectAricle[0])
//
//         }
        // else {
        //     //new article not exist
        //
        //     if (!articleToDelete.includes(parseInt($(this).data('old_article')))) {
        //         articleToDelete.push(parseInt($(this).data('old_article')));
        //         $('.articleToDelete').val(articleToDelete);
        //     }
        //
        //     if (selectAricle[0].includes(parseInt($(this).data('old_article')))) {
        //         countArticle--;
        //         const indexArticle = selectAricle[0].indexOf(parseInt($(this).data('old_article')));
        //         if (indexArticle > -1) {
        //             selectAricle[0].splice(indexArticle, 1);
        //         }
        //     }
        //
        //     if (selectAricle[0].includes(parseInt($(this).val()))) {
        //         countArticle--;
        //         const indexArticle = selectAricle[0].indexOf(parseInt($(this).val()));
        //         if (indexArticle > -1) {
        //             selectAricle[0].splice(indexArticle, 1);
        //         }
        //     }else{
        //         selectAricle[0].push(parseInt($(this).val()));
        //
        //     }
        //
        //
        //     console.log(' select article delte afer change  not exist---- '  + articleToDelete)
        //     console.log(' select article  afer change  not exist---- '  + selectAricle)
        //         $.ajax({
        //         url: Routing.generate('perso_get_articles_byId'),
        //         type: "POST",
        //         data: {id_article: $(this).val()},
        //         success: function (data) {
        //             if (data) {
        //                 $('.puht_' + indexOfSelect).val((data[0].puVenteHT).toFixed(3));
        //                 $('.stock_' + indexOfSelect).val((data[0].qte));
        //                 $('.remise_' + indexOfSelect).val(data[0].article.remise);
        //                 $('.puhtnet_' + indexOfSelect).val((data[0].puVenteHT).toFixed(3));
        //                 $('.qte_' + indexOfSelect).val(0);
        //                 $('.qte_' + indexOfSelect).val(0);
        //                 $('.totalht_' + indexOfSelect).val(0.000);
        //                 $('.puttc_' + indexOfSelect).val(0.000);
        //                 $('.totalttc_' + indexOfSelect).val(0.000);
        //             }
        //
        //             if ($('.qte_' + indexOfSelect).val() == 0) {
        //                 //total ht global
        //                 $('.totalht').each(function () {
        //                     totalHtGlobal = totalHtGlobal + parseFloat($(this).val());
        //                     $('.total_ht_global').text((totalHtGlobal).toFixed(3))
        //                 })
        //
        //                 //totalttcglobal
        //                 totalTTCGlobal = parseFloat(totalHtGlobal) + 0.19;
        //                 $('.total_ttc_global').text(parseFloat(totalTTCGlobal).toFixed(3));
        //             }
        //
        //
        //         },
        //         error: function () {
        //             alert('something wrong')
        //         }
        //     })
        // }

        //remove old article
        // articleToDelete.push(parseInt($(this).data('old_article')));
        // $('.articleToDelete').val(articleToDelete);
        // console.log(' select article delte afer change exist---- '  + articleToDelete)



    })


}

function removeArticleInitial() {
    $('.delete_ligneArticle').click(function () {
        error = false;

        if (selectAricle[0].includes(parseInt($(this).data('old_article')))) {
            var totalHtGlobal = 0;
            var totalTTCGlobal = 0;
            countArticle--;
            const indexArticle = selectAricle[0].indexOf($(this).data('old_article'));
            if (indexArticle > -1) {
                selectAricle[0].splice(indexArticle, 1);
            }
            $(this).parent().parent().remove();

            //total ht global
            $('.totalht').each(function () {
                totalHtGlobal = totalHtGlobal + parseFloat($(this).val());
                $('.total_ht_global').text((totalHtGlobal).toFixed(3))
            })

            //totalttcglobal
            totalTTCGlobal = parseFloat(totalHtGlobal) + 0.19;
            $('.total_ttc_global').text(parseFloat(totalTTCGlobal).toFixed(3));

            articleToDelete.push(parseInt($(this).data('old_article')));
            $('.articleToDelete').val(articleToDelete);

        }
        console.log(' select checked after delete ---- '  + selectAricle[0])

    })


}

function updateBL() {
    $('.editBL').click(function () {

        var type_payement = $('.type_payement').val();

        $('.customers').each(function () {
            if ($(this).val() == "0" || $(this).val() == null) {
                error = true;

                toastr.error('Veuillez choisir un client');

                return true;
            }
        })

        if (selectAricle.length == 0) {
            toastr.error('Aucun Article Ajouter')
            error = true;
            return true;
        }

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
        if (type_payement == null) {
            error = true;

            toastr.error('Veuillez choisir le type de payement le quantité');
            return true;
        }

        if (error) {
            return false;
        } else {
            $(this).hide();
            $('.formEditBL').submit();


        }
    })
}

function changeTypePayement() {
    $('.type_payement').change(function () {
        error = false;
        $('.typePayement').val($(this).val())
    })
}

function getArticlesBL(id) {
    $.ajax({
        url: Routing.generate('perso_get_articles_bl'),
        type: "get",
        data: {id_bl: id},
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
function changeQteArtBl2() {
    $('.qte').blur("input", function (e) {
        var index = $(this).data('index');
        var totalTTC = 0;
        var puttc = 0;
        var totalHt = 0;
        var totalHtGlobal = 0;
        var totalTTCGlobal = 0;

        if (($(this).val() > ($('.stock_' + index).val()))) {
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
        totalTTCGlobal = parseFloat(totalHtGlobal) + 0.19;
        $('.total_ttc_global').text(parseFloat(totalTTCGlobal).toFixed(3));

    })
}

function selectArticleBl2(index) {
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
            $(this).parent().parent().remove();
            var totalHtGlobal = 0;
            var totalTTCGlobal = 0;

            //total ht global
            $('.totalht').each(function () {
                totalHtGlobal = totalHtGlobal + parseFloat($(this).val());
                $('.total_ht_global').text((totalHtGlobal).toFixed(3))
            })

            //totalttcglobal
            totalTTCGlobal = parseFloat(totalHtGlobal) + 0.19;
            $('.total_ttc_global').text(parseFloat(totalTTCGlobal).toFixed(3));
            return false;
        }

        $('.qte_' + index).attr('readonly', false);
        // if (selectAricle[0].includes(parseInt($(this).val()))) {
        //     var totalHtGlobal = 0;
        //     var totalTTCGlobal = 0;
        //     countArticle--;
        //     toastr.error('cet article a été choisir , veuillez choisir un autre');
        //     //
        //     // const indexArticle = selectAricle[0].indexOf(parseInt($(this).val()));
        //     // if (indexArticle > -1) {
        //     //     selectAricle[0].splice(indexArticle, 1);
        //     // }
        //     $(this).parent().parent().remove();
        //
        //     //total ht global
        //     $('.totalht').each(function () {
        //         totalHtGlobal = totalHtGlobal + parseFloat($(this).val());
        //         $('.total_ht_global').text((totalHtGlobal).toFixed(3))
        //     })
        //
        //     //totalttcglobal
        //     totalTTCGlobal = parseFloat(totalHtGlobal) + 0.19;
        //     $('.total_ttc_global').text(parseFloat(totalTTCGlobal).toFixed(3));
        //
        //     console.log(selectAricle[0]);
        //
        //     return false;
        // }
        // selectAricle[0].push(parseInt($(this).val()));
        // console.log(selectAricle[0]);

        $.ajax({
            url: Routing.generate('perso_get_articles_byId'),
            type: "POST",
            data: {id_article: $(this).val()},
            success: function (data) {
                if (data) {
                    console.log(data[0])
                    $('.puht_' + index).val((data[0].puVenteHT).toFixed(3));
                    $('.stock_' + index).val((data[0].qte));
                    $('.remise_' + index).val(data[0].article.remise);
                    $('.puhtnet_' + index).val((data[0].puVenteHT).toFixed(3));
                }

            },
            error: function () {
                alert('something wrong')
            }
        })

    })


}