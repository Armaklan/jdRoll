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
	var reloadTime = 2000;
	var lastMsgs = '';
	var lastMsgId = 0;
	var waitForMsg = 0;


	function getMessages() {
		if(waitForMsg == 0)
		{
			waitForMsg = 1;
			$.ajax({
				type: "GET",
				url: BASE_PATH + "/chat/last", 
				timeout: 5000,
				data: {isFirst: isFirstLoad, lastId: lastMsgId},
				success: function(msg){
					if( $("#text").html() != msg ) {

						var completeLoad = true;
						var container = $('#text');
						var content = $('#messageContent');
						var height = content.height()-300;
						var toBottom;

						if(container[0].scrollTop == height)
							toBottom = true;
						else
							toBottom = false;
						
						deleted = msg.deleted;
						lastMsgId = msg.last_id.trim();
						lastMsg = msg.last_msg;

						if(isFirstLoad) {
							var msgs = '';
							var before = lastMsgs.substring(0, lastMsgs.lastIndexOf("</tr>"));
							var after = lastMsgs.substring(lastMsgs.lastIndexOf("</tr>") + 5);
							msgs = before + "</tr>" + lastMsg + after;


							lastMsgs = msgs;

							$("#text").html(msgs);
						} else {
							$("#tableChat tr:last").after(lastMsg);
						}

						deleted.forEach(function(rowId) {
							$('#chat_tr_' + rowId.messageId).remove();
						});

						content = $('#messages_content');
						height = content.height()-300;

						if(toBottom == true)
								container[0].scrollTop = container[0].scrollHeight

						if(scrollBar != true) {
								container.animate({scrollTop: container[0].scrollHeight},2000);
							scrollBar = true;
						}
					}
								
					waitForMsg = 0;
					if(isFirstLoad)
						isFirstLoad = false;
						
				},
				error: function(msg) {
					waitForMsg = 0;
				}
			});
			
		}
	}

	function getOnline() {
		$.ajax({
		    type: "GET",
		    url: BASE_PATH + "/chat/users",
		    success: function(msg){
		    	$("#onlineUsers").html(msg);
		    },
		    error: function(msg) {

			}
		});
	}

	return {
		postMessage : function (user) {
			textMsg=$('#messageChat').val();
			$('#messageChat').val('');
			$.ajax({
			    type: "POST",
			    url: BASE_PATH + "/chat/post",
			    data: {message: textMsg, user: user},
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
			$.ajax({
			    type: "POST",
			    url: BASE_PATH + "/chat/post",
			    data: {message: textMsg, user: user},
			    success: function(msg){

			    },
			    error: function(msg) {
			    	$('#messageChatMobile').val(textMsg);
				}
			});
		},

		deleteLastMessages: function() {
			$.ajax({
			    type: "POST",
			    url: BASE_PATH + "/chat/removelast",
			    data: {nbToDelete: 30},
			    success: function(msg){

			    },
			    error: function(msg) {

				}
			});
		},

		initMessage: function() {
			getMessages();
			window.setInterval(getMessages, reloadTime);
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