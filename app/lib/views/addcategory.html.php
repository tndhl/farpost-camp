<div class="container">
    <h1>Форма добавления раздела</h1>

    <form method="post" action="/lib/addcategory" data-validate-url="/lib/validate">
        <div class="group">
            <label for="inputTitle">Заголовок *</label>
            <input class="required" id="inputTitle" name="title" type="text">
            <span class="error"></span>
        </div>

        <button type="submit">Добавить раздел</button>
    </form>
</div>