$(document).find('form').submit(function(e) {
    var $form = $(this);
    var error = {
        msg: 'Поле обязательно для заполнения', 
        inputClass: 'invalid',
        errorClass: 'error'
    };

    var params = {};
    var isNotFailed = false;

    $form.find('span.error').each(function(){
        $(this).text('');
    });

    $form.find('input.required').each(function(){
        $(this).removeClass('invalid');
        
        params[$(this).prop("name")] = $(this).val();
    });

    $.ajax({
        cache: false,
        async: false,
        type: "POST",
        url: "/user/validate",
        dataType: "json",
        data: { params: JSON.stringify(params) },
        success: function(result) {
            if (result.length > 0) {
                for (var key in result) {
                    $form.find('[name=' + result[key] + ']').addClass(error.inputClass);
                    $form.find('[name=' + result[key] + ']').parent().find('span.' + error.errorClass).text(error.msg);
                }
            } else {
                isNotFailed = true;
            }
        }
    });

    if (isNotFailed) return true;
    else return false;
});