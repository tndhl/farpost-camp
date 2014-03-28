<div class="container user-profile">
    <h1><?= $title; ?></h1>

    <div class="row">
        <div class="col col-3">
            <img src="/public/images/noavatar.png" width="100%">

            <strong>Роли</strong>
            <ul>
                <?php foreach ($user->getRoles() as $role): ?>
                    <li><?= $role["name"]; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="col col-9">
            <form method="post">
                <div class="inline">
                    <div class="group">
                        <label for="inputLastname">Фамилия</label>
                    <span class="editable" data-type="text"
                          data-name="lastname"><?= $user->lastname; ?></span>
                    </div>

                    <div class="group">
                        <label for="inputFirstname">Имя</label>
                    <span class="editable" data-type="text"
                          data-name="lastname"><?= $user->firstname; ?></span>
                    </div>
                </div>

                <?php foreach ($user->xfields as $xfield): ?>
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