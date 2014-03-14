<div class="col col-4">
    <div class="container">
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
        #content
    </div>
</div>