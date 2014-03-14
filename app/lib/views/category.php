<div class="container">
    <h2><?php echo $params["category"]["title"]; ?></h2>

    <?php if (empty($params["books"])): ?>
        В этой категории пока нет книг :'(
    <?php endif; ?>

    <?php foreach ($params["books"] as $book): ?>
        
    <?php endforeach; ?>
</div>