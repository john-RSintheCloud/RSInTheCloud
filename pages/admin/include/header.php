<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo $baseurl?>/pages/admin/muse.css" />
<script type="text/javascript" src="../../lib/js/jquery-1.7.2.min.js"></script>
<link href="<?php echo $baseurl?>/css/Col-<?php echo (isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss",$defaulttheme)?>.css" rel="stylesheet" type="text/css" media="screen,projection,print" id="colourcss" />
<?php
# Include CSS files for for each of the plugins too (if provided)
for ($n=0;$n<count($plugins);$n++)
    {
    $csspath=dirname(__FILE__)."/../../../plugins/" . $plugins[$n] . "/css/style.css";
    if (file_exists($csspath))
        {
        ?>
        <link href="<?php echo $baseurl?>/plugins/<?php echo $plugins[$n]?>/css/style.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css" media="screen,projection,print"  />
        <?php
        }
    $theme=((isset($userfixedtheme) && $userfixedtheme!=""))?$userfixedtheme:getval("colourcss",$defaulttheme);
    $csspath=dirname(__FILE__)."/../../../plugins/" . $plugins[$n] . "/css/Col-".$theme.".css";
    if (file_exists($csspath))
        {
        ?>
        <link href="<?php echo $baseurl?>/plugins/<?php echo $plugins[$n]?>/css/Col-<?php echo $theme?>.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css" media="screen,projection,print" id="<?php echo $plugins[$n]?>css" />
        <?php
        }
    }
?>
<title>Administration</title>

</head>

