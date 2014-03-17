<div class="col col-4">
    <div class="container">
<<<<<<< HEAD
        <?php if ($globals["user"]->isAdmin()): ?>
            <a href="/lib/addcategory" class="add-btn">Добавить категорию</a>
        <?php endif; ?>
=======
        <a href="/lib/addcategory" class="add-btn">Добавить категорию</a>
>>>>>>> 371e20d8bc2d8d24ab0de4e448b666fa2a1acafd

        <h3>Категории</h3>
        <ul class="library-category-list">
        <?php foreach ($params["categories"] as $category): ?>
            <li><a href="/lib/category/<?php echo $category["id"]; ?>" title="<?php echo $category["title"]; ?>"><?php echo $category["title"]; ?></a></li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="col col-8">
    <div class="container">
        <a href="/lib/addbook" class="add-btn">Добавить книгу</a>
        #content
    </div>
</div>