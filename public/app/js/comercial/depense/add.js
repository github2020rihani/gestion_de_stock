$(document).ready(function () {
    addLingeArticle();
    //
    $('.addDepense ').click(function () {
        if ($('.tbodyLingeArticle').length == 0) {
            error = true;
            toastr.error('Aucun dépense a été ajouté');

            return true;
        }


        $('.date_dep').each(function () {
            if ($(this).val() == 0) {
                error = true;

                toastr.error('Veuillez entrer une date');

                return true;
            }
        })
        $('.total_ttc_dep').each(function () {
            if ($(this).val() == '') {
                error = true;

                toastr.error('Veuillez entrer total ttc');

                return true;
            }
        })


        if (error) {
            return false;
        } else {
            //get articles


            $(this).hide();
            $('.formAddDepense').submit();

        }

    })
});

var lingArt = [];
var index;

function addLingeArticle() {
    $('.addLingneArticle').click(function () {
        error = false;

        countArticle++
        lingArt.push(1);
        index = lingArt.length;
        //appel function ajax get articles
        $('.tbodyLingeArticle').append(`
       <tr class="ligne_article">
                                                <th scope="row" width="5%">
                                                    <button class="btn btn-danger mr-2 delete_ligneArticle_${index}" data-toggle="tooltip" type="button"
                                                       data-placement="top" title="" data-original-title="Supprimer"><i
                                                                class="fa fa-trash"></i></button>
                                                                </th>
                                                                
                                                                <td width="15%"><input type="datetime-local" class="form-control date_dep"  readonly id="" name="date_dep[]"  ></td>
                                                                <td width="10%"><input type="text" class="form-control total_ttc_dep" id="" name="total_ttc_dep[]"></td>
                                                                <td width="30%">
                                                                  <select class="js-example-basic-single type_dep article" name="type_dep[]">
                                                        <option value="0" selected readonly>Coisir un type</option>
                                                        <option value="1"  > Carburant / Gasoil </option>
                                                        <option value="2"  >Recharge téléphone</option>
                                                        <option value="3"  >Avance</option>
                                                       

                                                    </select>
</td>
                                                                <td width="60%"><textarea class="form-control" rows="6" name="desc_dep[]"></textarea> </td>
                                                        
                                                                          </tr>`);


        $('.js-example-basic-single').select2();
        $(".date_dep").val(new Date().toJSON().slice(0,19));

        //remove ligne achat
        $(".delete_ligneArticle_" + index).click(function (event) {
            $(this).parent().parent().remove();
        });


    })


}

var selectAricle = [];
var countArticle = 0;
var error = false;



