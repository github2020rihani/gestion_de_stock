$(document).ready(function ()  {
    changeStock();
})

 function changeStock() {
        $('.table_stock').on('click', '.btn_majQte', function (){
        var id_article = parseInt($(this).data('id'));
        var type = $(this).data('type');
        var newQte = parseInt($('#majQte_'+id_article).val());
        var oldqte = parseInt($('.old_qte_'+id_article).text());
        var totalQte = 0 ;

        if ($('#majQte_'+id_article).val() == '') {
            toastr.error('Saisir le quantite');
            return false ;
        }
        $.ajax({
            url: Routing.generate('maj_stock'),
            type: "post",
            data: {id_article: id_article, type: type , qte: newQte},
            success: function (data) {
                if (data.success == true) {
                    if (type == 'add') {
                        $('.old_qte_'+id_article).text(newQte + oldqte);
                        totalQte = oldqte + newQte ;
                        if (parseInt(totalQte) > 10) {
                            $('#status_'+id_article).removeClass('badge badge-warning').addClass('badge badge-success');
                            $('#status_'+id_article).removeClass('badge badge-danger').addClass('badge badge-success');
                            $('#status_'+id_article).text('En stock');
                        }else if (parseInt(totalQte) <= 10) {
                            $('#status_'+id_article).removeClass('badge badge-success').addClass('badge badge-warning');
                            $('#status_'+id_article).removeClass('badge badge-danger').addClass('badge badge-warning');
                            $('#status_'+id_article).text('Pré epuisée');
                        }else if(parseInt(totalQte) == 0 ) {
                            $('#status_'+id_article).removeClass('badge badge-success').addClass('badge badge-danger');
                            $('#status_'+id_article).removeClass('badge badge-warning').addClass('badge badge-danger');
                            $('#status_'+id_article).text('Epuisée');

                        }

                    }else{
                        if (newQte > oldqte) {
                            toastr.error('le quatite depasser la quatite de base');
                            return false ;
                        }else{
                            $('.old_qte_'+id_article).text(oldqte - newQte);
                            totalQte = oldqte - newQte ;
                            if (parseInt(totalQte) > 10) {
                                $('#status_'+id_article).removeClass('badge badge-warning').addClass('badge badge-success');
                                $('#status_'+id_article).removeClass('badge badge-danger').addClass('badge badge-success');
                                $('#status_'+id_article).text('En stock');
                            }else if (parseInt(totalQte) <= 10) {
                                $('#status_'+id_article).removeClass('badge badge-success').addClass('badge badge-warning');
                                $('#status_'+id_article).removeClass('badge badge-danger').addClass('badge badge-warning');
                                $('#status_'+id_article).text('Pré epuisée');
                            }else if(parseInt(totalQte) == 0 ) {
                                $('#status_'+id_article).removeClass('badge badge-success').addClass('badge badge-danger');
                                $('#status_'+id_article).removeClass('badge badge-warning').addClass('badge badge-danger');
                                $('#status_'+id_article).text('Epuisée');

                            }

                        }
                    }
                    $('#majQte_'+id_article).val('');
                    toastr.success(data.message);

                }else {
                    toastr.error(data.message);
                }

            },
            error: function () {
                alert('something wrong')
            }
        })


    })

 }