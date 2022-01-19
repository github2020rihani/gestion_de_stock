$(document).ready(function () {
    var url = window.location.href;
    var id = url.substring(url.lastIndexOf('/') + 1);

    $('.transfertDevis').hide();
    getArticlesDevis(id);
    removeArticle();
    addLingeArticle();
    changeQteInitial();
    selectArticleInitial();
    transfertDevis();

    $('.editDevis ').click(function () {


        //validation form
        $('.customers').each(function () {
            if ($(this).val() == "0" || $(this).val() == null) {
                error = true;

                toastr.error('Veuillez choisir un client');

                return true;
            }
        })

        // if (selectAricle.length == 0) {
        //     toastr.error('Aucun Article Ajouter');
        //     return false;
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


        if (error) {
            return false;
        } else {
            //getArticles
            $(this).hide();
            $('.formEditDevis').submit();
            $('.transfertDevis').show();


        }

    })
});

var index;
var lingArt = [];
function addLingeArticle() {
    $('.addLingneArticle').click(function () {
        countArticle++;

        lingArt.push(1);
        index = lingArt.length;
        var contentListArticle = '';
        $.ajax({
            url: Routing.generate('api_get_articles_from_prix_devis'),
            type: "POST",
            success: function (data) {
                console.log(data[0].qte);
                if (data) {
                    for (var i = 0; i < data.length; i++) {
                        contentListArticle += `<option value="${data[i]['article']['id']}">${data[i]['article']['ref']}</option>`;
                        pvente = data[i].puVenteTTC.toFixed(3);


                    }
                    //appel function ajax get articles
                    $('.tbodyLingeArticle').append(`
       <tr class="ligne_article">
                                                <th scope="row">
                                                    <button class="btn btn-danger mr-2 delete_ligneArticle_${index}" data-toggle="tooltip" type="button"
                                                       data-placement="top" title="" data-original-title="Supprimer"><i
                                                                class="fa fa-trash"></i></button>
                                                                </th>
                                                <td>
                                                    <select class="js-example-basic-single selectArticle selectArticle_${index} article" name="article[]">
                                                        <option value="0" selected readonly>Coisir un article</option>
                                                       ${contentListArticle}

                                                    </select>
                                                </td>
                                                <td class="descriptionarticle_${index}"></td>
                   <td>
                                                    <input type="text" name="stock" data-index = "${index}" value="" class="form-control  stock stock_${index}" readonly >
                                                </td>
                                                <td>
                                                    <input type="text" min="1" name="qte[]" pattern="[0-9]"  data-index = "${index}" value="" class="form-control qte qte_${index}" readonly>

                                                </td>
                                                <td>
                                                    <input type="text" value="" name="remise" data-index = "${index}"  class="form-control remise remise_${index}" readonly>

                                                </td>
                                                <td>
                                                    <input type="text" name="pventettc[]" value="" data-index = "${index}" class="form-control pventettc pventettc_${index}" readonly>

                                                </td>
                                                <td>
                                                    <input type="text" name="total[]" value="0.000" data-index = "${index}" class="form-control total total_${index}" readonly>

                                                </td>                                            </tr>`);


                    $('.js-example-basic-single').select2();
                    //remove ligne achat
                    $(".delete_ligneArticle_" + index).click(function (event) {

                        const indexArticle = selectAricle.indexOf(parseInt($('.selectArticle_' + index).val()));
                        if (indexArticle > -1) {
                            selectAricle.splice(indexArticle, 1);
                        }
                        $(this).parent().parent().remove();
                        var totalTTC = 0;

                        $('.total ').each(function () {
                            totalTTC = parseFloat(totalTTC) + parseFloat($(this).val());
                        })
                        $('.tottalTTC').val((totalTTC).toFixed(3))

                    });
                    //select article
                    selectArticle(index);

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
var articleToDelete = [];

var countArticle = 0;
var error = false;

function selectArticle(index) {
    $('.selectArticle_' + index).change(function () {
        error = false;
        var articleExiste = 0;
        var newTotalttc = 0;
        var art = $(this).val();


        $('.selectArticle').each(function () {
            if ($(this).val() == art) {
                articleExiste++;
            }
        })
        if (parseInt(articleExiste) >= 2) {
            toastr.error('cet article a été choisir , veuillez choisir un autre');
            $(this).parent().parent().remove();
            $('.total').each(function () {
                newTotalttc =  newTotalttc +parseFloat($(this).val());
                $('.tottalTTC').val(newTotalttc.toFixed(3));

            })
            return false;
        }
        $('.qte_' + index).attr('readonly', false);
        $.ajax({
            url: Routing.generate('perso_get_articles_byId'),
            type: "POST",
            data: {id_article: $(this).val()},
            success: function (data) {
                if (data) {
                    var newTotalttc = 0 ;
                    $('.descriptionarticle_' + index).text(data[0].article.description);
                    $('.stock_' + index).val(data[0].qte);
                    $('.remise_' + index).val(data[0].article.remise);
                    $('.pventettc_' + index).val((data[0].puVenteTTC).toFixed(3));
                    $('.total_' +  index).val(0.000);
                    $('.qte_' + index).val(1);

                    $('.pventettc_' + index).attr('data-id_art',  data[0].article.id);
                    $('.stock_' + index).attr('data-id_art',  data[0].article.id);
                    $('.total_' + index).attr('data-id_art',  data[0].article.id);
                    $('.qte_' + index).attr('data-id_art',  data[0].article.id);
                    $('.qte_' + index).attr('data-id_art',  data[0].article.id);
                    $('.total').each(function () {
                        newTotalttc =  newTotalttc +parseFloat($(this).val());
                        $('.tottalTTC').val(newTotalttc.toFixed(3));

                    })

                }

            },
            error: function () {
                alert('something wrong')
            }
        })


    })


}


function changeQte() {
    $('.qte').blur("input", function (e) {
        var index = $(this).data('index');
        console.log(index);
        var idart = $(this).data('id_art');
        var totalTTC = 0;
        var total = 0;
        // console.log($(this).val())
        // console.log($('.stock_' + idart).val())

        if (parseInt(($(this).val())) > parseInt(($('.stock_' + index).val()))) {
            toastr.error('la quatité est depasser le stock');
            $(this).val('');
            return false;
        }


        $(this).attr('value', $(this).val())
        total = (parseInt($(this).val()) * parseFloat($('.pventettc_' + index).val())).toFixed(3);
        $('.total_' + index).val(parseFloat(total).toFixed(3))
        $('.total ').each(function () {
            totalTTC = parseFloat(totalTTC) + parseFloat($(this).val());
        })
        $('.tottalTTC').val((totalTTC).toFixed(3))

    })
}

function changeQteInitial() {
    $('.qte').blur("input", function (e) {
        var index = $(this).data('index');
        console.log(index);
        var idart = $(this).data('id_art');
        var totalTTC = 0;
        var total = 0;
        // console.log($(this).val())
        // console.log($('.stock_' + idart).val())
        if ($(this).val() == '' ) {

            $('.stock_'+index).val(parseInt($('.stock_'+index).val()) + (parseInt($('.oldQteArt_'+index).val())))
            $('.oldQteArt_'+index).val(0);
        }

        if (parseInt(($(this).val())) > parseInt(($('.stock_' + index).val()))) {
            toastr.error('la quatité est depasser le stock');
            $(this).val('');
            return false;
        }


        $(this).attr('value', $(this).val())
        total = (parseInt($(this).val()) * parseFloat($('.pventettc_' + index).val())).toFixed(3);

        $('.total_' + index).val(parseFloat(total).toFixed(3))

        $('.total ').each(function () {
            totalTTC = parseFloat(totalTTC) + parseFloat($(this).val());
        })
        $('.tottalTTC').val((totalTTC).toFixed(3))

    })
}


function removeArticle() {
    var totalTTC = 0;
    $(".delete_ligneArticle").click(function (event) {
        var index = $(this).data('index');
        var id_art = $(this).data('id_article');
        const indexArticle = selectAricle.indexOf(parseInt($(this).data('id_article')));
        if (indexArticle > -1) {
            selectAricle.splice(indexArticle, 1);
        }
        articleToDelete.push($(this).data('id_article'));
        $(this).attr('data-original-title', '');
        $('.articleToDelete').val(articleToDelete);


        //update total ttc
        // $('.total').each(function () {
        //     totalTTC = parseFloat($('.tottalTTC').val()) - parseFloat($('.totalArt_' + id_art).val());
        // })
        // $('.tottalTTC').val((totalTTC).toFixed(3))

        $(this).parent().parent().remove();

        $('.total').each(function () {
            totalTTC =  totalTTC +parseFloat($(this).val());
            $('.tottalTTC').val(totalTTC.toFixed(3));

        })

    });
}


function getArticlesDevis(id) {
    $.ajax({
        url: Routing.generate('perso_get_articles_devis'),
        type: "get",
        data: {id_devis: id},
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


function transfertDevis() {
    $('.transfertDevis').click(function () {

        //validation form
        $('.customers').each(function () {
            if ($(this).val() == "0" || $(this).val() == null) {
                error = true;

                toastr.error('Veuillez choisir un client');

                return true;
            }
        })

        if (selectAricle.length == 0) {
            toastr.error('Aucun Article Ajouter');
            return false;
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


        if (error) {
            return false;
        } else {
            // $(this).hide();

        }
        var id_devis = $(this).data('id_devis');
        //show Modal
        $('#modalConfirmTransfer').modal('show');
        $('#confirme').attr('href', 'http://localhost:8000/personelle/BL/transfert/' + id_devis)


    })
}

var OldArt;

function selectArticleInitial() {

    $('.selectArticle').change(function () {
        var index = $(this).data('id_index');
        error = false;
        var articleExiste = 0;
       var newTotalttc = 0 ;
        var art = $(this).val();


        $('.selectArticle').each(function () {
            if ($(this).val() == art) {
                articleExiste++;
            }
        })
        if (parseInt(articleExiste) >= 2) {
            toastr.error('cet article a été choisir , veuillez choisir un autre');
            $(this).parent().parent().remove();
            //recalculer total ttc
            $('.total').each(function () {
                newTotalttc =  newTotalttc +parseFloat($(this).val());
                $('.tottalTTC').val(newTotalttc.toFixed(3));

            })
            return false;
        }

        console.log('initial ====' + selectAricle)
        $('.qte_' + index).attr('readonly', false);

        $.ajax({
            url: Routing.generate('perso_get_articles_byId'),
            type: "POST",
            data: {id_article: $(this).val()},
            success: function (data) {
                if (data) {
                    $('.descriptionarticle_' + index).text(data[0].article.description);
                    $('.stock_' + index).val(data[0].qte);
                    $('.remise_' + index).val(data[0].article.remise);
                    $('.pventettc_' + index).val((data[0].puVenteTTC).toFixed(3));
                    $('.total_' +  index).val(0.000);
                    $('.qte_' + index).val(1);

                    $('.pventettc_' + index).attr('data-id_art',  data[0].article.id);
                    $('.stock_' + index).attr('data-id_art',  data[0].article.id);
                    $('.total_' + index).attr('data-id_art',  data[0].article.id);
                    $('.qte_' + index).attr('data-id_art',  data[0].article.id);
                    $('.qte_' + index).attr('data-id_art',  data[0].article.id);

                    $('.total').each(function () {
                        newTotalttc =  newTotalttc +parseFloat($(this).val());
                        $('.tottalTTC').val(newTotalttc.toFixed(3));

                    })


                }

            },
            error: function () {
                alert('something wrong')
            }
        })

    })


}


