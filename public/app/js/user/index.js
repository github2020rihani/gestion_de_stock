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
                if (data.mot === 'Activer') {
                    $('#statusUser_' + id).removeClass('badge badge-danger').addClass(data.classcss);
                    $('#btn_statusUser_' + id).removeClass('badge bg-success').addClass('badge bg-danger');
                    $('#btn_statusUser_' + id).attr('data-original-title', 'desactiver')

                } else  if (data.mot === 'Desactiver') {
                    $('#statusUser_' + id).removeClass('badge badge-success').addClass(data.classcss);
                    $('#btn_statusUser_' + id).removeClass('badge bg-danger').addClass('badge bg-success');
                    $('#btn_statusUser_' + id).attr('data-original-title', 'Activer')


                }
                $('#statusUser_' + id).text(data.mot);


            },
            error: function () {
                alert('something wrong')
            }
        })


    })
}