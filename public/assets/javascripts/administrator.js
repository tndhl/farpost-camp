$(document).ready(function () {

    /**
     * Добавление роли пользователю
     */
    $('#user-add-role').click(function () {
        var uid = $(this).data('uid');

        if (uid == undefined) {
            displayBlock({title: 'Ошибка!', message: 'Не указан идентификатор пользователя.'});
        } else {
            $.ajax({
                type: "GET",
                url: "/user/addrole",
                data: {userid: uid},
                dataType: "json",
                success: function (result) {
                    if (result.form.length > 0) {
                        displayBlock({title: 'Выберите роль', html: result.form});

                        $(document).find('#form-add-role').find('button').on('click', function (e) {
                            e.preventDefault();
                            var rid = $(this).parent().find('select').val();

                            if (rid.length != 0) {
                                $.ajax({
                                    type: "POST",
                                    url: "/user/addrole",
                                    data: {userid: uid, roleid: rid},
                                    success: function(result) {
                                        if (result == 'OK') {
                                            displayBlock({
                                                title: 'Успешно!',
                                                message: 'Роль успешно добавлена.',
                                                onClose: function() {
                                                    window.location.reload();
                                                }
                                            });
                                        } else {
                                            displayBlock({title: 'Ошибка!', message: 'Пожалуйста, попробуйте позже.'});
                                        }
                                    }
                                });
                            } else {
                                displayBlock({title: 'Ошибка!', message: 'Пожалуйста, выберите роль.'});
                            }
                        });
                    } else {
                        displayBlock({title: 'Ошибка!', message: 'Пожалуйста, попробуйте позже.'});
                    }
                }
            });
        }
    });

    $('.user-remove-role').click(function () {
        var uid = $(this).data('uid');
        var rid = $(this).data('rid');

        if (uid == undefined || rid == undefined) {
            displayBlock({title: 'Ошибка!', message: 'Не указаны необходимые данные для выполнения команды.'});
        } else {
            $.ajax({
                type: "POST",
                url: "/user/removerole",
                data: {userid: uid, roleid: rid},
                success: function (result) {
                    if (result == 'OK') {
                        displayBlock({
                            title: 'Успешно!',
                            message: 'Роль успешно удалена.',
                            onClose: function() {
                                window.location.reload();
                            }
                        });
                    } else {
                        displayBlock({title: 'Ошибка!', message: 'Пожалуйста, попробуйте позже.'});
                    }
                }
            });
        }
    });
});