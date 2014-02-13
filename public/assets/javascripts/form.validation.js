$(document).find('form').submit(function(e) {
    var $form = $(this);
    var error = {
        msg: 'Поле обязательно для заполнения', 
        inputClass: 'invalid',
        errorClass: 'error'
    };

    var params = [];
    var isNotFailed = false;

    $form.find('span.error').each(function(){
        $(this).text('');
    });

    $form.find('input.required').each(function(){
        $(this).removeClass('invalid');

        var param = { "name": $(this).prop("name"), "value": $(this).val() };
        params.push(param);
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
                    var errorSpan = document.createElement('span');
                    $(errorSpan).addClass(error.errorClass);
                    $(errorSpan).text(error.msg);

                    $form.find('[name=' + result[key] + ']').addClass(error.inputClass);
                    $form.find('[name=' + result[key] + ']').parent().append(errorSpan);
                }
            } else {
                isNotFailed = true;
            }
        }
    });

    console.log(isNotFailed);
    if (isNotFailed) return true;
    else return false;
});