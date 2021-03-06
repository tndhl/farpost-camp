<?php if ($user->hasRole('Администратор')): ?>
    <div class="admin-menu">
        <span>Управление</span>

        <div class="actions">
            <ul>
                <li><a href="/lib/remove_book/<?= $book->id; ?>">Удалить книгу</a></li>
                <li><a href="/lib/edit_book/<?= $book->id; ?>">Редактировать книгу</a></li>
            </ul>
        </div>
    </div>
<?php endif; ?>

<div class="container library-book">
    <a class="btn-back" href="/lib/category/<?= $book->category; ?>">Вернуться назад</a>

    <h1><?= $book->title; ?></h1>

    <div class="row">
        <div class="col col-3">
            <img src="/public/books/images/<?= $book->image; ?>">
        </div>

        <div class="col col-9">
            <div><span>Автор</span> <?= $book->author; ?></div>
            <div><span>Издатель</span> <?= $book->publisher; ?></div>
            <div><span>Аннотация</span> <?= $book->annotation; ?></div>
        </div>
    </div>

    <?php if (!$book->is_ebook): ?>
        <hr/>

        <h2>Электронная очередь</h2>

        <div class="row queue">
            <div class="col col-4">
                <div>Текущее состояние: <?= $book->getQueueStatus(); ?></div>
                <?php if ($book->isOwned()): ?>
                    <div>Находится у: <?= $book->getCurrentOwner(); ?></div>
                <?php endif; ?>
            </div>

            <div class="col col-4">
                <?php if ($book->isOwned()): ?>
                    <div>Книга взята: <?= $book->getTakingDate(); ?></div>
                    <div>Длинна очереди: <?= $book->getQueueLength(); ?></div>
                <?php endif; ?>
            </div>

            <div class="col col-4">
                <?php if (!empty($user->id)): ?>
                    <?php if ($book->isOwned()): ?>
                        <?php if ($user->isInBookQueue($book->id)): ?>
                            <a href="/lib/remove_queue/<?= $book->id; ?>/<?= $user->id; ?>"
                               class="btn-default">Выйти из очереди</a>
                        <?php else: ?>
                            <a href="/lib/add_queue/<?= $book->id; ?>/<?= $user->id; ?>"
                               class="btn-default">Встать в очередь</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="/lib/add_queue/<?= $book->id; ?>/<?= $user->id; ?>/1"
                           class="btn-default">Взять книгу</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>