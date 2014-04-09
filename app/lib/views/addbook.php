<div class="container">
    <h1>Форма добавления книги</h1>

    <form method="post" enctype="multipart/form-data" action="/lib/addbook" data-validate-url="/lib/validate">
        <div class="inline">
            <div class="group">
                <label for="selectCategory">Категория *</label>
                <select class="required" id="selectCategory" name="category">
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category["id"]; ?>"><?= $category["title"]; ?></option>
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
            <label for="inputImage">Изображение * <small>(.JPEG, .JPG)</small></label>
            <input class="required" id="inputImage" name="image" type="file">
            <span class="error"></span>
        </div>

        <div class="group">
            <label for="textareaAnnotation">Аннотация</label>
            <textarea id="textareaAnnotation" name="annotation"></textarea>
            <span class="error"></span>
        </div>

        <label for="checkboxEbook">
            <input type="checkbox" name="ebook" class="extra_param" id="checkboxEbook" value="1"> Электронная книга
        </label>

        <br/><br/>

        <div class="group hidden" id="ebook_loader">
            <label for="inputBook">Файл книги * <small>(.PDF, .DJVU)</small></label>
            <input class="required" id="inputBook" name="book" type="file">
            <span class="error"></span>
        </div>

        <button type="submit">Добавить книгу</button>
    </form>
</div>

<script src="/public/assets/javascripts/form.validation.js"></script>
<script>
    $('#checkboxEbook').click(function () {
        if ($(this).is(':checked')) {
            $('#ebook_loader').removeClass('hidden');
        } else {
            $('#ebook_loader').addClass('hidden');
        }
    })
</script>