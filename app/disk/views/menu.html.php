<ul class="nav">
    <?php
    foreach ($links as $item) {
        if ($active == $item["url"]) $activeClass = "class='active'";
        else $activeClass = "";

        echo '<li ', $activeClass, '><a href="/', $item["url"], '">', $item["icon"], ' ', $item["title"], '</a></li>';
    }
    ?>
</ul>
