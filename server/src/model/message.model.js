"use strict";

class Message {

    constructor(message) {
        if(message && message.from) {
            this.fromEvent(message);
        } else {
            this.fromDb(message);
        }
    }

    isPrivate() {
        return this.to ? true : false;
    }

    fromEvent(message) {
        this.id = message.id;
        this.to = message.to;
        this.from = message.from;
        this.text = message.text;
        this.time = new Date();
        this.private = this.isPrivate();
        parseChatMsg(this);
    }

    fromDb(data) {
        this.id = data.id;
        this.to = data.to_username;
        this.from = data.username;
        this.text = data.message;
        this.time = data.time;
        this.private = this.isPrivate();
    }
}

module.exports = Message;


function parseChatMsg(message) {
    //On strip les tags HTML
    var cleanText = message.text.replace(/<\/?[^>]+(>|$)/g, "");

		//On remplace la forme HTML du '<' par son équivalent ascii
    cleanText = cleanText.replace(/&lt/g, "<");

    cleanText = urlLink(cleanText);
    cleanText = parseSmiley(cleanText);

    if(cleanText.search('/me') > -1) {
        cleanText = cleanText.replace("/me", message.from + " ");
				cleanText = `<span class="dialogue"><span style="font-size: 8.5pt; font-family: 'Verdana','sans-serif'; color: #4488cc;">${cleanText}</span></span>`;
        message.from = "";
    }

    message.text = cleanText;
}

function urlLink(text) {
    var urlRegex = /(https?:\/\/[^\s]+)/g;
    return text.replace(urlRegex, function(url) {
        return '<a href="' + url + '" target="_blank">' + url.substring(0, 50) + '...</a>';
    });
}

function parseSmiley(cleanText) {
    const tinymce_emoticon = "../../../../vendor/tinymce/plugins/emoticons/img/";
    cleanText = cleanText.replace(":)", "<img src='" + tinymce_emoticon + "smiley-smile.gif' alt=''>");
    cleanText = cleanText.replace(";)", "<img src='" + tinymce_emoticon + "smiley-wink.gif' alt=''>");
    cleanText = cleanText.replace(":p", "<img src='" + tinymce_emoticon + "smiley-tongue-out.gif' alt=''>");
    cleanText = cleanText.replace(":X", "<img src='" + tinymce_emoticon + "smiley-sealed.gif' alt=''>");
    cleanText = cleanText.replace(":'(", "<img src='" + tinymce_emoticon + "smiley-cry.gif' alt=''>");
    cleanText = cleanText.replace("8-)", "<img src='" + tinymce_emoticon + "smiley-cool.gif' alt=''>");
    cleanText = cleanText.replace("o-)", "<img src='" + tinymce_emoticon + "smiley-innocent.gif' alt=''>");
    cleanText = cleanText.replace(":D", "<img src='" + tinymce_emoticon + "smiley-laughing.gif' alt=''>");
    cleanText = cleanText.replace(":mrgreen:", "<img src='../../../../img/smileys-mrgreen.gif' alt=''>");
    return cleanText;
}
