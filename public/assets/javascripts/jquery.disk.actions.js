$(document).ready(function () {
    $(document).on('click', '.share-files', function () {
        var files = [];

        $('table.files').find('input:checked').each(function () {
            files.push($(this).parent().parent().data('fileid'));
        });

        if (files.length > 0) {
            hideMessage();

            $.ajax({
                async: false,
                url: "/disk/share_files",
                type: "POST",
                data: {list: files},
                success: function (result) {
                    showMessage({message: result});
                }
            });
        } else {
            showMessage({message: 'Не выбрано ни одного файла'})
        }
    });

    $(document).on('click', '.file-remove', function (e) {
        e.preventDefault();
        var fileid = $(this).parent().parent().data('fileid');
        var self = this;

        $.ajax({
            async: true,
            url: "/disk/remove_file/" + fileid,
            type: "POST",
            data: { ajax: true },
            success: function (result) {
                if (result == 'OK') {
                    $(self).parent().parent().fadeOut('slow', function () {
                        $(this).remove();
                    });
                } else {
                    showMessage({message: result});
                }
            }
        });
    });

    $(document).on('click', '.file-verify', function (e) {
        e.preventDefault();
        var fileid = $(this).parent().parent().data('fileid');
        var self = this;

        $.ajax({
            url: "/disk/verify_file/" + fileid,
            type: "POST",
            data: { ajax: true },
            success: function (result) {
                if (result == 'OK') {
                    $(self).parent().parent().fadeOut('slow', function () {
                        $(this).remove();
                    });
                } else {
                    showMessage({message: result});
                }
            }
        });
    });
});