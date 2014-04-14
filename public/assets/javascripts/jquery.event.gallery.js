function ChunkedUploader(file, elem, eventId) {
    if (!this instanceof ChunkedUploader) {
        return new ChunkedUploader(file, elem, eventId);
    }

    this.file = file;
    this.options = {
        url: '/news/gallery_upload'
    };

    this.uniqueIdGenerator = function() {
        return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
    };

    this.uniqueId = this.uniqueIdGenerator();

    this.elem = elem;
    this.eventId = eventId;

    this.fileSize = this.file.size;
    this.chunkSize = 102400; // 100KB
    this.rangeStart = 0;
    this.rangeEnd = this.chunkSize;

    if ('mozSlice' in this.file) {
        this.sliceMethod = 'mozSlice';
    } else if ('webkitSlice' in this.file) {
        this.sliceMethod = 'webkitSlice';
    } else {
        this.sliceMethod = 'slice';
    }

    this.upload = function () {
        var self = this,
            chunk;

        if (self.rangeEnd > self.fileSize) {
            self.rangeEnd = self.fileSize;
        }

        chunk = self.file[self.sliceMethod](self.rangeStart, self.rangeEnd);

        var formData = new FormData();
        var dataSet = {
            filename: self.file.name,
            filesize: self.fileSize,
            uniqueId: self.uniqueId,
            rangeStart: self.rangeStart,
            rangeEnd: self.rangeEnd,
            eventId: self.eventId
        };

        formData.append(self.uniqueId, chunk);
        formData.append('data', JSON.stringify(dataSet));

        setTimeout(function(){
            $.ajax({
                url: self.options.url,
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                mimeType: 'application/octet-stream',
                headers: (self.rangeStart !== 0) ? {
                    'Content-Range': ('bytes ' + self.rangeStart + '-' + self.rangeEnd + '/' + self.fileSize)
                } : {},
                success: self.onChunkLoaded(self)
            });
        }, 20);

    };

    this.onUploadComplete = function () {
        $(this.elem).find('.status').text('Готово. 100%');
    };

    this.onChunkLoaded = function (self) {
        if (this.rangeEnd == this.fileSize) {
            self.onUploadComplete();
            return;
        }

        this.rangeStart = this.rangeEnd;
        this.rangeEnd = this.rangeStart + this.chunkSize;

        var percent = Math.round(this.rangeEnd / this.fileSize * 100);
        percent = percent > 100 ? 100 : percent;

        $(this.elem).find('.status .loaded').text(percent + '%');
        $(this.elem).find('.loading-bar .loaded').width(percent + '%');

        this.upload();
    };

    this.start = function () {
        this.upload();
    };
}

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
            uploaders.push(new ChunkedUploader(file, elem, eventId));
        }

        $.each(uploaders, function(i, uploader) {
            uploader.start();
        });

        uploaders = [];
    };

    $selectFiles.bind('change', onFilesSelected);
});
