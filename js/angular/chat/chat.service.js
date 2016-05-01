(function() {
    "use strict";

    var NEW_MSG_EVENT = 'chat-new-message',
        INIT_MSG_EVENT = "chat-init-message",
        USER_LOGON_EVENT = 'user-logon',
        DEL_MSG_EVENT = 'chat-delete-message';

    angular
        .module('jdRoll.chat.service', [])
        .service('WebSocket', WebSocketProvider)
        .service('UserProvider', UserProvider)
        .service('ChatProvider', ChatProvider);

    function UserProvider($http) {
        this.all = function() {
            return $http({
                method: 'GET',
                url: 'apiv2/users'
            }).then(function(response) {
                return response.data;
            });
        };

        this.connected = function() {
            return $http({
                method: 'GET',
                url: 'apiv2/users/connected'
            }).then(function(response) {
                return response.data;
            });
        };
    }

    function ChatProvider(WebSocket) {
        var srv = this;
        var messageCallback;
        srv.msgs = [];

        srv.logon = function(username) {
            return WebSocket.logon(username);
        };

        srv.send = function(msg) {
            WebSocket.emit(NEW_MSG_EVENT, msg);
        };

        srv.delete = function(msg) {
            WebSocket.emit(DEL_MSG_EVENT, msg);
        };

        srv.onNewMessage = function(callback) {
            messageCallback = callback;
        };

        WebSocket.on(INIT_MSG_EVENT, function(msgs) {
            msgs.forEach(function (p) {
                srv.msgs.push(p);
            });
            if(messageCallback) messageCallback(true);
        });

        WebSocket.on(DEL_MSG_EVENT, function(msg) {
            var deletedMsg = ctrl.msgs.find(function(m) {
                return m.id === msg.id;
            });
            if(deletedMsg) {
                var index = ctrl.msgs.indexOf(deletedMsg);
                ctrl.msgs.splice(index, 1);
            }
        });

        WebSocket.on(NEW_MSG_EVENT, function(msg) {
            srv.msgs.push(msg);
            if(messageCallback) messageCallback();
        });
    }

    function WebSocketProvider($timeout) {
        var socket = io();

        this.logon = function(username) {
            socket.emit(USER_LOGON_EVENT, username);
        };

        this.on = function(eventName, callback) {
            socket.on(eventName, function(data) {
                $timeout(function() {
                    callback(data);
                });
            });
        };

        this.emit = function(eventName, data) {
            socket.emit(eventName, data);
        };
    }
})();
