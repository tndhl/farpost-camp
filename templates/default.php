<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>FarPost Portal</title>

    <link rel="stylesheet" href="/public/assets/stylesheets/normalize.css">
    <link rel="stylesheet/less" href="/public/assets/stylesheets/template.less?<?php print time(); ?>">
</head>
<body>
    <div class="header">
        <div class="layout">
            <span class="logo"></span>
            <span class="user-links pull-right">
                <a href="/user/signup" title="Регистрация">Регистрация</a> | <a href="/user/signin" title="Войти">Войти</a>
            </span></a>
        </div>

        <div class="top-nav">
            <div class="layout">
                <ul>
                    <li class="active"><a href="/" title="Главная">Главная</a></li>
                    <li><a href="/" title="Документы">Документы</a></li>
                    <li><a href="/" title="Файлообменник">Файлообменник</a></li>
                    <li><a href="/" title="Библиотека">Библиотека</a></li>
                    <li><a href="/" title="Форум">Форум</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="layout" role="main">
        {component}
    </div>

    <div class="footer">
    </div>

    <script src="/public/assets/javascripts/less.js"></script>
</body>
</html>