<?php
// Chat Simple, from: http://coursesweb.net/php-mysq/

class ChatSimple {
  public $maxrows = 30;
  public $chatrooms = array();            // for chat rooms
  public $chatroomcnt = '';               // store chat room content

  protected $lsite = array();              // will contains the texts in the defined language
  protected $chatdir = 'chattxt';         // directory that store TXT files for chat
  protected $fileroom;                    // store the file of current chat room
  protected $chatuser = '';               // store user name
  protected $chatadd = 1;                 // if not 1, the user must be logged in

  // constructor (receives the array with chat rooms)
  public function __construct($chatrooms) {
    // set properties value
    $this->lsite = $GLOBALS['lsite'];
    $this->chatrooms = $chatrooms;
    if(defined('CHATADD')) $this->chatadd = CHATADD;
    if(defined('CHATDIR')) $this->chatdir = (basename(dirname($_SERVER['PHP_SELF'])) == 'chatfiles') ? '../'.CHATDIR : CHATDIR;
    if(defined('MAXROWS')) $this->maxrows = MAXROWS;

    $this->fileusers = $this->chatdir.'/chatusers.txt';
    $this->fileroom = isset($_POST['chatroom']) ? ($this->chatdir.'/'.trim(strip_tags($_POST['chatroom'].'.txt'))) : ($this->chatdir.'/'.$this->chatrooms[0].'.txt');

    // sets current chat user with the name added in form, and calls the method to set $chatroomcnt
    if(isset($_POST['chatuser'])) $this->chatuser = trim(htmlentities($_POST['chatuser'], ENT_NOQUOTES, 'utf-8'));
    $this->chatroomcnt = $this->setChatRoomCnt();

    // if data from the form to add chat, output chat room content
    if(isset($_POST['chatuser'])) echo $this->chatroomcnt;
  }

  // returns the HTML code with chat rooms
  public function chatRooms() {
    $nrooms = count($this->chatrooms);
    $chatrooms = '';
    if($nrooms > 0) {
      for($i=0; $i<$nrooms; $i++) {
        $id = ($i==0) ? 'id="s_room"' : '';
        $chatrooms .= '<span class="chatroom" '.$id.' onclick="setChatRoom(this)">'.$this->chatrooms[$i].'</span>';
      }
    }
    else $chatrooms = '<span><b> &nbsp; &nbsp; - Chat</span>';

    return $chatrooms;
  }


  // include the form to add text in chat room, or messaje to Logg in (if $chatuser false, and $chatadd not 1)
  public function chatForm() {
    if($this->chatadd !== 1) {
      if(defined('CHATUSER')) include('chat_form.php');
      else echo $this->lsite['chatlogged'];
    }
    else include('chat_form.php');
  }

  // returns HTML list with online users in chat
  protected function getChatUsers($chatroomcnt) {
    $regtime = time();

    // gets in $found 2 arrays, with "users", and "regtime" in their associate order
    preg_match_all('#\<li\>(.*?)\<span\>(.*?)\</span\>\</li\>#is', $chatroomcnt, $found);

    // if $found[1] has elements, traverse the array, and create lists (in $chatusers) with users in last 7 sec.
    $nrlists = count($found[1]);
    if($nrlists > 0) {
      for($i=0; $i<$nrlists; $i++) {
        if(intval($found[2][$i]) > ($regtime -7) && $found[1][$i]) $chatusers[$found[1][$i]] = '<li>'.$found[1][$i].'<span>'.$found[2][$i].'</span></li>';
      }
    }

    // adds current user in list
    if($this->chatuser !== '') $chatusers[$this->chatuser] = '<li>'.$this->chatuser.'<span>'.$regtime.'</span></li>';

    // if the array $chatusers is set, sorts the list alphabetically, and return it, else, return without UL
    if(isset($chatusers)) {
      ksort($chatusers);
      return '<div id="chatusers"><h4 id="onl">'.$this->lsite['online'].'</h4><ul id="chatusersli">'.implode('', $chatusers).'</ul></div>';
    }
    else return '<div id="chatusers"><h4 id="onl">'.$this->lsite['online'].'</h4>'.$this->lsite['no1online'].'</div>';
  }

  // adds HTML code with chat text in TXT file
  protected function setChatRoomCnt() {
    $chatroomcnt = ' ';          // stores chat room content

    // if file for current chat room exists, gets its content, else, display 'no chat', and current user
    if(file_exists($this->fileroom)) {
      $chatroomcnt = file_get_contents($this->fileroom);
      $chatusers = $this->getChatUsers($chatroomcnt);         // get the list with online users

      // if access to add new chat text
      if(isset($_POST['adchat'])) {
        $adchat = trim(htmlentities($_POST['adchat'], ENT_NOQUOTES, 'utf-8'));     // Transform HTML characters, and delete external whitespace
        if(get_magic_quotes_gpc()) $adchat = stripslashes($adchat);     // Removes slashes added by get_magic_quotes_gpc

        // gets chat text rows
        preg_match_all('#(\<p\>(.*?)\</p\>)#is', $chatroomcnt, $found);
        $chatrows = $found[1];

        // if text added, sets the new row at the end, and keep the last $maxrows rows
        if(strlen($adchat)<1 || strlen($adchat)<201) {
          $chatrows[] = '<p><span class="chatusr">&bull; '.$this->chatuser.' - </span><em>'.date('j F H:i').'</em><span class="chat">- '. $this->formatBbcode($adchat). '</span></p>';
          $chatrows = array_slice($chatrows, -($this->maxrows));
        }

        // sets chat room content
        $chatroomcnt = '<div id="chats"><q>'.time().'</q>'. implode('', $chatrows). '</div>' .$chatusers;
      }
      else {
        // replace users list with new one ("/is" pattern case-insensitive, include newlines)
        $chatroomcnt = preg_replace('#\<div id="chatusers"\>\<h4 id="onl"\>Online\</h4\>(.*?)\</div\>#is', $chatusers, $chatroomcnt);
      }
    }
    else $chatroomcnt = '<div id="chats">'.$this->lsite['notchat'].'</div>'. $this->getChatUsers('');

    if(strlen($chatroomcnt) > 10) {
      // write the chat content in TXT file, returns $chatroomcnt, or message error
      if(file_put_contents($this->fileroom, $chatroomcnt)) return $chatroomcnt;
      else return sprintf($this->lsite['err_savechat'], $this->fileroom);
    }
  }

  // to empty chatrooms, include the form with chatrooms to empty
  // if request to empty room, and "cadmpass" is correct, write 'notchat' in that room
  public function emptyChatRooms() {
    if(isset($_POST['emptyroom']) && $_POST['cadmpass'] == CADMPASS) {
      $fileroom = $this->chatdir.'/'.trim(strip_tags($_POST['emptyroom'].'.txt'));
      $chatroomcnt = '<div id="chats">'.$this->lsite['notchat'].'</div>'. $this->getChatUsers('');
      if(file_put_contents($fileroom, $chatroomcnt)) echo '<center>'. sprintf($this->lsite['emptedroom'], $_POST['emptyroom']). '</center>';
      else echo '<center>'. $this->lsite['err_emptedroom']. $_POST['emptyroom']. '</center>';
    }
    include('emptychat_form.php');
  }

  // Function to convert BBCODE in HTML tags
  protected function formatBbcode($str) {
    $str = nl2br(strip_tags($str));                 // delete tags, add <br> for new line
    $str = str_replace(PHP_EOL, '', $str);          // delete new line characters, and "http"
    $str = str_replace('[url=http://', '[url=', $str);
    $str = str_replace('[url]http://', '[url]', $str);

    // characters that represents bbcode, and smiles
    $bbcode = array(
    '/\[b\](.*?)\[\/b\]/is', '/\[i\](.*?)\[\/i\]/is', '/\[u\](.*?)\[\/u\]/is',      // for format text
    '/\[url\=(.*?)\](.*?)\[\/url\]/is', '/\[url\](.*?)\[\/url\]/is',             // for URL
    '/:\)/i', '/:\(/i', '/:P/i', '/:D/i', '/:S/i', '/:O/i', '/:=\)/i', '/:\|H/i', '/:X/i', '/:\-\*/i');

    // HTML code that replace bbcode, and smiles characters
    $htmlcode = array(
    '<b>$1</b>', '<i>$1</i>', '<u>$1</u>',             // format text
    '<a target="_blank" rel="nofallow" href="http://$1" title="link">$2</a>',
    '<a target="_blank" rel="nofallow" href="http://$1" title="link">$1</a>',
    '<img src="chatex/0.gif" alt=":)" border="0" />',
    '<img src="chatex/1.gif" alt=":(" border="0" />',
    '<img src="chatex/2.gif" alt=":P" border="0" />',
    '<img src="chatex/3.gif" alt=":D" border="0" />',
    '<img src="chatex/4.gif" alt=":S" border="0" />',
    '<img src="chatex/5.gif" alt=":O" border="0" />',
    '<img src="chatex/6.gif" alt=":=)" border="0" />',
    '<img src="chatex/7.gif" alt=":|H" border="0" />',
    '<img src="chatex/8.gif" alt=":X" border="0" />',
    '<img src="chatex/9.gif" alt=":-*" border="0" />'
    );

    $str = preg_replace($bbcode, $htmlcode, $str);   // perform replaceament

    return $str;
  }
}