<div class="container">
    <h1>Форма редактирования книги</h1>

    <form method="post" enctype="multipart/form-data" action="/lib/edit_book/<?= $book->id; ?>" data-validate-url="/lib/validate">
        <div class="inline">
            <div class="group">
                <label for="selectCategory">Категория *</label>
                <select class="required" id="selectCategory" name="category">
                    <?php foreach ($categories as $category): ?>
                        <?php
                            if ($category["id"] == $book->category) $selected = "selected";
                            else $selected = "";
                        ?>
                        <option <?= $selected; ?> value="<?= $category["id"]; ?>"><?= $category["title"]; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error"></span>
            </div>

            <div class="group">
                <label for="inputTitle">Заголовок *</label>
                <input class="required" id="inputTitle" name="title" type="text" value="<?= $book->title; ?>">
                <span class="error"></span>
            </div>
        </div>

        <div class="inline">
            <div class="group">
                <label for="inputAuthor">Автор *</label>
                <input class="required" id="inputAuthor" name="author" type="text" value="<?= $book->author; ?>">
                <span class="error"></span>
            </div>

            <div class="group">
                <label for="inputPublisher">Издатель *</label>
                <input class="required" id="inputPublisher" name="publisher" type="text" value="<?= $book->publisher; ?>">
                <span class="error"></span>
            </div>
        </div>

        <div class="group">
            <label for="textareaAnnotation">Аннотация</label>
            <textarea id="textareaAnnotation" name="annotation"><?= $book->annotation; ?></textarea>
            <span class="error"></span>
        </div>

        <button type="submit">Сохранить книгу</button>
    </form>
</div>

<script src="/public/assets/javascripts/form.validation.js"></script>