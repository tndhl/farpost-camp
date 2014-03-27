$(document).find('form').submit(function () {
    var $form = $(this);
    var error = {
        msg: 'Поле обязательно для заполнения',
        inputClass: 'invalid',
        errorClass: 'error'
    };

    var params = {};
    var extra_params = {};
    var isNotFailed = false;

    $form.find('span.error').each(function () {
        $(this).text('');
    });

    $form.find('.required').each(function () {
        $(this).removeClass('invalid');

        params[$(this).prop("name")] = $(this).val();
    });

    $form.find('.extra_param').each(function () {
        if ($(this).is(":checkbox")) {
            extra_params[$(this).prop("name")] = $(this).is(':checked');
        } else {
            extra_params[$(this).prop("name")] = $(this).val();
        }
    });

    $.ajax({
        cache: false,
        async: false,
        type: "POST",
        url: $form.data('validate-url'),
        dataType: "json",
        data: { params: JSON.stringify(params), extra: JSON.stringify(extra_params) },
        success: function (result) {
            if (result.length > 0) for (var key in result) {
                $form.find('[name=' + result[key] + ']').addClass(error.inputClass);
                $form.find('[name=' + result[key] + ']').parent().find('span.' + error.errorClass).text(error.msg);
            } else {
                isNotFailed = true;
            }
        }
    });

    return !!isNotFailed;
});