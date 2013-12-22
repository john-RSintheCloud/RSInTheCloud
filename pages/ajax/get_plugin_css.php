<?php
include_once(dirname(__FILE__)."/../../application/bootstrap.php");
include_once(dirname(__FILE__)."/../../include/general.php");
include_once(dirname(__FILE__)."/../../include/authenticate.php");

// in this folder so paths are relatively correct

$theme=getvalescaped('theme','greyblu');

echo get_plugin_css($theme);
