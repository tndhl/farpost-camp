function Messanger(params) {
    this.options = $.extend({
        timeout: 360
    }, params);

    this.last_msg_id = 0;
    this.connection = 0;
    this.connectTimeout = 0;

    var self = this;

    this.sendMessage = function(e) {
        var message = self.options.messenger.find('input#message');

        if (message.length == 0) {
            showMessage({message: 'Сообщение должно быть не пустым'});
            return;
        }

        $.ajax({
            type: "POST",
            url: "/chat/sendMessage",
            data: { ajax: true, 'message': $(message).val() },
            dataType: "json",
            success: function (result) {
                if (result.error) {
                    showMessage({message: result.error});
                } else {
                    $(message).val('');

                    self.putMessage(result.id, result.username, result.datetime, result.message);
                    self.reconnect();
                }
            }
        });
    };

    this.putMessage = function (id, username, datetime, msg) {
        self.last_msg_id = id;

        var messages = self.options.messenger.find('.messages');

        var message = document.createElement('div');
        message.className = 'message';

        message.innerHTML =
            '<span class="info">' +
                '<span class="username">' + username + '</span> ' +
                '<span class="datetime">(' + datetime + ')</span>' +
            '</span>' +
            '<span class="text">' + msg + '</span>';

        messages.append(message);

        messages.scrollTop(messages[0].scrollHeight);
    };

    this.onDataReceived = function (result) {
        if (result.length == 0) return;

        for (var key in result) {
            if (result.hasOwnProperty(key)) {
                self.putMessage(result[key].id, result[key].username, result[key].datetime, result[key].message);
            }
        }

        self.connectTimeout = setTimeout(self.connect, 1000);
    };

    this.reconnect = function() {
        self.connection.abort();
        clearTimeout(self.connectTimeout);

        self.connect();
    };

    this.connect = function () {
        self.connection = $.ajax({
            type: "POST",
            url: "/chat/getMessageList",
            data: { ajax: true, 'last_msg_id': self.last_msg_id },
            dataType: "json",
            timeout: self.options.timeout * 1000,
            success: function (result) {
                self.onDataReceived(result);
            },
            error: function () {
                self.connectTimeout = setTimeout(self.connect, 1000);
            }
        });
    };

    this.init = function () {
        self.options.messenger.find('button').bind('click', self.sendMessage);
        self.options.messenger.find('#message').keypress(function(event) {
            if (event.keyCode == 13) {
                self.sendMessage();
            }
        });

        self.connect();
    };
}

$(document).ready(function () {
    var $messenger_html = $('.messenger');

    var messenger = new Messanger({
        messenger: $messenger_html
    });

    messenger.init();

    $messenger_html.find('.title').click(function () {
        if ($messenger_html.css('bottom').replace('px', '') == 0) {
            $messenger_html.css('bottom', -353);
        } else {
            $messenger_html.css('bottom', 0);
        }
    })
});