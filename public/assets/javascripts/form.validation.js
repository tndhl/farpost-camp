$(document).find('form').on('submit', function(e) {
    e.preventDefault();

    var $form = $(this);
    var error = {
        msg: 'Поле обязательно для заполнения', 
        inputClass: 'invalid',
        errorClass: '.error'
    };

    $form.find('span.error').each(function(){
        $(this).text('');
    });

    $form.find('input.required').each(function(){
        $(this).removeClass('invalid');

        if (!$(this).val().length) {
            $(this).addClass('invalid');
            $(this).parent().find(error.errorClass).text(error.msg);
        }
    });
});