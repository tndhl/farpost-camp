<?php if (!empty($params["alert"])) echo $params["alert"]; ?>

<div class="container">
    <h1>Авторизация</h1>

    <form method="post" data-validate-url="/user/validate">
        <div class="group group-inline">
            <label for="inputLogin">Логин (E-mail) *</label>
            <input class="required" type="text" name="login" id="inputLogin">
            <span class="error"></span>
        </div>

        <div class="group">
            <label for="inputPassword">Пароль *</label>
            <input class="required" type="password" name="password" id="inputPassword">
            <span class="error"></span>
        </div>

        <button type="submit">Продолжить</button>
    </form>
</div>

<script src="/public/assets/javascripts/form.validation.js"></script>