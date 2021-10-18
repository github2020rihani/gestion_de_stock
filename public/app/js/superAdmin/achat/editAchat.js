$(document).ready(function () {
    var url = window.location.href;
    var id = url.substring(url.lastIndexOf('/') + 1);
    getArticlesAchat(id);
    removeArticle();
    changePUHTNET();
    changePVenteTTC();
    addLigneAchat();



});
var puttc = 0
var marge = 0
var selectAricle = [];
var articleToDelete = [];

function removeArticle() {
    $(".delete_ligneAchat").click(function (event) {
        const indexArticle = selectAricle[0].indexOf( $(this).data('id_article'));
        if (indexArticle > -1) {
            selectAricle[0].splice(indexArticle, 1);
        }
        $(this).parent().parent().remove();
        articleToDelete.push($(this).data('id_article'));
       $(this).attr('data-original-title', '');
       $('.articleToDelete').val(articleToDelete);
        console.log(articleToDelete);
    });
}
function getArticlesAchat(id) {
    $.ajax({
        url: Routing.generate('get_articles_achat'),
        type: "get",
        data: {id_achat: id},
        success: function (data) {
            if (data) {
                console.log(data)
                selectAricle.push(data);



            }

        },
        error: function () {
            alert('something wrong')
        }
    })
}

function addLigneAchat() {
    $('.addLingeAchat').click(function () {
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
                                                    <button class="badge bg-danger mr-2 delete_ligneAchat_${index}" data-toggle="tooltip" type="button"
                                                       data-placement="top" title="" data-original-title="Supprimer"><i
                                                                class="ri-delete-bin-line mr-0"></i></button></th>
                                                <td>
                                                    <select class="js-example-basic-single selectArticle_${index}" name="article[]">
                                                        <option value="0" selected readonly>Coisir un article</option>
                                                       ${contentListArticle}

                                                    </select>
                                                </td>
                                                <td class="descriptionarticle_${index}"></td>
                                                <td>
                                                    <input type="text" name="puhtnet[]" data-index = "${index}" value="0.000" class="form-control  puhtnet puhtnet_${index}">
                                                </td>
                                                <td>
                                                    <input type="number" min="1" name="qte[]" data-index = "${index}" value="0.000" class="form-control qte_${index}">

                                                </td>
                                                <td>
                                                    <input type="text" value="19.0" name="tva[]" data-index = "${index}"  class="form-control tva_${index}" readonly>

                                                </td>
                                                <td>
                                                    <input type="text" name="puttc[]" value="0.000" data-index = "${index}" class="form-control puttc_${index}" readonly>

                                                </td>
                                                <td>
                                                    <input type="text" name="marge[]" value="0.000" data-index = "${index}" class="form-control marge_${index}" readonly>

                                                </td>
                                                <td>
                                                    <input type="text" name="pventettc[]" value="0.000" data-index = "${index}" class="form-control pventettc pventettc_${index}">

                                                </td>
                                            </tr>`);
                    $('.js-example-basic-single').select2();
                    //remove ligne achat
                    $(".delete_ligneAchat_" + index).click(function (event) {
                        const indexArticle = selectAricle[0].indexOf( $('.selectArticle_' + index).val());
                        if (indexArticle > -1) {
                            selectAricle[0].splice(indexArticle, 1);
                        }


                        $(this).parent().parent().remove();
                    });
                    //select article
                    selectArticle(index);
                    //changePUHTnET
                    changePUHTNET();
                    changePVenteTTC();
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
        console.log(selectAricle);
        if (selectAricle[0].includes(parseInt($(this).val()))){
            toastr.error('cet article a été choisir , veuillez choisir un autre');
            $(this).parent().parent().remove();
            return false ;
        }
        selectAricle[0].push(parseInt($(this).val()));
        console.log(selectAricle[0]);

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
    puttc = 0 ;

    $('.puhtnet').keyup("input", function(e) {
        var index = $(this).data('index');
        var pventettc =  $('.pventettc_'+index).val();
        puttc = (parseFloat($(this).val() * tva).toFixed(3));
        $('.puttc_'+index).val((puttc));
        console.log(pventettc);
        if (pventettc) {
            marge =  (((pventettc - puttc) / puttc) * 100).toFixed(2);
            $('.marge_'+index).val(marge);
        }
    })

}
function changePVenteTTC() {

    $('.pventettc').keyup("input", function(e) {
        var index = $(this).data('index');

        puttc =   $('.puttc_'+index).val();
        console.log(puttc);


        marge =  ((($(this).val() - puttc) / puttc) * 100).toFixed(2);


        $('.marge_'+index).val(marge);
    })

}






