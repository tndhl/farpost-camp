<div class="container user-profile">
    <h1><?= $title; ?></h1>

    <div class="row">
        <div class="col col-3">
            <img src="/public/images/noavatar.png" width="100%">

            <strong>Роли</strong>

            <?php if ($user->hasRole('Администратор')): ?>
                <i title="Добавить роль" class="fa fa-plus" id="user-add-role" data-uid="<?= $profile->id; ?>"></i>
            <?php endif; ?>

            <ul>
                <?php foreach ($profile->getRoles() as $role): ?>
                    <li>
                        <?= $role["name"]; ?>

                        <?php if ($user->hasRole('Администратор') && !$profile->isSuperUser()): ?>
                            <i title="Удалить роль"
                               class="fa fa-minus user-remove-role"
                               data-uid="<?= $profile->id; ?>"
                               data-rid="<?= $role["id"]; ?>"></i>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="col col-9">
            <form method="post" data-userid="<?= $profile->id; ?>">
                <div class="inline">
                    <div class="group">
                        <label for="inputLastname">Фамилия</label>
                        <span class="editable"
                              data-name="lastname"
                              data-html-tag="input"
                              data-html-tag-type="text"><?= $profile->lastname; ?></span>
                    </div>

                    <div class="group">
                        <label for="inputFirstname">Имя</label>
                        <span class="editable"
                              data-name="firstname"
                              data-html-tag="input"
                              data-html-tag-type="text"><?= $profile->firstname; ?></span>
                    </div>
                </div>

                <?php foreach ($profile->xfields as $xfield): ?>
                    <div class="group">
                        <label for="input<?= $xfield["alt"]; ?>"><?= $xfield["title"]; ?></label>
                        <span class="editable"
                              data-name="<?= $xfield["alt"]; ?>"
                              data-html-tag="<?= $xfield["html_tag"]; ?>"
                              data-html-tag-type="<?= $xfield["html_tag_type"]; ?>"
                              data-fid="<?= $xfield["id"]; ?>"><?= !empty($xfield["value"]) ? $xfield["value"] : 'не заполнено'; ?></span>
                    </div>
                <?php endforeach; ?>

                <button type="button" class="btn-default hidden">Сохранить изменения</button>
                <button type="button" class="btn-cancel hidden">Отменить</button>
            </form>
        </div>
    </div>
</div>

<script src="/public/assets/javascripts/profile.edit.js"></script>