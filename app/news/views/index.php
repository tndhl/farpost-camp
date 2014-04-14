<?php foreach ($news as $item): ?>
    <div class="container news-item">
        <header>
            <h1><?= $item["title"]; ?></h1>

            <span><time><?= $item["date"]; ?></time></span>
            <span>#<?= $item["tag"]; ?></span>
        </header>

        <p><?= $item["content"]; ?></p>

        <div class="footer">
            <a class="btn pull-right" href="/news/<?= $item['id']; ?>">Подробнее</a>
        </div>
    </div>
<?php endforeach; ?>