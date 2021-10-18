$(document).ready(function () {

    addLigneAchat();


});


function addLigneAchat() {
    $('.addLingeAchat').click(function () {
        var index = ($('.ligne_achat').length);
        var contentListArticle = '';
        index++ ;
        console.log(index);
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
                        const indexArticle = selectAricle.indexOf( $('.selectArticle_' + index).val());
                        if (indexArticle > -1) {
                            selectAricle.splice(indexArticle, 1);
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
var puttc = 0
var marge = 0
var selectAricle = [];
function selectArticle(index) {
    $('.selectArticle_' + index).change(function () {
        if (selectAricle.includes($(this).val())){
           toastr.error('cet article a été choisir , veuillez choisir un autre');
            $(this).parent().parent().remove();
            return false ;
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