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
            <td colspan="5">Нет общих файлов</td>
        </tr>
    <?php endif; ?>
    <?php foreach ($files as $file): ?>
        <tr data-fileid="<?= $file->file_id; ?>">
            <td class="title"><?= $file->title; ?></td>
            <td class="created"><?= $file->formatCreatedDate(); ?></td>
            <td class="size"><?= $file->formatFileSize(); ?></td>
            <td class="options">
                <a href="/disk/download_file/<?= $file->file_id; ?>" title="Скачать файл"><i class="fa fa-download"></i></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>