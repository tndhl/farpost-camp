jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop()) + "px");
    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");

    return this;
};

function displayBlock(parameters) {
    var title = parameters.title;
    var message = parameters.html || parameters.message;

    var alertBox = $('.popup');
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
