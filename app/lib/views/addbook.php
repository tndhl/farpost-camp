<div class="container">
    <h1>Форма добавления книги</h1>

    <form method="post" enctype="multipart/form-data" action="/lib/addbook" data-validate-url="/lib/addbook_validate">
        <div class="inline">
            <div class="group">
                <label for="selectCategory">Категория *</label>
                <select class="required" id="selectCategory" name="category">
                <?php foreach ($params["categories"] as $category): ?>
                    <option value="<?php echo $category["id"]; ?>"><?php echo $category["title"]; ?></option>
                <?php endforeach; ?>
                </select>
                <span class="error"></span>
            </div>

            <div class="group">
                <label for="inputTitle">Заголовок *</label>
                <input class="required" id="inputTitle" name="title" type="text">
                <span class="error"></span>
            </div>
        </div>

        <div class="inline">
            <div class="group">
                <label for="inputAuthor">Автор *</label>
                <input class="required" id="inputAuthor" name="author" type="text">
                <span class="error"></span>
            </div>

            <div class="group">
                <label for="inputPublisher">Издатель *</label>
                <input class="required" id="inputPublisher" name="publisher" type="text">
                <span class="error"></span>
            </div>
        </div>

        <div class="group">
            <label for="inputImage">Изображение *</label>
            <input class="required" id="inputImage" name="image" type="file">
            <span class="error"></span>
        </div>

        <div class="group">
            <label for="textareaAnnotation">Аннотация</label>
            <textarea id="textareaAnnotation" name="annotation"></textarea>
            <span class="error"></span>
        </div>

        <button type="submit">Добавить книгу</button>
    </form>
</div>

<script src="/public/assets/javascripts/form.validation.js"></script>