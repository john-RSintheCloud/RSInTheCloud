<?php
require_once "../application/bootstrap.php";

if (!hook("authenticate")){require_once "../include/authenticate.php";}

require_once "../include/header.php";
?>

<div class="BasicsBox">
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["aboutus"]?></h1>
  <p><?php echo text("about")?></p>
</div>

<?php
require_once "../include/footer.php";
?>