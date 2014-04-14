<div class="container">
    <h1>Удаление книги</h1>

    Вы хотите удалить книгу <strong><?= $book->title; ?></strong>.<br />
    Удаление нельзя будет отменить.<br /><br />

    Вы уверены? <a href="?confirm">Да</a> | <a href="/lib/book/<?= $book->id; ?>">Нет</a>
</div>