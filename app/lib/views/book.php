<div class="container library-book">
    <a class="btn-back" href="/lib/category/<?= $params["book"]->category; ?>">Вернуться назад</a>

    <h1><?= $params["book"]->title; ?></h1>

    <div class="row">
        <div class="col col-3">
            <img src="/public/images/books/<?= $params["book"]->image; ?>">
        </div>

        <div class="col col-9">
            <div><span>Автор</span> <?= $params["book"]->author; ?></div>
            <div><span>Издатель</span> <?= $params["book"]->publisher; ?></div>
            <div><span>Аннотация</span> <?= $params["book"]->annotation; ?></div>
        </div>
    </div>

    <?php if (!$params["book"]->is_ebook): ?>
        <div class="row">
            <h2>История. Электронная очередь</h2>
        </div>
    <?php endif; ?>
</div>