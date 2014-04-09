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
                               class="fa fa-minus"
                               id="user-remove-role"
                               data-uid="<?= $profile->id; ?>"
                               data-rid="<?= $role["id"]; ?>"></i>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>


        </div>

        <div class="col col-9">
            <form method="post">
                <div class="inline">
                    <div class="group">
                        <label for="inputLastname">Фамилия</label>
                    <span class="editable" data-type="text"
                          data-name="lastname"><?= $profile->lastname; ?></span>
                    </div>

                    <div class="group">
                        <label for="inputFirstname">Имя</label>
                    <span class="editable" data-type="text"
                          data-name="lastname"><?= $profile->firstname; ?></span>
                    </div>
                </div>

                <?php foreach ($profile->xfields as $xfield): ?>
                    <div class="group">
                        <label for="input<?= $xfield["alt"]; ?>"><?= $xfield["title"]; ?></label>
                    <span class="editable" data-fid="<?= $xfield["id"]; ?>">
                        <?= $xfield["value"] ? $xfield["value"] : 'NULL'; ?>
                    </span>
                    </div>
                <?php endforeach; ?>
            </form>
        </div>
    </div>
</div>