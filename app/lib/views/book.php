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
        <hr/>

        <h2>Электронная очередь</h2>

        <div class="row queue">
            <div class="col col-4">
                <div>Текущее состояние: <?= $params["book"]->getQueueStatus(); ?></div>
                <?php if ($params["book"]->isOwned()): ?>
                    <div>Находится у: <?= $params["book"]->getCurrentOwner(); ?></div>
                <?php endif; ?>
            </div>

            <div class="col col-4">
                <?php if ($params["book"]->isOwned()): ?>
                    <div>Книга взята: <?= $params["book"]->getTakingDate(); ?></div>
                    <div>Длинна очереди: <?= $params["book"]->getQueueLength(); ?></div>
                <?php endif; ?>
            </div>

            <div class="col col-4">
                <?php if (!empty($globals["user"]->id)): ?>
                    <?php if ($params["book"]->isOwned()): ?>
                        <?php if ($globals["user"]->isInBookQueue($params["book"]->id)): ?>
                            <a href="/lib/remove_queue/<?= $params["book"]->id; ?>/<?= $globals["user"]->id; ?>"
                               class="btn">Выйти из очереди</a>
                        <?php else: ?>
                            <a href="/lib/add_queue/<?= $params["book"]->id; ?>/<?= $globals["user"]->id; ?>"
                               class="btn">Встать в очередь</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="/lib/add_queue/<?= $params["book"]->id; ?>/<?= $globals["user"]->id; ?>/1"
                           class="btn">Взять книгу</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>