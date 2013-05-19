<?php
// Texts added in site (English)
$en_site = array(
  'chat'=>'Chat',
  'name'=>'Name',
  'code'=>'Code',
  'addnmcd'=>'Add a Name, and this code',
  'chatlogged'=>'<h4 id="chlogged">To can add texts in chat, you must be logged in.</h4>',
  'online'=>'Online',
  'no1online'=>'- No-one online',
  'loadroom'=>'<h3>Loadding Chat-Room</h3>',
  'notchat'=>'Not chat added in this room.',
  'addurl'=>'Add URL without http://',
  'logoutchat'=>'Logout from Chat',
  'enterchat'=>'Hi <b>%s</b> Enter the Chat',
  'emptyroom'=>'Select a Chat-Room to empty',
  'cadmpass'=>'Admin Password:',
  'sbmemptyroom'=>'Empty Chat-Room',
  'emptedroom'=>'The chat room: <b>%s</b> is empty',
  'err_emptedroom'=>'Cannot empty the chat room: ',
  'err_savechat'=>'Unable to save data in: %s , or the file cannot be created',
  'err_name'=>'The name must contain between 2 and 12 characters',
  'err_nameused'=>' - is already used. \n Choose another nickname',
  'err_vcode'=>'Add correct verification code',
  'err_textchat'=>'The text must contain between 2 and 200 characters',
  'err_addurl'=>'Incorrect URL format \n Add URL without http:// \n Example: coursesweb.net/ajax/'
);


// Sets an json object for JavaScript with text messages according to language set
function jsTexts($lsite) {
  // define the JavaScript json object
$texts = 'var texts = {
 "err_name":"'.$lsite['err_name'].'",
 "err_nameused":"'.$lsite['err_nameused'].'",
 "err_vcode":"'.$lsite['err_vcode'].'",
 "err_textchat":"'.$lsite['err_textchat'].'",
 "err_addurl":"'.$lsite['err_addurl'].'",
 "loadroom":"'.$lsite['loadroom'].'",
 "addurl":"'.$lsite['addurl'].'"
};';

  return '<script type="text/javascript"><!--'.PHP_EOL.
  $texts.PHP_EOL.
  '//-->
  </script>';
}