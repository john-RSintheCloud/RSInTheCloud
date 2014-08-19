<?php
# Feeder page for AJAX user/group search for the user selection include file

include "../../application/bootstrap.php";
include "../../include/authenticate.php";
include "../../include/general.php";

$userstring=getvalescaped("userstring","");
$userstring=resolveUserlistGroups($userstring);
$userstring=array_unique(trim_array(explode(",",$userstring)));
sort($userstring);
$userstring=implode(", ",$userstring);
echo $userstring;
