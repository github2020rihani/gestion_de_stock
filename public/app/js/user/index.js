$(document).ready(function () {

    changeStatus();
})

function changeStatus() {
    $('.tableUsers').on('click', '.btn_changeStatus', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        $.ajax({
            url: 'change_status',
            type: "POST",
            data: {
                id: id,
            },
            success: function (data) {
                if (data.mot === 'Activé') {
                    $('#statusUser_' + id).removeClass('badge badge-danger').addClass(data.classcss);
                    $('#btn_statusUser_' + id).attr('title', 'Désactiver')

                } else  if (data.mot === 'Désactivé') {
                    $('#statusUser_' + id).removeClass('badge badge-success').addClass(data.classcss);
                    $('#btn_statusUser_' + id).attr('title', 'Activer')


                }
                $('#statusUser_' + id).text(data.mot);


            },
            error: function () {
                alert('something wrong')
            }
        })


    })
}