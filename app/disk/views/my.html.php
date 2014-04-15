<?php if (!empty($title)): ?>
    <h1><?= $title; ?></h1>
<?php endif; ?>

<table class="files">
    <thead>
        <tr>
            <th></th>
            <th class="text-left">Имя файла</th>
            <th>Дата создания</th>
            <th>Размер</th>
            <th></th>
        </tr>
    </thead>

    <tbody>
    <?php if (empty($files)): ?>
        <tr class="empty-list">
            <td colspan="5">У вас нет файлов</td>
        </tr>
    <?php endif; ?>
    <?php foreach ($files as $file): ?>
        <tr data-fileid="<?= $file->file_id; ?>" <?= !$file->verified ? "class='not-verified'" : ""; ?>>
            <td class="select">
                <?php if ($file->verified): ?>
                    <input type="checkbox">
                <?php endif; ?>
            </td>
            <td class="title">
                <?= $file->title; ?>
                <?= !$file->verified ? "<span class='pull-right'>Не проверен</span>" : ""; ?>
            </td>
            <td class="created"><?= $file->formatCreatedDate(); ?></td>
            <td class="size"><?= $file->formatFileSize(); ?></td>
            <td class="options">
                <?php if ($file->verified): ?>
                    <a href="/disk/download_file/<?= $file->file_id; ?>" title="Скачать файл"><i class="fa fa-download"></i></a>
                    <a class="file-remove" href="/disk/remove_file/<?= $file->file_id; ?>" title="Удалить файл"><i class="fa fa-trash-o"></i></a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>