<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>FarPost Portal</title>

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/normalize/3.0.1/normalize.min.css">
    <link rel="stylesheet/less" href="/public/assets/stylesheets/template.less?<?php print time(); ?>">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="/public/assets/javascripts/functions.js"></script>
    <script src="/public/assets/javascripts/administrator.js"></script>
</head>
<body>
<div class="header">
    <div class="layout">
        <span class="logo"></span>
        <span class="user-links pull-right">
            <?= $userlinks; ?>
        </span>
    </div>

    <div class="top-nav">
        <div class="layout">
            <ul>
                <li class="active"><a href="/" title="Главная">Главная</a></li>
                <li><a href="/news" title="Новости и уведомления">Новости</a></li>
                <li><a href="/" title="Документы">Документы</a></li>
                <li><a href="/" title="Файлообменник">Файлообменник</a></li>
                <li><a href="/lib" title="Библиотека">Библиотека</a></li>
                <li><a href="/" title="Форум">Форум</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="layout" role="main">
    {alert}

    {content}
</div>

<div class="footer">
</div>

<div class="popup">
    <div class="title"></div>
    <a class="close" href="#">&times;</a>

    <p></p>
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/1.7.0/less.min.js"></script>
</body>
</html>