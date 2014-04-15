jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop()) + "px");
    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");

    return this;
};

function displayBlock(parameters) {
    var title = parameters.title;
    var message = parameters.html || parameters.message;

    var alertBox = $('.popup-block');
    $(alertBox).hide();

    $(alertBox).find('.title').text(title);
    $(alertBox).find('p').html(message);
    $(alertBox).find('.close').bind('click', function(e){
        e.preventDefault();

        if (parameters.onClose) {
            parameters.onClose();
        }

        $(alertBox).fadeOut('fast');
    });

    $(alertBox).center().fadeIn('fast');
}

function hideMessage() {
    $(document).find('.popup-message').fadeOut('fast').remove();
}

function showMessage(options) {
    var opt = $.extend({
        timeout: 4000
    }, options);

    hideMessage();

    var $messageBox = $('<div class="popup-message"/>').text(opt.message).hide();
    $(document.body).append($messageBox);
    $messageBox.css('left', ($(document).width() - $messageBox.outerWidth()) / 2);

    $messageBox.fadeIn('fast');

    setTimeout(function(){
        $messageBox.fadeOut('fast');
    }, opt.timeout);
}
