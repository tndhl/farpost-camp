<?php if (!empty($roles)): ?>
    <form id="form-add-role">
        <div class="group">
            <select>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role["id"]; ?>"><?= $role["name"]; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button>Добавить выбранную роль</button>
    </form>
<?php else: ?>
    У данного пользователя уже есть все роли ;)
<?php endif; ?>