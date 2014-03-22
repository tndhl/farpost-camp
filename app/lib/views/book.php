<div class="container library-book">
    <a class="btn-back" href="/lib/category/<?= $params["book"]->category; ?>">Вернуться назад</a>

    <div class="col col-3">
        <img src="/public/images/books/<?= $params["book"]->image; ?>">
    </div>

    <div class="col col-9">
        <div><span>Название</span> <?= $params["book"]->title; ?></div>
        <div><span>Автор</span> <?= $params["book"]->author; ?></div>
        <div><span>Издатель</span> <?= $params["book"]->publisher; ?></div>
        <div><span>Аннотация</span> <?= $params["book"]->annotation; ?></div>
    </div>
</div>