<?php

//echo 'hello world';

require_once "../application/bootstrap.php";

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

<title>Plupload to Amazon S3 Example</title>

<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/base/jquery-ui.css" type="text/css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<?php echo $container['Bucket']->getPlUploadJs();?>
</head>
<body style="font: 13px Verdana; background: #eee; color: #333">

<h1>Plupload to Amazon S3 Example</h1>

<div id="uploader">
    <p>Your browser doesn't have HTML5 or Flash support.</p>
</div>

<script type="text/javascript">
$(document).ready(function() {
    <?php echo $container['Bucket']->getPlUpload('uploader');?>
});
</script>

</body>
</html>

<?php
//var_dump($container['db']->sqlQuery('show tables'));
//
//var_dump ($container['db']->fetchAll());
//
//var_dump($container['db']->sqlQuery('select * from random'));
//
//var_dump ($container['db']->fetchAll());
//
//var_dump ($container['db']->fetchSome());
//
//var_dump ($container['db']->fetchSome(4,1));
//
//var_dump ($container['db']->fetchSome(4,5));
//
//var_dump ($container['db']->fetchAll('select * from random where ref = 2'));
//
//var_dump ($container['db']->getStats());
//


/**
 * @var resource_model_assetAbstract
 */
//$asset = $container['model_asset'];
//
//echo " <br><br>deleting asset 'test' <br>";
//
//var_dump($asset->deleteAssetBySlug('test'));
//
//echo "asset->save(array(
//    'slug' => 'testa-11',
//    'date_modified' => 'now'
//)))";
//var_dump($asset->setOptions(array(
//    'slug' => 'testa-11',
//    'date_modified' => 'now'
//))->save());
//
//
//echo "asset 1 ref = $asset->ref<br>";
//echo "asset 1 slug = $asset->slug<br>";
//
//
//
///**
// * @var resource_model_assetAbstract
// */
//$ass2 = $container['model_asset'];
//
//
//echo " <br><br>asset 2 ref = $ass2->ref<br>";
//
//var_dump($ass2);
//
//$ass2->setOptions(array(
//    'slug' => 'testa-11',));
//
//var_dump ($ass2->hydrate());
//
//echo 'done';
//
