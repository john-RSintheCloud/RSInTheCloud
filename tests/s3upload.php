<?php

require_once "../application/bootstrap.php";

header('Cache-Control: no-transform');

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

<title>Plupload to S3</title>

<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/base/jquery-ui.css" type="text/css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<?php echo $container['Bucket']->getPlUploadJs();?>
</head>
<body style="font: 13px Verdana; background: #eee; color: #333">

<h1>Plupload to S3</h1>

<div id="uploader">
    <p>If you can see this, it means that PLupload has not loaded correctly.</p>
    <p>This may be because your browser doesn't have HTML5 support (in which case - upgrade!)
        or there may be a problem with jQuery or Javascript.</p>
</div>

<script type="text/javascript">
$(document).ready(function() {
    <?php echo $container['Bucket']->getPlUpload('uploader');?>
});
</script>

</body>
</html>

