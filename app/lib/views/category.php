<div class="container library">
    <h2><?= $params["category"]["title"]; ?></h2>

    <a href="/lib" class="btn-back">Вернуться назад</a>

    <?php if (empty($params["books"])): ?>
        В этой категории пока нет книг :'(
    <?php else: ?>
        <div class="row">
            <div class="col col-6">
                <?php for ($i = 0; $i < count($params["books"]); $i = $i + 2): ?>
                    <div class="book">
                        <div class="image">
                            <a href="/lib/book/<?= $params["books"][$i]->id; ?>">
                                <img src="/public/images/books/<?= $params["books"][$i]->image; ?>">
                            </a>
                        </div>
                        <div class="description">
                            <div><span>Название:</span> <?= $params["books"][$i]->title; ?></div>
                            <div><span>Автор:</span> <?= $params["books"][$i]->author; ?></div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="col col-6">
                <?php for ($i = 1; $i < count($params["books"]); $i = $i + 2): ?>
                    <div class="book">
                        <div class="image">
                            <a href="/lib/book/<?= $params["books"][$i]->id; ?>">
                                <img src="/public/images/books/<?= $params["books"][$i]->image; ?>">
                            </a>
                        </div>
                        <div class="description">
                            <div><span>Название:</span> <?= $params["books"][$i]->title; ?></div>
                            <div><span>Автор:</span> <?= $params["books"][$i]->author; ?></div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="pagination">
            {pagination}
        </div>
    <?php endif; ?>
</div>