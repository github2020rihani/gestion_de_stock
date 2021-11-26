$(document).ready(function () {
    $('.searchArticleEpuise').keypress(function (e) {
        var key = e.which;
        if(key == 13)  // the enter key code
        {
            $('input[name = butAssignProd]').click();
            //appel ajax get result with search
            var mot = $('.searchArticleEpuise').val();
            $.ajax({
                url: Routing.generate('api_get_articles'),
                type: "POST",
                data: {mot: mot},
                success: function (data) {
                    if (data.success == "true") {
                        $('.tableArticlesEpuise').empty();
                        $('.tableArticlesEpuise').append(data.message);




                    }else{
                        toastr.error(data.message);
                    }


                },
                error: function () {
                    alert('something wrong')
                }


            });




            return false;
        }
    });

    $('.searchArticlePrix').keypress(function (e) {
        var key = e.which;
        if(key == 13)  // the enter key code
        {
            $('input[name = butAssignProd]').click();
            //appel ajax get result with search
            var mot = $('.searchArticlePrix').val();
            $.ajax({
                url: Routing.generate('api_get_articles_prix'),
                type: "POST",
                data: {mot: mot},
                success: function (data) {
                    if (data.success == "true") {
                        $('.tableArticlePrix').html(data.message);




                    }else{
                        toastr.error(data.message);
                    }


                },
                error: function () {
                    alert('something wrong')
                }


            });



            return false;
        }
    });
})