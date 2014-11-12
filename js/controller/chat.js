/**
 * Control chat ajax action.
 *
 * @package chat
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

var chatControllerImpl = function() {

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

	function initialLoad() {
		var before = lastMsgs.substring(0, lastMsgs.lastIndexOf('</tr>'));
		var after = lastMsgs.substring(lastMsgs.lastIndexOf('</tr>') + 5);
		var msgs = before + '</tr>' + lastMsg + after;


		lastMsgs = msgs;

		$('#text').html(msgs);
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
		if(scrollBar != true) {
			container[0].scrollTop = container[0].scrollHeight;
			scrollBar = true;
		}
	}

	function getMessages() {
		$.ajax({
			type: 'GET',
			url: BASE_PATH + '/chat/last',
			timeout: 5000,
			data: {isFirst: isFirstLoad, lastId: lastMsgId}
		}).
		done(function(msg){
				if( $('#text').html() != msg ) {

					var completeLoad = true;
					var container = $('#text');
					var content = $('#messageContent');

					var isAutoScroll = isAutoScrollOn(content);

					lastMsgId = msg.last_id.trim();
					lastMsg = msg.last_msg;

					if(isFirstLoad) {
						initialLoad();
					} else {
						appendMsg();

					}
					deleteMsg(msg.deleted);

					scrollToBottom(isAutoScroll);
					addScrollbar();

					isFirstLoad = false;

				}
		}).
		always(function() {
			setTimeout(getMessages,reloadTime);
		});
	}

	function activateChat() {
		container = $('#text');
        msgDivHeight = $('#text').height();
		getMessages();
	}

    function keyEvent() {
        $('#messageChat').on('keyup', function(e) {

            if(  $('#messageChat').val() == '@') {
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
			$.ajax({
			    type: 'POST',
			    url: BASE_PATH + '/chat/post',
			    data: {message: textMsg, user: user, to: to},
			    success: function(msg){

			    },
			    error: function(msg) {
			    	$('#messageChat').val(textMsg);
				}
			});
		},

		postMessageMobile : function (user) {
			textMsg=$('#messageChatMobile').val();
            $('#messageChatMobile').val('');
            to='';
			$.ajax({
			    type: 'POST',
			    url: BASE_PATH + '/chat/post',
			    data: {message: textMsg, user: user, to: to},
			    success: function(msg){

			    },
			    error: function(msg) {
			    	$('#messageChatMobile').val(textMsg);
				}
			});
		},

		deleteLastMessages: function() {
			$.ajax({
			    type: 'POST',
			    url: BASE_PATH + '/chat/removelast',
			    data: {nbToDelete: 30},
			    success: function(msg){

			    },
			    error: function(msg) {

				}
			});
		},

		initMessage: function() {
			activateChat();
            keyEvent();
		},

		initOnline: function() {
			getOnline();
			window.setInterval(getOnline, 30000);
		}

	}
}

var chatController = chatControllerImpl();
onLoadController.withChats.push(chatController.initMessage);
onLoadController.withChats.push(chatController.initOnline);
