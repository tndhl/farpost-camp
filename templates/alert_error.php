<div class="alert">
    <div class="close">&times;</div>
    <span class="error"></span>

    <h2>Что-то пошло не так</h2>

    <p><?= $text; ?></p>

    <script>
        $('.close').click(function () {
            $('.alert').remove();
        });
    </script>
</div>