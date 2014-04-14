<div class="container">
    <h1>Удаление категории</h1>

    Вы хотите удалить категорию <strong><?= $category["title"]; ?></strong>.<br />Вместе с ней удалятся ВСЕ книги,
    находящиеся в ней.<br /><br />

    Вы уверены? <a href="?confirm">Да</a> | <a href="/lib/category/<?= $category["id"]; ?>">Нет</a>
</div>