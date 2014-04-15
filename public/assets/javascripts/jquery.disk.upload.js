$(document).ready(function () {
    var uploadBtn = $('.upload-file'),
        uploadFiles = $('input#files'),
        $fileList = $('table.files');

    $(uploadBtn).bind('click', function () {
        $(uploadFiles).trigger('click');
    });

    $(uploadFiles).bind('change', function (e) {
        var files = e.target.files;
        var uploaders = [];

        if (files.length > 0) $fileList.find('tr.empty-list').addClass('hidden');
        else $fileList.find('tr.empty-list').removeClass('hidden');

        $.each(files, function(i, file) {
            var spinner = $('<i/>').addClass('fa fa-refresh fa-spin');

            var $fileRow = $('<tr class="not-verified"/>');
            var $fileIcon = $('<td class="icon"/>'),
                $fileTitle = $('<td class="title"/>').text(file.name),
                $fileCreatedDate = $('<td class="created"/>').append(spinner),
                $fileSize = $('<td class="size"/>').html('<span class="loaded">0</span> / ' + file.size + ' Б'),
                $fileOptions = $('<td class="options"/>');

            $fileRow.append($fileIcon, $fileTitle, $fileCreatedDate, $fileSize, $fileOptions);
            $fileList.prepend($fileRow);

            uploaders.push(new ChunkedUploader(file,
                {
                    url: "/disk/upload",
                    onChunkLoaded: function(percent, bytes) {
                        $fileSize.find('.loaded').text(bytes);
                    },
                    onUploadComplete: function(result) {
                        result = JSON.parse(result);

                        $fileRow.hide();
                        $fileTitle.parent().data('fileid', result.file_id);
                        $fileTitle.text(result.title);
                        $fileCreatedDate.text(result.created);
                        $fileSize.text(result.filesize);

                        $fileRow.fadeIn('slow');
                    }
                }
            ));

            uploaders[i].start();
        });
    });
});
