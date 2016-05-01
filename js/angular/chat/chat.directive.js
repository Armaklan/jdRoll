(function() {
    "use strict";

    angular
        .module('jdRoll.chat.directive', [
            'jdRoll.chat.service'
        ])
        .directive('chat', chat);

    function chat() {
        return {
            restrict: 'EA',
            templateUrl: 'js/angular/chat/chat.html',
            controller: ChatController,
            scope: {
                height: '='
            }
        };
    }


    function ChatController($timeout, $scope, ChatProvider, UserProvider) {
        ChatProvider.logon(window.USERNAME);
        var ctrl = {};
        $scope.chatCtrl = ctrl;
        var msgDivHeight = $scope.height || 300;

        _build();

        function _build() {
            ctrl.canPost = (window.AUTHENTICATED === "1");
            ctrl.canAdmin = (window.ADM === "1");
            ctrl.height = msgDivHeight;
            ctrl.message = { from: window.USERNAME, to: ""};
            ctrl.username = window.USERNAME;
            ctrl.msgs = ChatProvider.msgs;
            ChatProvider.onNewMessage(onNewMessage);
            _buildUsersConnected();
            _buildUsers();
            _autorefreshUsersConnected();
        }

        ctrl.send = function(message) {
            ChatProvider.send(message);
            message.text = "";
        };

        ctrl.delete = function(message) {
            ChatProvider.delete(message);
        };

        ctrl.deleteLastMessages = function() {
            var deleted = ctrl.msgs.filter(function(m, index) {
                return index > (ctrl.msgs.length < 30);
            });
            deleted.forEach(function(message) {
                ctrl.delete(message);
            });
        };

        ctrl.changeText = function(text) {
            if(text === '@') {
                ctrl.message.text = '';
                try {
                    angular.element($('#chatTo')).controller('uiSelect').activate();
                } catch(e) {
                    // nothing
                }
            }
        };

        function onNewMessage(force) {
            $timeout(function() {
                _scrollToBottom(force);
            });
        }

        function _buildUsersConnected() {
            UserProvider.connected().then(function(users) {
                ctrl.connected = users;
            });
        }

        function _buildUsers() {
            UserProvider.all().then(function(users) {
                ctrl.users = users.map(function (u) {
                    u.value = u.username;
                    return u;
                });
                ctrl.users.push({
                    id: 0,
                    username: 'Tous',
                    value: ""
                });
            });
        }

        function isAutoScrollOn() {
            var container = $('#text');
            var content = $('#messageContent');
            return container.scrollTop() > ( content.height() - msgDivHeight - 100 );
        }

        function _scrollToBottom(force) {
            var isAutoScroll = force || isAutoScrollOn();
            var container = $('#text');
            var content = $('#messageContent');

            if(isAutoScroll === true) {
                container.scrollTop(container.scrollTop() + content.height());
            }
        }

        function _autorefreshUsersConnected() {
            $timeout(function() {
                _buildUsersConnected();
                _autorefreshUsersConnected();
            }, 60000);
        }
    }
})();
