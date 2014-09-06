<?php
require_once "../application/bootstrap.php";

require_once "authenticate.php";

require_once 'view/wrapper.php';

if (getval("save","")!="")
	{
    rs_setcookie("language", getval("language", ""), 1000); # Only used if not global cookies
    rs_setcookie("language", getval("language", ""), 1000, $baseurl_short);
    rs_setcookie("language", getval("language", ""), 1000, $baseurl_short . "pages");
	redirect(getval("uri",$baseurl_short."pages/" . ($use_theme_as_home?'themes.php':$default_home_page)));
	}
require_once "header.php";
?>

<h1><?php echo $lang["languageselection"]?></h1>
<p><?php echo text("introtext")?></p>

<form method="post" action="">
<div class="Question">
<label for="language_set"><?php echo $lang["language"]?></label>
<select class="stdwidth" name="language_set">
<?php foreach ($languages as $key=>$value) :
    echo '
   <option value="' . $key . '" ';
    if ($language==$key) echo 'selected="selected" ';
    echo ' >' . htmlspecialchars($value) . '</option>';

    endforeach; ?>

</select>
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>


<?php
require_once "footer.php";
?>
