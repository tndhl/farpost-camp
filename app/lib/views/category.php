<?php if ($user->hasRole('Администратор')): ?>
    <div class="admin-menu">
        <span>Управление</span>

        <div class="actions">
            <ul>
                <li><a href="/lib/remove_category/<?= $category["id"]; ?>">Удалить раздел</a></li>
                <li><a href="/lib/edit_category/<?= $category["id"]; ?>">Редактировать раздел</a></li>
            </ul>
        </div>
    </div>
<?php endif; ?>

<div class="container library">
    <h2><?= $category["title"]; ?></h2>

    <a href="/lib" class="btn-back">Вернуться назад</a>

    <?php if (empty($books)): ?>
        В этой категории пока нет книг :'(
    <?php else: ?>
        <div class="row">
            <div class="col col-6">
                <?php for ($i = 0; $i < count($books); $i = $i + 2): ?>
                    <div class="book">
                        <div class="image">
                            <a href="/lib/book/<?= $books[$i]->id; ?>">
                                <img src="/public/images/books/<?= $books[$i]->image; ?>">
                            </a>
                        </div>
                        <div class="description">
                            <div><span>Название:</span> <?= $books[$i]->title; ?></div>
                            <div><span>Автор:</span> <?= $books[$i]->author; ?></div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="col col-6">
                <?php for ($i = 1; $i < count($books); $i = $i + 2): ?>
                    <div class="book">
                        <div class="image">
                            <a href="/lib/book/<?= $books[$i]->id; ?>">
                                <img src="/public/images/books/<?= $books[$i]->image; ?>">
                            </a>
                        </div>
                        <div class="description">
                            <div><span>Название:</span> <?= $books[$i]->title; ?></div>
                            <div><span>Автор:</span> <?= $books[$i]->author; ?></div>
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