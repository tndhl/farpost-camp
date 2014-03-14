<div class="container library">
    <h2><?php echo $params["category"]["title"]; ?></h2>

    <?php if (empty($params["books"])): ?>
        В этой категории пока нет книг :'(
    <?php endif; ?>

    <?php foreach ($params["books"] as $book): ?>
        <div class="book">
            <div class="image"><img src="/public/images/books/<?php echo $book->image; ?>"></div>
            <div class="description">
                <div><span>Название:</span> <?php echo $book->title; ?></div>
                <div><span>Автор:</span> <?php echo $book->author; ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>