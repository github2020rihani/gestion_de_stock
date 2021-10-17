$(document).ready(function () {
    $("#formUser").validate({
        errorClass: "my-error-class",
        validClass: "my-valid-class",
        rules: {
            'user[phone]': {
                required: true,
                pattern: /^\d{8}$/,
                minlength: 8,
                maxlength: 8
            },
            'user[plainPassword][first]': {
                required: true,
                // pattern: /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8})$/,
                //pwcheck: true,

            },
            'user[plainPassword][second]': {
                required: true,
                // pattern: /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8})$/,
                //pwcheck: true,
                equalTo: "#user_plainPassword_first"

            },


        },
        messages: {

            'user[phone]': {
                pattern: 'Saisissez que des chiffres. Format valide : 99999999',
                minlength: "le numéro téléphone contient que 8 chiffres",
                maxlength: "le numéro téléphone contient que 8 chiffres",
            },
            'user[plainPassword][second]': {
                equalTo: 'Les mots de passe saisis ne sont pas identiques'
            },
        },
        errorElement: "em",
    });

    $("#formEditUser").validate({
        errorClass: "my-error-class",
        validClass: "my-valid-class",
        rules: {
            'edit_user[phone]': {
                required: true,
                pattern: /^\d{8}$/,
                minlength: 8,
                maxlength: 8
            },
        },
        messages: {

            'user[phone]': {
                pattern: 'Saisissez que des chiffres. Format valide : 99999999',
                minlength: "le numéro téléphone contient que 8 chiffres",
                maxlength: "le numéro téléphone contient que 8 chiffres",
            },

        },
        errorElement: "em",

});
    $("#formLogin").validate({
        errorClass: "my-error-class",
        validClass: "my-valid-class",
        rules: {
            'email': {
                required: true,
                email: true
            },
            'password': {
                required: true,


            },
        },
        messages: {



        },
        errorElement: "em",

});


})
