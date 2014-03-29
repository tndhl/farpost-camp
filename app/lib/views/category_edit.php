<div class="container">
    <h1>Форма изменения раздела</h1>

    <form method="post" action="/lib/edit_category/<?= $category["id"]; ?>" data-validate-url="/lib/validate">
        <div class="group">
            <label for="inputTitle">Заголовок *</label>
            <input class="required" id="inputTitle" name="title" type="text" value="<?= $category["title"]; ?>">
            <span class="error"></span>
        </div>

        <button type="submit">Изменить раздел</button>
    </form>
</div>

<script src="/public/assets/javascripts/form.validation.js"></script>