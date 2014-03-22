<div class="container user-profile">
    <h1><?= $params["title"]; ?></h1>

    <div class="col col-3">
        <img src="/public/images/noavatar.png" width="100%">

        <strong>Роли</strong>
        <ul>
            <?php foreach ($params["user"]->getRoles() as $role): ?>
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
                          data-name="lastname"><?= $params["user"]->lastname; ?></span>
                </div>

                <div class="group">
                    <label for="inputFirstname">Имя</label>
                    <span class="editable" data-type="text"
                          data-name="lastname"><?= $params["user"]->firstname; ?></span>
                </div>
            </div>

            <?php foreach ($params["user"]->xfields as $xfield): ?>
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