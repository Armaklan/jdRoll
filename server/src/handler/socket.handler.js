var Message = require('../model/message.model.js');

function chatSocketHandler(io, chatProvider) {
    io.on('connection', function(socket) {
        new UserSocket(io, socket, chatProvider);
    });
}

module.exports = chatSocketHandler;

const NEW_MSG_EVENT = 'chat-new-message',
      INIT_MSG_EVENT = 'chat-init-message',
      USER_LOGON_EVENT = 'user-logon',
      DEL_MSG_EVENT = 'chat-delete-message';

function UserSocket(io, socket, chatProvider) {
    socket.on(USER_LOGON_EVENT, logonEvent);
    socket.on(NEW_MSG_EVENT, messageEvent);
    socket.on(DEL_MSG_EVENT, deleteMessageEvent);

    function logonEvent(username) {
        socket.join(username);
        loadInitialMessage(username);
    }

    function loadInitialMessage(username) {
        chatProvider.getLastMessage(username).then((messages) => {
            socket.emit(INIT_MSG_EVENT, messages);
        });
    }

    function messageEvent(data) {
        var message = new Message(data);
        saveMessage(message).then((message) => {
            if(message.isPrivate()) {
                sendPrivateMessage(message);
            } else {
                sendMessage(message);
            }
        });
    }

    function deleteMessageEvent(message) {
        io.emit(DEL_MSG_EVENT, message);
        chatProvider.deleteMessage(message);
    }

    function sendPrivateMessage(message) {
        socket.to(message.to).emit(NEW_MSG_EVENT, message);
        socket.emit(NEW_MSG_EVENT, message);
    }

    function sendMessage(message) {
        io.emit(NEW_MSG_EVENT, message);
    }

    function saveMessage(message) {
        return chatProvider.saveMessage(message).then((id) => {
            message.id = id;
            return message;
        });
    }
}
