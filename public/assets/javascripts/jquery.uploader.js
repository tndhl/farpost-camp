function ChunkedUploader(file, options) {
    if (!this instanceof ChunkedUploader) {
        return new ChunkedUploader(file, options);
    }

    this.file = file;
    this.options = $.extend({
        entryId: ''
    }, options);

    this.uniqueIdGenerator = function () {
        return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
    };

    this.uniqueId = this.uniqueIdGenerator();

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
            entryId: self.options.entryId
        };

        formData.append(self.uniqueId, chunk);
        formData.append('data', JSON.stringify(dataSet));

        setTimeout(function () {
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
                success: function (result) {
                    self.onChunkLoaded(self, result);
                }
            });
        }, 20);

    };

    this.onUploadComplete = function (result) {
        if (this.options.onUploadComplete) this.options.onUploadComplete(result);
    };

    this.onChunkLoaded = function (self, result) {
        if (this.rangeEnd == this.fileSize) {
            self.onUploadComplete(result);
            return;
        }

        this.rangeStart = this.rangeEnd;
        this.rangeEnd = this.rangeStart + this.chunkSize;

        if (this.rangeEnd > this.fileSize) {
            this.rangeEnd = this.fileSize;
        }

        var percent = Math.round(this.rangeEnd / this.fileSize * 100);
        percent = percent > 100 ? 100 : percent;

        if (this.options.onChunkLoaded) this.options.onChunkLoaded(percent, this.rangeEnd);

        this.upload();
    };

    this.start = function () {
        this.upload();
    };
}