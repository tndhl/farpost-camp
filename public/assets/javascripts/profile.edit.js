$(document).ready(function () {
    function hideEditAreas(changeText) {
        $('.editable-data').each(function (i, element) {
            if (changeText) $(element).parent().find('.editable').text($(element).val() || $(element).text() || 'не заполнено').show();
            else $(element).parent().find('.editable').show();

            $(element).remove();
        });

        $('.user-profile button').addClass('hidden');
    }

    function saveData() {
        var data = [];

        $('.editable-data').each(function (i, element) {
            data.push({
                title: $(element).parent().find('label').text(),
                value: $(element).val() || $(element).text(),
                name: $(element).data('name'),
                field_id: $(element).data('fid') || 0,
                user_id: $('.user-profile form').data('userid') || 0
            });
        });

        $.ajax({
            type: "POST",
            url: "/user/save_field",
            data: {
                'data': data
            },
            dataType: "json",
            success: function (result) {
                if (result.error) {
                    showMessage({message: result.error});
                    hideEditAreas(false);
                } else {
                    showMessage({message: result.message});
                    hideEditAreas(true);
                }
            },
            error: function () {
                showMessage({message: 'Невозможно сохранить данные. Попробуйте позже'});
                hideEditAreas(false);
            }
        })
    }

    $('.user-profile').find('span.editable').on('click', function () {
        var span = this;

        var html_tag_name = $(this).data('html-tag'),
            html_tag_type = $(this).data('html-tag-type'),
            name = $(this).data('name'),
            fid = $(this).data('fid');

        var html_tag = document.createElement(html_tag_name);
        $(html_tag).data('name', name);
        $(html_tag).data('fid', fid);
        $(html_tag).addClass("editable-data");

        if (html_tag_type !== undefined) html_tag.type = html_tag_type;

        $(span).hide();
        $(html_tag).insertAfter(span);

        if ($(span).text() != "не заполнено") {
            $(html_tag).text($(span).text());
            $(html_tag).val($(span).text());
        }

        $(html_tag).focus();

        $(html_tag).keypress(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                saveData();
            }
        });

        $('.user-profile button').removeClass('hidden');
    });

    $('.user-profile button.btn-default').bind('click', saveData);
    $('.user-profile button.btn-cancel').bind('click', function () {
        hideEditAreas(false);
    });
});