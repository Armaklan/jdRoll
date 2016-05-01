/**
 * Control chat ajax action.
 *
 * @package chat
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

var chatControllerImpl = function() {
  var wsController = undefined;

  var scrollBar = false;
  var isFirstLoad = true;
  var reloadTime = 1500;
  var lastMsgs = '';
  var container;
  var lastMsgId = 0;
  var msgDivHeight = 300;


  function isAutoScrollOn(content) {
    return container[0].scrollTop == ( content.height() - msgDivHeight );
  }

  function appendMsg() {
    $('#tableChat tr:last').after(lastMsg);
  }

  function deleteMsg(deleted) {
    deleted.forEach(function(rowId) {
      $('#chat_tr_' + rowId.messageId).remove();
    });
  }

  function scrollToBottom(isAutoScroll) {
    var height = $('#messages_content').height() - msgDivHeight;

    if(isAutoScroll === true) {
      container[0].scrollTop = container[0].scrollHeight;
    }
  }

  function addScrollbar() {
    if(scrollBar !== true) {
      container[0].scrollTop = container[0].scrollHeight;
      scrollBar = true;
    }
  }

  function getMessages() {
      if( $('#text').html() != msg ) {

        var isAutoScroll = isAutoScrollOn(content);

        scrollToBottom(isAutoScroll);
        addScrollbar();

        isFirstLoad = false;

      }
  }

  function keyEvent() {
    $('#messageChat').on('keyup', function(e) {

      if($('#messageChat').val() == '@') {
        $('#chatTo').select2('open');
        $('#messageChat').val('');
      }

    });
  }

  function getOnline() {
    $.ajax({
      type: 'GET',
    url: BASE_PATH + '/chat/users',
    success: function(msg){
      $('#onlineUsers').html(msg);
    },
    error: function(msg) {

    }
    });
  }

  return {
    postMessage : function (user) {
      textMsg=$('#messageChat').val();
      $('#messageChat').val('');
      to=$('#chatTo').val();
      wsController.sendMessage({
        to: to,
        text: textMsg,
        from: user
      });
    },

    postMessageMobile : function (user) {
      textMsg=$('#messageChatMobile').val();
      $('#messageChatMobile').val('');
      to='';
      wsController.sendMessage({
        to: to,
        text: textMsg,
        from: user
      });
    },

    deleteLastMessages: function() {
      // TODO - Chat Event
    },

    initMessage: function() {
      wsController = new ChatWsController();
      keyEvent();
    },

    initOnline: function() {
      getOnline();
      window.setInterval(getOnline, 30000);
    }

  };
};

function ChatWsController() {

  var NEW_MSG_EVENT = 'chat-new-message',
      INIT_MSG_EVENT = "chat-init-message",
      USER_LOGON_EVENT = 'user-logon',
      DEL_MSG_EVENT = 'chat-delete-message';

  socket.on(NEW_MSG_EVENT, onMessage);
  socket.on(DEL_MSG_EVENT, onMessageDeleted);
  socket.on(INIT_MSG_EVENT, onMessageInitialize);

  this.sendMessage = sendMessage;
  this.deleteMessage = deleteMessage;

  var that = this;
  var socket = io();
  logon();

  function logon() {
    socket.emit(USER_LOGON_EVENT, "toto");
  }

  function sendMessage(msg) {
    socket.emit(NEW_MSG_EVENT, msg)
      console.log(msg);
  }

  function deleteMessage(msg) {
    console.log(msg);
  }

  function onMessage(msg) {
    console.log(msg);
  }

  function onMessageDeleted(msg) {
    console.log(msg);
  }

  function onMessageInitialize(msgs) {
    console.log(msgs);
  }
}

var chatController = chatControllerImpl();
onLoadController.withChats.push(chatController.initMessage);
onLoadController.withChats.push(chatController.initOnline);
