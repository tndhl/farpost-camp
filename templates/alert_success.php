<div class="alert">
    <div class="close">&times;</div>
    <span class="success"></span>

    <h2>Ура, все получилось!</h2>

    <p><?= $text; ?></p>

    <script>
        $('.close').click(function () {
            $('.alert').remove();
        });
    </script>
</div>