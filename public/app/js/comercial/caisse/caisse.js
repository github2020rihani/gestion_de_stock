$(document).ready(function () {

    var date ;

    $('.date_selected').change(function () {

        date = $(this).val();
        $.ajax({
            url: Routing.generate('change_caisse'),
            data: {date: date},
            cache: false,
            success: function (data) {
                console.log(data);
                if (data.status){
                    $('.twig').empty();
                    $('.twig').append(data.twig);

                }else{
                    toastr.error('Pas de enregistrement');
                }

            },
            error: function () {
                alert('something wrong')

            }
        });


    })


    // $('.printCaisse').click(function () {
    //     date = $(this).val();
    //     $.ajax({
    //         url: Routing.generate('print_caisse'),
    //         data: {date: date},
    //         cache: false,
    //         success: function (data) {
    //             console.log(data);
    //
    //
    //         },
    //         error: function () {
    //             alert('something wrong')
    //
    //         }
    //     });
    // })


})