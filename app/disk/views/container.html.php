<div class="container disk">
    <script src="/public/assets/javascripts/jquery.disk.actions.js"></script>
    <div class="row">
        <div id="menu" class="col col-3">
            <?= $menu; ?>

            <?= $diskActions; ?>
            <?= $diskInfo; ?>
        </div>
        <div class="col col-9">
            <?= $content; ?>
        </div>
    </div>
</div>

<script>
    var baseTop = $("div#menu").offset().top;

    $(window).scroll(function () {
        var top = $(window).scrollTop();
        if ((top + 15) >= baseTop) {
            $("div#menu").css({
                "position": "relative",
                "top": (top - baseTop + 15) + "px"
            });
        } else if (top < baseTop) {
            $("div#menu").css({
                "position": "",
                "top": ""
            });
        }
    });
</script>