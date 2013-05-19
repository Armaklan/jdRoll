<?php
// PHP Script Chat - coursesweb.net

define('MAXROWS', 90);             // Maximum number of rows registered for chat
define('CHATLINK', 1);             // allows links in texts (1), not allow (0)

// Here create the rooms for chat
// For more rooms, add lines with this syntax  $chatrooms[] = 'room_name';
$chatrooms = array();
$chatrooms[] = 'English';


// password used to empty chat rooms after this page is accessed with ?mod=admin
define('CADMPASS', 'adminpass');

/* For example, access in your browser
    http://domain/chatfiles/setchat.php?mod=admin
*/


// If you want than only the logged users to can add texts in chat, sets CHATADD to 0
// And sets $_SESSION['username'] with the session that your script uses to keep logged users
define('CHATADD', 1);
if(CHATADD !== 1) {
  if(isset($_SESSION['username'])) define('CHATUSER', $_SESSION['username']);
}

// Name of the directory in which are stored the TXT files for chat rooms
define('CHATDIR', 'chattxt');

include('texts.php');             // file with the texts for different languages
$lsite = $en_site;                // Gets the language for site

if(!headers_sent()) header('Content-type: text/html; charset=utf-8');         // header for utf-8

// include the class ChatSimple, and create objet from it
include('class.ChatSimple.php');
$chatS = new ChatSimple($chatrooms);

// if this page is accessed with mod=admin in URL, calls emptyChatRooms() method
if(isset($_GET['mod']) && $_GET['mod'] == 'admin') $chatS->emptyChatRooms();