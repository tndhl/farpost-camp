<div class="container news-item">
    <header>
        <h1><?= $entry["title"]; ?></h1>

        <span><time><?= $entry["date"]; ?></time></span>
        <span>#<?= $entry["tag"]; ?></span>

        <a href="/news" class="btn-back">Вернуться назад</a>
    </header>

    <p><?= $entry["content"]; ?></p>

    <?php if ($entry["tag"] == "event"): ?>
        <hr/>

        <div class="gallery" data-eventid="<?= $entry["id"]; ?>">
            <h2>Фотогалерея события</h2>

            <?php if ($user->id): ?>
                <div class="gallery-uploader">
                    <span>Выбрать файлы для загрузки</span>
                    <input id='selectFiles' type="file" name="photos" multiple>
                </div>

                <div class="gallery-progress">

                </div>
            <?php else: ?>
                <div class="alert">Войдите, чтобы загрузить фотографии ;)</div>
            <?php endif; ?>

            <div class="gallery-content">
                <?php foreach ($gallery as $photo): ?>
                    <div class="photo" data-eventid="id<?= $entry["id"]; ?>" data-filename="<?= $photo["filename"]; ?>">

                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <script src="/public/assets/javascripts/jquery.event.gallery.js"></script>
        <script>
            $(document).ready(function () {
                var $photos = $('.gallery-content').find('.photo');

                $photos.each(function (i, photo) {
                    var image = new Image(),
                        filename = $(photo).data('filename'),
                        eventid = $(photo).data('eventid');

                    $(image).load(function () {
                        $(photo).hide().append(image).fadeIn('slow');
                    });

                    image.src = '/public/events/' + eventid + '/thumb_' + filename;


                });


            });
        </script>
    <?php endif; ?>
</div>