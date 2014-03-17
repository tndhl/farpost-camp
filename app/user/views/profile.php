<div class="container user-profile">
    <h1><?php echo $params["title"]; ?></h1>

    <div class="col-3">
        <img src="/public/images/noavatar.png" width="100%">
    </div>

    <div class="col-9">
        <form method="post">
            <div class="inline">
                <div class="group">
                    <label for="inputLastname">Фамилия</label>
                    <span class="editable" data-type="text" data-name="lastname"><?php echo $params["user"]->lastname; ?></span>
                </div>

                <div class="group">
                    <label for="inputFirstname">Имя</label>
                    <span class="editable" data-type="text" data-name="lastname"><?php echo $params["user"]->firstname; ?></span>
                </div>
            </div>

            <?php foreach ($params["user"]->xfields as $xfield): ?>
                <div class="group">
                    <label for="input<?php echo $xfield["alt"]; ?>"><?php echo $xfield["title"]; ?></label>
                    <span class="editable" data-fid="<?php echo $xfield["id"]; ?>"><?php echo $xfield["value"]; ?></span>
                </div>
            <?php endforeach; ?>
        </form>
    </div>
</div>