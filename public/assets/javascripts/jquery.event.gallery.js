$(document).ready(function () {
    var $gallery = $(document).find('.gallery');
    var eventId = $gallery.data('eventid');
    var $selectFiles = $gallery.find('#selectFiles');
    var $progress = $gallery.find('.gallery-progress');
    var $content = $gallery.find('.gallery-content');

    var uploaders = [];

    var addFileToUploadList = function () {
        var $item = $('<div/>').addClass('item');
        var $preview = $('<div/>').addClass('preview');
        var $loadingBar = $('<div/>').addClass('loading-bar');
        var $loadedBar = $('<div/>').addClass('loaded');
        var $status = $('<div/>').addClass('status');
        var $loadedPercent = $('<span/>').addClass('loaded');

        $status.text('Загрузка.. ');
        $item.append($preview, $loadingBar.append($loadedBar), $status.append($loadedPercent));

        $progress.append($item);

        return $item;
    };

    var onFilesSelected = function (e) {
        var files = e.target.files,
            file,
            elem;

        for (var i = 0; i < files.length; i++) {
            file = files[i];
            elem = addFileToUploadList();

            uploaders.push(
                new ChunkedUploader(file,
                    {
                        url: "/news/gallery_upload",
                        entryId: eventId,
                        onChunkLoaded: function (percent, bytes) {
                            $(elem).find('.status .loaded').text(percent + '%');
                            $(elem).find('.loading-bar .loaded').width(percent + '%');
                        },
                        onUploadComplete: function(result) {
                            $(elem).find('.status').text('Готово. 100%');
                        }
                    }
                )
            );
        }

        $.each(uploaders, function(i, uploader) {
            uploader.start();
        });

        uploaders = [];
    };

    $selectFiles.bind('change', onFilesSelected);
});
