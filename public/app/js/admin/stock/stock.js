$(document).ready(function () {
    changeStock();
    verifierStock();
})

function changeStock() {
    $('.table_stock').on('click', '.btn_majQte', function () {
        var id_article = parseInt($(this).data('id'));
        var type = $(this).data('type');
        var newQte = parseInt($('#majQte_' + id_article).val());
        var oldqte = parseInt($('.old_qte_' + id_article).text());
        var totalQte = 0;



        if ($('#majQte_' + id_article).val() == '' || newQte == 0 ) {
            toastr.error('Pas de quantité entrer');
            return false;
        }
        $.ajax({
            url: Routing.generate('achat_maj_stock'),
            type: "post",
            data: {id_article: id_article, type: type, qte: newQte},
            success: function (data) {
                if (data.success == true) {
                    if (type == 'add') {
                        $('.old_qte_' + id_article).text(newQte + oldqte);
                        totalQte = oldqte + newQte;
                        if (parseInt(totalQte) > 10) {
                            $('#status_' + id_article).removeClass('bg-warning').addClass('bg-success');
                            $('#status_' + id_article).removeClass('bg-danger').addClass('bg-success');
                            $('#status_' + id_article).text('En stock');
                        } else if (parseInt(totalQte) > 0 && parseInt(totalQte) < 10) {
                            $('#status_' + id_article).removeClass('bg-success').addClass('bg-warning');
                            $('#status_' + id_article).removeClass('bg-danger').addClass('bg-warning');
                            $('#status_' + id_article).text('Pré epuisée');
                        } else if (parseInt(totalQte) == 0) {
                            $('#status_' + id_article).removeClass('bg-success').addClass('bg-danger');
                            $('#status_' + id_article).removeClass('bg-warning').addClass('bg-danger');
                            $('#status_' + id_article).text('Epuisée');

                        }

                    } else {
                        if (newQte > oldqte) {
                            toastr.error('La quantité est supérieur de la quantité du base');
                            return false;
                        } else {
                            $('.old_qte_' + id_article).text(oldqte - newQte);
                            totalQte = oldqte - newQte;
                            if (parseInt(totalQte) > 10) {
                                $('#status_' + id_article).removeClass('bg-warning').addClass('bg-success');
                                $('#status_' + id_article).removeClass('bg-danger').addClass('bg-success');
                                $('#status_' + id_article).text('En stock');
                            } else if (parseInt(totalQte) > 0 && parseInt(totalQte) < 10) {
                                $('#status_' + id_article).removeClass('bg-success').addClass('bg-warning');
                                $('#status_' + id_article).removeClass('bg-danger').addClass('bg-warning');
                                $('#status_' + id_article).text('Pré epuisée');
                            } else if (parseInt(totalQte) == 0) {
                                $('#status_' + id_article).removeClass('bg-success').addClass('bg-danger');
                                $('#status_' + id_article).removeClass('bg-warning').addClass('bg-danger');
                                $('#status_' + id_article).text('Epuisée');

                            }

                        }
                    }
                    $('#majQte_' + id_article).val('');
                    // toastr.success(data.message);

                    //change status dynamique
                    $('#statutInventer_'+id_article).html(`     <span id="status_${id_article}"
                                                  class="badge badge-warning">
                                                        En Attente
                                                    </span>`);

                    $('#modalInv').modal('show');
                    $('.InventerArticle').click(function () {
                        //appele function inventaire()
                        $.ajax({
                            url: Routing.generate('verifier_stock'),
                            type: "post",
                            data: {id_article: id_article},
                            success: function (data) {
                                if (data.success == 'true') {
                                    toastr.success(data.message);
                                    //change status dynamique
                                    $('#statutInventer_'+id_article).html(`     <span id="status_${id_article}"
                                                  class="badge badge-warning">
                                                        En Attente
                                                    </span>`);


                                }else{
                                    toastr.error(data.message);
                                }


                            },
                            error: function () {
                                alert('something wrong')
                            }
                        })


                    })

                } else {
                    toastr.error(data.message);
                }

            },complete: function(data){
            },
            error: function () {
                alert('something wrong')
            }
        })


    })

}

function verifierStock() {
    $('.table_stock').on('click', '.verifierQte', function () {
        var id_article = parseInt($(this).data('id'));
        $.ajax({
            url: Routing.generate('achat_verifier_stock'),
            type: "post",
            data: {id_article: id_article},
            success: function (data) {
                if (data.success == 'true') {
                    toastr.success(data.message);
                    //change status dynamique
                    $('#statutInventer_'+id_article).html(`     <span id="status_${id_article}"
                                                  class="badge badge-success">
                                                        Inventé
                                                    </span>`);


                }else{
                    toastr.error(data.message);
                }


            },
            error: function () {
                alert('something wrong')
            }
        })


    })
}



