<div style="margin:20px auto; text-align:center;">
<?php
// makes radio buttons with chat rooms
$rchatroom = $this->lsite['emptyroom'];
$nrchatroom = count($this->chatrooms);
if($nrchatroom > 0) {
  for($i=0; $i<$nrchatroom; $i++) {
    $rchatroom .= '<br/><label for="'. $this->chatrooms[$i]. '"><input type="radio" name="emptyroom" value="'. $this->chatrooms[$i]. '" id="'. $this->chatrooms[$i]. '" />'. $this->chatrooms[$i]. '</label>';
  }
}
?>
<form action="<?php echo basename($_SERVER['REQUEST_URI']); ?>" method="post">
<?php
echo $rchatroom. '<br/><br/>'. $this->lsite['cadmpass'];
?>
 <input type="password" name="cadmpass" /><br/><br/>
 <input type="submit" value="<?php echo $this->lsite['sbmemptyroom']; ?>"/>
</form>
</div>