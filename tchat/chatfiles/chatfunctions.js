// PHP Script Chat - http://coursesweb.net

var chatuserset = 0;        // used to check if chat user name is set
var logoutchat = 0;          // is set 1 wheb user leaves the chat, to remove it from Online list
var chatroom = document.getElementById('chatroom') ? document.getElementById('chatroom').value : document.getElementById('s_room').innerHTML;        // store chat room
var callphp = 0;             // number of seconds till access frequently "setchat.php"
var nrchatusers = 1;         // store number of chat users online
var setchat = 'chatfiles/setchat.php';           // file accesed when add chat, or actualise user
var getchat = function() {return 'chattxt/'+chatroom+'.txt';};        // TXT file with chat content of current room
var ajxsend = 0;                                 // to control accessing Ajax 
var chatbeep = 1;                                // store value from <q>...</q> to beep when change
var playbeep = 2;                                // if 1 not beep, if 2 beep
var beepfile = 'beep1.wav';                      // the name of WAV file used for beep sound

/** Functions for cookie **/

// Check cookie and return the value
function GetCookie(name) {
  var result = '0';
  var myCookie = " " + document.cookie + ";";      // all the strings for cookie start with space, end with ;
  var searchName = " " + name + "=";      // search for data between 'name' and the next '='
  var startOfCookie = myCookie.indexOf(searchName);
  if (startOfCookie != -1) {      // if found data
    startOfCookie += searchName.length;      // omitte the previous cookie name
    var endOfCookie = myCookie.indexOf(";", startOfCookie);
    result = unescape(myCookie.substring(startOfCookie, endOfCookie));
  }
  return result;
}

// to deleete cookie
function delCookie(name) {
  var aday = 3*24*60*60*1000;
  var expDate = new Date();
  expDate.setTime (expDate.getTime() - aday);
  document.cookie = name + "=deletes; expires=" + expDate.toGMTString();

  // hide field to add texts and shows area to add name /code, or to enter in chat
  if(document.getElementById('name_code')) document.getElementById('name_code').style.display = 'block';
  if(document.getElementById('chatadd')) document.getElementById('chatadd').style.display = 'none';

  logoutchat = 1;       // set to delete the user from list
  chatuserset = 0;     // set not with a name in chat
}

// If no user added in form, calls to get chat user name from cookie
var cookie_namec = (document.getElementById('chatuser') && document.getElementById('chatuser').value.length>1) ? '0' : GetCookie('name_c');

if (cookie_namec!='0' && document.getElementById('name_code')) {
  callphp = 0;         // set "callphp" to 0 to force next ajax access to php file

  // Hides name /code, show field to add text chat
  document.getElementById('name_code').style.display = 'none';
  document.getElementById('chatadd').style.display = 'block';

  // Add cookie value in form to #chatuser
  document.getElementById('chatuser').value = cookie_namec;
  logoutchat = 0;     // set to not delete the user from list
  chatuserset = 1;
}

// If it is set cookie for room
var cookie_roomc = GetCookie('room_c');            // get chat room name from cookie
if (cookie_roomc!='0') {
  var chatrooms = document.getElementById('chatrooms').getElementsByTagName('span');    // Get <span> with chatroom
  
  // Removes "id" from <span> with chatroom, and set this ID to Span with room name from cookie
  for (var i=0; i<chatrooms.length; i++) {
    chatrooms[i].removeAttribute("id");
    if(chatrooms[i].innerHTML == cookie_roomc) chatrooms[i].setAttribute("id", "s_room");
  }
  if(document.getElementById('chatroom')) document.getElementById('chatroom').value = cookie_roomc;      // Change the value of form field for chatroom
  chatroom = cookie_roomc;              // set chat room in variable used for the name of TXT file for current room
}

    /* Function for audio beep */

// If it is set cookie for audio beep, sets playbeep with the value from cookie
var cookie_beepc = GetCookie('beep_c');
if(cookie_beepc !== '0' && document.getElementById('playbeep')) {
  playbeep = cookie_beepc;
  document.getElementById('playbeep').src = 'chatex/playbeep'+playbeep+'.png';
}

// Receives the text with chats, get and set value for "chatbeep" (within <q>..</q>)
// if "chatbeep" changed, adds <embed> in #chatbeep to play
function playBeep(chats) {
  if(chats.match(/\<q\>([^\<]+)\<\/q\>/)) {
    var lastchat = chats.match(/\<q\>([^\<]+)\<\/q\>/)[1];
    if(lastchat != chatbeep) {
      chatbeep = lastchat;
      document.getElementById('chatbeep').innerHTML= '<audio autoplay="autoplay" src="chatex/'+beepfile+'" type="audio/wav"><embed src="chatex/'+beepfile+'" hidden="true" autostart="true" loop="false" /></audio>';
    }
  }
}

// sets sound-beep on or off (playbeep 2 or 1), change image for playBeep()
function setPlayBeep(imgset) {
  playbeep = (playbeep == 1) ? 2 : 1;
  imgset.src = 'chatex/playbeep'+playbeep+'.png';

    // Sets data in cookie
    var name_cookie = 'beep_c';
    var val_cookie = playbeep;
    var oned = 48*60*60*1000;      // Cookie expiration, two days in milliseconds
    var expDate = new Date();
    expDate.setTime(expDate.getTime()+oned);

    document.cookie = name_cookie + "=" + escape(val_cookie) + "; expires=" + expDate.toGMTString();     // sets cookie
}

/** Functions for checkings and settings **/

// Check if the name is already used
function checkNameC(name_c) {
  var nameused = 0;
  if(document.getElementById('chatusersli')) {
    var chatusersli = document.getElementById('chatusersli').getElementsByTagName('li');    // gets the list with chat users
    var nrchatusersli = chatusersli.length;

    // Traverse chat users list
    for (var i=0; i<nrchatusersli; i++) {
      if (chatusersli[i].innerHTML.match(/[^\<]+/)==name_c) {
        nameused = 1;
        break;
      }
    }
  }
  return nameused;
}

// Changes chat room
function setChatRoom(room) {
  var chatrooms = document.getElementById('chatrooms').getElementsByTagName('span');    // Get <span> with chatroom
  
  // Removes "id" from <span> with chatroom
  for (var i=0; i<chatrooms.length; i++) {
    chatrooms[i].removeAttribute("id");
  }

  if(document.getElementById('chatroom')) document.getElementById('chatroom').value = room.innerHTML;      // Change the value of form field for chatroom
  chatroom = room.innerHTML;
  room.setAttribute("id", "s_room");      // Add id="s_room" to clicked Span
  document.getElementById('chats').innerHTML = texts['loadroom'];
  callphp = 0;         // set "callphp" to 0 to force next ajax access to php file

   // Set to register current room name in cookie
  var name_cookie = 'room_c';
  var val_cookie = room.innerHTML;
  var onew = 7*24*60*60*1000;      // Expiration time, one week, in milisecond
  var expDate = new Date();
  expDate.setTime(expDate.getTime()+onew);

  document.cookie = name_cookie + "=" + escape(val_cookie) + "; expires=" + expDate.toGMTString();   // set cookie
}

// return number of chat users online, from the list #chatusersli
function getNrChatUsers(){
  if(document.getElementById('chatusersli')) {
    return document.getElementById('chatusersli').getElementsByTagName('li').length;
  }
  else return 1;
}

// Check name /code, set cookie, show field to add chat
function setNameC(frm) {
  var chatuser = frm.chatuser.value;
  // In name not between 2 and 12 characters
  if (chatuser.length<2 || chatuser.length>12) {
    alert(texts['err_name']);
    frm.chatuser.focus();
    return false;
  }
  // If incorrect code
  else if (frm.cod.value.length<4 || frm.cod.value!=document.getElementById('code_ch').innerHTML) {
    alert(texts['err_vcode']);
    document.getElementById('code_ch').style.color = 'red';
    frm.cod.focus();
    frm.cod.select();
    return false;
  }
  else if (checkNameC(chatuser)==1) {
    alert(chatuser+texts['err_nameused']);
    frm.chatuser.select();
  }
  // If correct code and name
  else {
    // Sets data for cookie
    var name_cookie = 'name_c';
    var val_cookie = chatuser;
    var oned = 24*60*60*1000;      // Cookie expiration, one day in milliseconds
    var expDate = new Date();
    expDate.setTime(expDate.getTime()+oned);

    document.cookie = name_cookie + "=" + escape(val_cookie) + "; expires=" + expDate.toGMTString();     // sets cookie

    // Hides name /code, show field to add text chat, delete the code
    document.getElementById('name_code').style.display = 'none';
    document.getElementById('chatadd').style.display = 'block';
  frm.cod.value = '';
  logoutchat = 0;     // set to not delete the user from list
    return chatuserset = 1;
  }
}

// function called when logged user click to enter in chat
function enterChat() {
  logoutchat = 0;     // set to not delete the user from list
  chatuserset = 1;
    document.getElementById('name_code').style.display = 'none';
    document.getElementById('chatadd').style.display = 'block';
  callphp = 0;         // set "callphp" to 0 to force next ajax access to php file
}

// check number of characters in field that adds chat text
function addChatS(text) {
  if (chatuserset == 1) {
    var chat = text.adchat.value.length;

    if(chat < 2 || chat > 200) {
      alert(texts['err_textchat']);
      text.adchat.focus();
    }
    else {
      // sends data to Ajax
      var  send_chat = "adchat="+text.adchat.value+"&chatuser="+text.chatuser.value;
      ajxsend = 1;         // Ajax busy now 

      ajaxF(setchat, send_chat);
      text.adchat.value = '';
    }
  }
  else setNameC(text);

  return false;
}

/** Start - functions to add URL, Format text, and Smiles in textarea **/

// check and pass the URL
function setUrl(idadd) {
  var url = window.prompt(texts['addurl']);    // open Prompt to add URL

  // check if a correct URL (without http://), send it to addChatBIU(), else alert
  if (url.match(/^(www.){0,1}([a-zA-z0-9_,+ -]+[.]+)/)) addChatBIU('[url='+url+']','[/url]', idadd);
  else alert(texts['err_addurl']);
}

// Adaugare font B, I, U
function addChatBIU(start, end, zona) {
  var adchat = document.getElementById(zona);
  var IE = /*@cc_on!@*/false;    // this variable is false in all browsers, except IE

  if (IE) {
    adchat.value = adchat.value + start + end;    // Add in field the initial values + received dta
    var pos = adchat.value.length - end.length;    // Sets location for cursor position

    // position the cursor through a selected area
    range = adchat.createTextRange();
    range.collapse(true);
    range.moveEnd('character', pos);        // start position
    range.moveStart('character', pos);        // end position
    range.select();                 // selects the zone
  }
  else if (adchat.selectionStart || adchat.selectionStart == "0") {
    var startPos = adchat.selectionStart;
    var endPos = adchat.selectionEnd;
    adchat.value = adchat.value.substring(0, startPos) + start + adchat.value.substring(startPos, endPos) + end + adchat.value.substring(endPos, adchat.value.length);

    // Place the cursor between formats in #adchat
    adchat.setSelectionRange((endPos+start.length),(endPos+start.length));
    adchat.focus();
  }
}

// for clicked smile in element with ID passed in "idadd"
function addSmile(smile, idadd) {
  var tarea_com = document.getElementById(idadd);
  tarea_com.value += smile;
  tarea_com.focus();
}

/** Functions for Ajax **/

// Start AJAX 
function ajaxRequest(){
 var activexmodes=["Msxml2.XMLHTTP", "Microsoft.XMLHTTP"] //activeX versions to check for in IE
 if (window.ActiveXObject){      //Test for support for ActiveXObject in IE first (as XMLHttpRequest in IE7 is broken)
  for (var i=0; i<activexmodes.length; i++){
   try{
    return new ActiveXObject(activexmodes[i])
   }
   catch(e){
    //suppress error
   }
  }
 }
 else if (window.XMLHttpRequest) // if Mozilla, Safari etc
  return new XMLHttpRequest()
 else
  return false
}

// Variables for positioning scrollbar
var scrol0 = -1;
var i_scrol = 0;

var mypostrequest = new ajaxRequest();    // Create the object for AJAX
function ajaxF(file, parameters) {
  var ajxsend = 1;        // parameter to check sending (Ajax busy)

  parameters += '&chatroom='+chatroom;      // Add the "chatroom"
  // Sends data
  mypostrequest.open("POST", file, true);
  mypostrequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  mypostrequest.send(parameters);

  mypostrequest.onreadystatechange=stateChanged;

  // Function to display response
  function stateChanged() {
    var niv_scroll = document.getElementById('chats').scrollTop;    // determine where the scrollbar is positioned

  if (mypostrequest.readyState==4) {
    if (mypostrequest.status==200 && document.getElementById('chats') && mypostrequest.responseText.indexOf('id="chats"')!=-1) {
    // set chat content to be added in page. if "logoutchat" is 1, del the user from Online list
    var chatcnt = mypostrequest.responseText;
    if(logoutchat === 1) {
      var rgx_userli = new RegExp('\<li\>'+document.getElementById('chatuser').value+'\<span\>([^\<]*)\<\/span\>\<\/li\>', 'i');
      if(chatcnt.match(rgx_userli)) chatcnt = chatcnt.replace(rgx_userli, '');
    }

      document.getElementById('chatwindow').innerHTML = chatcnt;       // add chat content in page

      // Make auto-scroll to scrollbar position if it was moved, or bottom of DIV #chats
      var div = document.getElementById('chats');
      var scrollHeight = Math.max(div.scrollHeight, div.clientHeight);
      if (niv_scroll!=0 && niv_scroll<scrol0) {
        div.scrollTop = niv_scroll;
      }
      else {
        div.scrollTop = div.scrollHeight;
        i_scrol = 0;
      }
      // Sets scrollbar position
      if (i_scrol==0) {
        scrol0 = document.getElementById('chats').scrollTop;
        i_scrol = 1;
      }

      if(playbeep == 2) playBeep(chatcnt);       // calls to check for play beep if chat added
    }
  }
  ajxsend = 0;      // Set Ajax sent and free
  }
}

// Calls Ajax function to each 2 seconds (with chatuser) to 
function apelAjax() {
  callphp -= 1.5;              // decrement callphp 1.5 seconds
  // sets file to access according to "sec", if it`s 0, accesses "setchat", else, the TXT file with chatroom name
  if(callphp <= 0) {
    callphp = 2.8 + (getNrChatUsers() * 0.3);            // sets to call "setchat" according to number of online users
    var chatfile = setchat;
  }
  else var chatfile = getchat();
  if(callphp > 10) callphp = 10

  var chatusr = (chatuserset==1) ? (document.getElementById('chatuser').value) : '';

  // if Ajax free, sends data, else, set free for next auto-call
  if(ajxsend === 0) ajaxF(chatfile, 'chatuser='+chatusr);
  else ajxsend = 0;

  setTimeout('apelAjax()', 1900);
}
apelAjax();    // Calls Ajax function