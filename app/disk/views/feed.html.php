<table class="files">
    <thead>
    <tr>
        <th class="text-left">Имя файла</th>
        <th>Дата создания</th>
        <th>Размер</th>
        <th></th>
    </tr>
    </thead>

    <tbody>
    <?php if (empty($files)): ?>
        <tr class="empty-list">
            <td colspan="5">Нет ожидающих проверку файлов</td>
        </tr>
    <?php endif; ?>
    <?php foreach ($files as $file): ?>
        <tr data-fileid="<?= $file->file_id; ?>">
            <td class="title"><?= $file->title; ?></td>
            <td class="created"><?= $file->formatCreatedDate(); ?></td>
            <td class="size"><?= $file->formatFileSize(); ?></td>
            <td class="options">
                <a class="file-verify" href="/disk/verify_file/<?= $file->file_id; ?>" title="Одобрить файл"><i class="fa fa-plus"></i></a>
                <a class="file-remove" href="/disk/remove_file/<?= $file->file_id; ?>" title="Удалить файл"><i class="fa fa-trash-o"></i></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>