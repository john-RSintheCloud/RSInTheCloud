<?php
/**
 * Initial setup page.
 * 
 * Large parts of this are not applicable to OORS / RSitC
 *
 * @package RS Legacy
 * @subpackage Pages_Misc
 */
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application') . '/');

require_once APPLICATION_PATH . 'modules/setup/setup.php';

?>
<html>
<head>
<title><?php echo $lang["setup-rs_initial_configuration"];?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="../css/global.css" rel="stylesheet" type="text/css" media="screen,projection,print" />
<link href="../css/Col-greyblu.css" rel="stylesheet" type="text/css" media="screen,projection,print" id="colourcss" />
<script type="text/javascript" src="../lib/js/jquery-1.7.2.min.js"></script>

<script type="text/javascript">

$(document).ready(function(){
$('p.iteminfo').hide();
$('.starthidden').hide();
$('#tabs div.tabs').hide();
$('#tabs div:first').show();
$('#tabs ul li:first').addClass('active');
$('#tabs ul li a').click(function(){
	$('#tabs ul li').removeClass('active');
	$(this).parent().addClass('active');
	var currentTab = $(this).attr('href');
	$("#tabs div.tabs:visible").slideUp("slow",function(){
		$(currentTab).slideDown("slow");
	});
	return false;
});
$('#configstoragelocations').each(function(){
	if (this.checked != true){
		$('#storageurl').attr("disabled",true);
		$('#storagedir').attr("disabled",true);
	}
	else {
		$('#remstorageoptions').show();
	}
});
$('#configstoragelocations').click(function(){
	if (this.checked == true) {
		$('#storageurl').removeAttr("disabled");
		$('#storagedir').removeAttr('disabled');
		$('#remstorageoptions').slideDown("slow");
	}
	else{
		$('#storageurl').attr("disabled",true);
		$('#storagedir').attr("disabled",true);
		$('#remstorageoptions').slideUp("slow");
	}
});
$('p.iteminfo').click(function(){
	$('p.iteminfo').hide("slow");
	});
$('.mysqlconn').keyup(function(){
	$('#al-testconn').fadeIn("fast",function(){
		$.ajax({
			url: "dbtest.php",
			async: true,
			dataType: "text",
			data: { mysqlserver: $('#mysqlserver').val(), mysqlusername: $('#mysqlusername').val(), mysqlpassword: $('#mysqlpassword').val(),mysqldb: $('#mysqldb').val() },
			success: function(data,type){
				if (data==200) {
					$('#mysqlserver').removeClass('warn');
					$('#mysqlusername').removeClass('warn');
					$('#mysqlpassword').removeClass('warn');
					$('#mysqldb').addClass('ok');
					$('#mysqlserver').addClass('ok');
					$('#mysqlusername').addClass('ok');
					$('#mysqlpassword').addClass('ok');
					$('#mysqldb').removeClass('warn');
				}
				else if (data==201) {
					$('#mysqlserver').removeClass('warn');
					$('#mysqlusername').removeClass('ok');
					$('#mysqlpassword').removeClass('ok');
					$('#mysqldb').removeClass('ok');
					$('#mysqldb').removeClass('warn');
					$('#mysqlserver').addClass('ok');
					$('#mysqlusername').addClass('warn');
					$('#mysqlpassword').addClass('warn');
				}
				else if (data==203) {
					$('#mysqlserver').removeClass('warn');
					$('#mysqlusername').removeClass('warn');
					$('#mysqlpassword').removeClass('warn');
					$('#mysqldb').removeClass('ok');
					$('#mysqldb').addClass('warn');
					$('#mysqlserver').addClass('ok');
					$('#mysqlusername').addClass('ok');
					$('#mysqlpassword').addClass('ok');
				}
				else{
					$('#mysqlserver').removeClass('ok');
					$('#mysqlusername').removeClass('ok');
					$('#mysqlpassword').removeClass('ok');
					$('#mysqldb').removeClass('ok');
					$('#mysqldb').removeClass('warn');
					$('#mysqlserver').addClass('warn');
					$('#mysqlusername').removeClass('warn');
					$('#mysqlpassword').removeClass('warn');
				}
				$('#al-testconn').hide();
			},
			error: function(){
				alert('<?php echo $lang["setup-mysqltestfailed"] ?>');
				$('#mysqlserver').addClass('warn');
				$('#mysqlusername').addClass('warn');
				$('#mysqlpassword').addClass('warn');
				$('#al-testconn').hide();
		}});
	});
});
$('a.iflink	').click(function(){
	$('p.iteminfo').hide("slow");
	var currentItemInfo = $(this).attr('href');
	$(currentItemInfo).show("fast");
	return false;
});
$('#mysqlserver').keyup();

});
</script>

<style type="text/css">

<!--
#wrapper{ margin:0 auto;width:600px; }
 #intro {  margin-bottom: 40px; font-size:100%; background: #333333; text-align: left; padding: 40px; }
#intro a{ color: #fff; }
#introbottom { padding: 10px; clear: both; text-align:center;}
#preconfig {  float:right; background: #555555; padding: 25px;}
#preconfig h2 { border-bottom: 1px solid #ccc;	width: 100%;}
#preconfig p { font-size:110%; padding:0; margin:0; margin-top: 5px;}
#preconfig p.failure{ color: #f00; font-weight: bold; }

#tabs { font-size: 100%;}
#tabs > ul { float: right; width: 600px; margin:0; padding:0; border-bottom:5px solid #333333; }
#tabs > ul >li { margin: 0; padding:0; margin-left: 8px; list-style: none; background: #777777; }
* html #tabs li { display: inline; /* ie6 double float margin bug */ }
#tabs > ul > li, #tabs  > ul > li a { float: left; }
#tabs > ul > li a { text-decoration: none; padding: 8px; color: #CCCCCC; font-weight: bold; }
#tabs > ul > li.active { background: #CEE1EF; }
#tabs > ul > li.active a { color: #333333; }
#tabs div.tabs { background: #333; clear: both; padding: 20px; text-align: left; }
#tabs div h1 { text-transform: uppercase; margin-bottom: 10px; letter-spacing: 1px; }

p.iteminfo{ background: #e3fefa; width: 60%; color: #000; padding: 4px; margin: 10px; clear:both; }
strong { padding:0 5px; color: #F00; font-weight: bold; }
a.iflink { color: #F00; padding: 2px; border: 1px solid #444; margin-left: 4px; }

input { margin-left: 10px; border: 1px solid #000; }

input.warn { border: 2px solid #f00; }
input.ok{ border:2px solid #0f0; }
input#submit { margin: 30px; font-size:120%; }

div.configitem { padding-top:10px; padding-left:40px; padding-bottom: 5px; border-bottom: 1px solid #555555; }

label { padding-right: 10px; width: 30%; font-weight: bold; }
div.advsection{ margin-bottom: 20px; }
.ajloadicon { padding-left:4px; }
h2#dbaseconfig{  min-height: 32px;}

.erroritem{ background: #fcc; border: 2px solid #f00; color: #000; padding: 10px; margin: 7px; font-weight:bold;}
.erroritem.p { margin: 0; padding:0px;padding-bottom: 5px;}
.warnitem{ background: #FFFFB3; border: 2px solid #FFFF33; color: #000; padding: 10px; margin: 7px; font-weight:bold;}
.warnitem.p { margin: 0; padding:0px;padding-bottom: 5px;}
#errorheader { font-size: 110%; margin-bottom: 20px; background: #fcc; border: 1px solid #f00; color: #000; padding: 10px; font-weight: bold; }
#configoutput { background: #777; color: #fff; text-align: left; padding: 20px; }
#warnheader { font-size: 110%; margin-bottom: 20px; background: #FFFFB3; border: 1px solid #FFFF33; color: #000; padding: 10px; font-weight: bold; }
.language {clear:both; text-align:center; padding:20px;}

-->

</style>
</head>
<body>
<div id="Header">
	<div id="HeaderNav1" class="HorizontalNav ">&nbsp;</div>
	<div id="HeaderNav2" class="HorizontalNav HorizontalWhiteNav">&nbsp;</div>
</div>
<div id="wrapper">
<?php
	//warn if config file already exists
    echo $errMsg;

    if($success):
	?>
	<div id="intro">
		<h1><?php echo $lang["setup-successheader"]; ?></h1>
		<p><?php echo $lang["setup-successdetails"]; ?></p>
		<p><?php echo $lang["setup-successnextsteps"]; ?></p>
		<ul>
			<li><?php echo $lang["setup-successremovewrite"]; ?></li>
			<li><?php echo $lang["setup-visitwiki"]; ?></li>
			<li><a href="<?php echo $baseurl;?>/login.php"><?php echo $lang["setup-login_to"] . " " . $applicationname; ?></a>
				<ul>
					<li><?php echo $lang["username"] . ": admin"; ?></li>
					<li><?php echo $lang["password"] . ": admin"; ?></li>
				</ul>
			</li>
		</ul>
	</div>
	<?php

else:
?>
<form action="setup.php" method="POST">
<?php echo $config_windows==true?'<input type="hidden" name="config_windows" value="true"/>':'' ?>
	<div id="intro">
			<div id="preconfig">
				<h2><?php echo $lang["installationcheck"]; ?></h2>
				<?php
                    echo $passResult;
				?>
			</div>
			<h1><?php echo $lang["setup-welcome"];?></h1>
			<p><?php echo $lang["setup-introtext"];?><p>
			<p><?php echo $lang["setup-visitwiki"];?></p>
			<div class="language">
					<label for="defaultlanguage"><?php echo $lang["language"];?>:</label><select id="defaultlanguage" name="defaultlanguage">
						<?php
							foreach($languages as $code => $text){
								echo "<option value=\"$code\"";
								if ($code == $defaultlanguage)
									echo ' selected';
								echo ">$text</option>";
							}
						?>
					</select>
					<input type="submit" id="changelanguage" name="changelanguage" value="<?php echo $lang["action-changelanguage"]; ?>"/>
				</div>
			<div id="introbottom">
			<?php if ($continue===false) { ?>
			<strong><?php echo $lang["setup-checkerrors"];?></strong>
			<?php } else { ?>
			<script type="text/javascript">
			$(document).ready(function(){
				$('#tabs').show();
			});
			</script>
			<?php } ?>
			</div>
	</div>
	<?php if (isset($errors)){ ?>
		<div id="errorheader"><?php echo $lang["setup-errorheader"];?></div>
	<?php } ?>
	<?php if (isset($warnings)){ ?>
		<div id="warnheader"><?php echo $lang["setup-warnheader"];?></div>
	<?php } ?>
	<div id="tabs" class="starthidden">
		<ul>
			<li><a href="#tab-1"><?php echo $lang["setup-basicsettings"];?></a></li>
			<li><a href="#tab-2"><?php echo $lang["setup-advancedsettings"];?></a></li>
		</ul>
		<div class="tabs" id="tab-1">
			<h1><?php echo $lang["setup-basicsettings"];?></h1>
			<p><?php echo $lang["setup-basicsettingsdetails"];?></p>
			<p class="configsection">
				<h2 id="dbaseconfig"><?php echo $lang["setup-dbaseconfig"];?><img class="starthidden ajloadicon" id="al-testconn" src="../gfx/ajax-loader.gif"/></h2>
				<?php if(isset($errors['database'])){?>
					<div class="erroritem"><?php echo $lang["setup-mysqlerror"];?>
						<?php if(isset($errors['databaseversion']))
							echo $lang["setup-mysqlerrorversion"];
						if(isset($errors['databaseserver']))
							echo $lang["setup-mysqlerrorserver"];
						if(isset($errors['databaselogin']))
							echo $lang["setup-mysqlerrorlogin"];
						if(isset($errors['databasedb']))
							echo $lang["setup-mysqlerrordbase"];
						if(isset($errors['databaseperms']))
							echo $lang["setup-mysqlerrorperms"]; ?>

						<p><?php echo $errors['database'];?></p>
					</div>
				<?php } ?>

				<div class="configitem">
					<label for="mysqlserver"><?php echo $lang["setup-mysqlserver"];?></label><input class="mysqlconn" type="text" id="mysqlserver" name="mysql_server" value="<?php echo $mysql_server;?>"/><strong>*</strong><a class="iflink" href="#if-mysql-server">?</a>
					<p class="iteminfo" id="if-mysql-server"><?php echo $lang["setup-if_mysqlserver"];?></p>
				</div>
				<div class="configitem">
					<label for="mysqlusername"><?php echo $lang["setup-mysqlusername"];?></label><input class="mysqlconn" type="text" id="mysqlusername" name="mysql_username" value="<?php echo $mysql_username;?>"/><strong>*</strong><a class="iflink" href="#if-mysql-username">?</a>
					<p class="iteminfo" id="if-mysql-username"><?php echo $lang["setup-if_mysqlusername"];?></p>
				</div>
				<div class="configitem">
					<label for="mysqlpassword"><?php echo $lang["setup-mysqlpassword"];?></label><input class="mysqlconn" type="password" id="mysqlpassword" name="mysql_password" value="<?php echo $mysql_password;?>"/><strong>*</strong><a class="iflink" href="#if-mysql-password">?</a>
					<p class="iteminfo" id="if-mysql-password"><?php echo $lang["setup-if_mysqlpassword"];?></p>
				</div>
				<div class="configitem">
					<label for="mysqldb"><?php echo $lang["setup-mysqldb"];?></label><input id="mysqldb" class="mysqlconn" type="text" name="mysql_db" value="<?php echo $mysql_db;?>"/><strong>*</strong><a class="iflink" href="#if-mysql-db">?</a>
					<p class="iteminfo" id="if-mysql-db"><?php echo $lang["setup-if_mysqldb"];?></p>
				</div>

				<div class="configitem">
					<?php if(isset($errors['mysqlbinpath'])){?>
						<div class="erroritem"><?php echo $lang["setup-err_mysqlbinpath"];?></div>
					<?php } ?>
					<label for="mysqlbinpath"><?php echo $lang["setup-mysqlbinpath"];?></label><input id="mysqlbinpath" type="text" name="mysql_bin_path" value="<?php echo $mysql_bin_path;?>"/><a class="iflink" href="#if-mysql-bin-path">?</a>
					<p class="iteminfo" id="if-mysql-bin-path"><?php echo $lang["setup-if_mysqlbinpath"];?></p>
				</div>
			</p>
			<p class="configsection">
				<h2><?php echo $lang["setup-generalsettings"];?></h2>
				<div class="configitem">
					<label for="applicationname"><?php echo $lang["setup-applicationname"];?></label><input id="applicationname" type="text" name="applicationname" value="<?php echo $applicationname;?>"/><a class="iflink" href="#if-applicationname">?</a>
					<p class="iteminfo" id="if-applicationname"><?php echo $lang["setup-if_applicationname"];?></p>
				</div>
				<div class="configitem">
					<?php if(isset($errors['baseurl'])){?>
						<div class="erroritem"><?php echo $lang["setup-err_baseurl"];?></div>
					<?php } ?>
					<?php if(isset($warnings['baseurlverify'])){?>
						<div class="warnitem"><?php echo $lang["setup-err_baseurlverify"];?></div>
					<?php } ?>
					<label for="baseurl"><?php echo $lang["setup-baseurl"];?></label><input id="baseurl" type="text" name="baseurl" value="<?php echo $baseurl;?>"/><strong>*</strong><a class="iflink" href="#if-baseurl">?</a>
					<p class="iteminfo" id="if-baseurl"><?php echo $lang["setup-if_baseurl"];?></p>
				</div>
				<div class="configitem">
					<?php if(isset($errors['email_from'])){?>
						<div class="erroritem"><?php echo $lang["setup-emailerr"];?></div>
					<?php } ?>
					<label for="emailfrom"><?php echo $lang["setup-emailfrom"];?></label><input id="emailfrom" type="text" name="email_from" value="<?php echo $email_from;?>"/><a class="iflink" href="#if-emailfrom">?</a>
					<p id="if-emailfrom" class="iteminfo"><?php echo $lang["setup-if_emailfrom"];?></p>
				</div>
				<div class="configitem">
					<?php if(isset($errors['email_notify'])){?>
						<div class="erroritem"><?php echo $lang["setup-emailerr"];?></div>
					<?php } ?>
					<label for="emailnotify"><?php echo $lang["setup-emailnotify"];?></label><input id="emailnotify" type="text" name="email_notify" value="<?php echo $email_notify;?>"/><a class="iflink" href="#if-emailnotify">?</a>
					<p id="if-emailnotify" class="iteminfo"><?php echo $lang["setup-if_emailnotify"];?></p>
				</div>
				<div class="configitem">
				<?php if(isset($errors['spider_password'])){?>
						<div class="erroritem"><?php echo $lang["setup-if_spiderpassword"];?></div>
					<?php } ?>
					<label for="spiderpassword"><?php echo $lang["setup-spiderpassword"];?></label><input id="spiderpassword" type="text" name="spider_password" value="<?php echo $spider_password;?>"/><strong>*</strong><a class="iflink" href="#if-spiderpassword">?</a>
					<p id="if-spiderpassword" class="iteminfo"><?php echo $lang["setup-err_spiderpassword"];?></p>
				</div>
				<div class="configitem">
					<?php if(isset($warnings['scramble_key'])){?>
						<div class="warnitem"><?php echo $lang["setup-err_scramblekey"];?></div>
					<?php } ?>
					<label for="scramblekey"><?php echo $lang["setup-scramblekey"];?></label><input id="scramblekey" type="text" name="scramble_key" value="<?php echo $scramble_key;?>"/><a class="iflink" href="#if-scramblekey">?</a>
					<p id="if-scramblekey" class="iteminfo"><?php echo $lang["setup-if_scramblekey"];?></p>
				</div>
                <div class="configitem">
					<?php if(isset($warnings['api_scramble_key'])){?>
						<div class="warnitem"><?php echo $lang["setup-err_apiscramblekey"];?></div>
					<?php } ?>
					<label for="scramblekey"><?php echo $lang["setup-apiscramblekey"];?></label><input id="apiscramblekey" type="text" name="api_scramble_key" value="<?php echo $api_scramble_key;?>"/><a class="iflink" href="#if-apiscramblekey">?</a>
					<p id="if-apiscramblekey" class="iteminfo"><?php echo $lang["setup-if_apiscramblekey"];?></p>
				</div>

			</p>
			<p class="configsection">
				<h2><?php echo $lang["setup-paths"];?></h2>
				<p><?php echo $lang["setup-pathsdetail"];?></p>
				<div class="configitem">
					<?php if(isset($errors['imagemagick_path'])){?>
						<div class="erroritem"><?php echo $lang["setup-err_path"];?> 'convert'.</div>
					<?php } ?>
					<label for="imagemagickpath"><?php echo str_replace("%bin", "ImageMagick/GraphicsMagick", $lang["setup-binpath"]) . ":"; ?></label><input id="imagemagickpath" type="text" name="imagemagick_path" value="<?php echo @$imagemagick_path; ?>"/>
				</div>
				<div class="configitem">
					<?php if(isset($errors['ghostscript_path'])){?>
						<div class="erroritem"><?php echo $lang["setup-err_path"];?> 'gs'.</div>
					<?php } ?>
					<label for="ghostscriptpath"><?php echo str_replace("%bin", "Ghostscript", $lang["setup-binpath"]) . ":"; ?></label><input id="ghostscriptpath" type="text" name="ghostscript_path" value="<?php echo @$ghostscript_path; ?>"/>
				</div>
				<div class="configitem">
					<?php if(isset($errors['ffmpeg_path'])){?>
						<div class="erroritem"><?php echo $lang["setup-err_path"];?> 'ffmpeg'.</div>
					<?php } ?>
					<label for="ffmpegpath"><?php echo str_replace("%bin", "FFMpeg", $lang["setup-binpath"]) . ":"; ?></label><input id="ffmpegpath" type="text" name="ffmpeg_path" value="<?php echo @$ffmpeg_path; ?>"/>
				</div>
				<div class="configitem">
					<?php if(isset($errors['exiftool_path'])){?>
						<div class="erroritem"><?php echo $lang["setup-err_path"];?> 'exiftool'.</div>
					<?php } ?>
					<label for="exiftoolpath"><?php echo str_replace("%bin", "Exiftool", $lang["setup-binpath"]) . ":"; ?></label><input id="exiftoolpath" type="text" name="exiftool_path" value="<?php echo @$exiftool_path; ?>"/>
				</div>
				<div class="configitem">
				<?php if(isset($errors['antiword_path'])){?>
						<div class="erroritem"><?php echo $lang["setup-err_path"];?> 'AntiWord'.</div>
					<?php } ?>
					<label for="antiwordpath"><?php echo str_replace("%bin", "AntiWord", $lang["setup-binpath"]) . ":"; ?></label><input id="antiwordpath" type="text" name="antiword_path" value="<?php echo @$antiword_path; ?>"/>
				</div>

				<div class="configitem">
					<?php if(isset($errors['pdftotext_path'])){?>
						<div class="erroritem"><?php echo @$lang["setup-err_path"];?> 'pdftotext'.</div>
					<?php } ?>
					<label for="pdftotextpath"><?php echo str_replace("%bin", "PDFtotext", $lang["setup-binpath"]) . ":"; ?></label><input id="pdftotextpath" type="text" name="pdftotext_path" value="<?php echo @$pdftotext_path; ?>"/>
				</div>
			</p>
			<p><?php echo $lang["setup-basicsettingsfooter"];?></p>
		</div>
		<div class="tabs" id="tab-2">
			<h1><?php echo $lang["setup-advancedsettings"];?></h2>
			<h2><?php echo $lang["setup-generaloptions"];?></h2>
			<div class="advsection" id="generaloptions">
				<div class="configitem">
					<label for="allow_password_change"><?php echo $lang["setup-allow_password_change"];?></label><input id="allow_password_change" type="checkbox" name="allow_password_change" <?php echo ($allow_password_change==true?'checked':'');?>/><a class="iflink" href="#if-allow_password_change">?</a>
					<p class="iteminfo" id="if-allow_password_change"><?php echo $lang["setup-if_allowpasswordchange"];?></p>
				</div>
				<div class="configitem">
					<label for="allow_account_request"><?php echo $lang["setup-allow_account_requests"];?></label><input id="allow_account_request" type="checkbox" name="allow_account_request" <?php echo ($allow_account_request==true?'checked':'');?>/>
				</div>
				<div class="configitem">
					<label for="research_request"><?php echo $lang["setup-display_research_request"];?></label><input id="research_request" type="checkbox" name="research_request" <?php echo ($research_request==true?'checked':'');?>/><a class="iflink" href="#if-research_request">?</a>
					<p class="iteminfo" id="if-research_request"><?php echo $lang["setup-if_displayresearchrequest"];?></p>
				</div>
				<div class="configitem">
					<label for="use_theme_as_home"><?php echo $lang["setup-themes_as_home"];?></label><input id="use_theme_as_home" type="checkbox" name="use_theme_as_home" <?php echo ($use_theme_as_home==true?'checked':'');?>/>
				</div>
                <div class="configitem">
					<label for="enable_remote_apis"><?php echo $lang["setup-enable_remote_apis"];?></label><input id="enable_remote_apis" type="checkbox" name="enable_remote_apis" <?php echo ($enable_remote_apis==true?'checked':'');?>/><a class="iflink" href="#if-enable_remote_apis">?</a>
					<p class="iteminfo" id="if-enable_remote_apis"><?php echo $lang["setup-if_enableremoteapis"];?></p>
				</div>

			</div>
			<h2><?php echo $lang["setup-remote_storage_locations"];?></h2>
			<div class="advsection" id="storagelocations">
				<div class="configitem">
					<label for="configstoragelocations"><?php echo $lang["setup-use_remote_storage"];?></label><input id="configstoragelocations" type="checkbox" name="configstoragelocations" value="true" <?php echo ($configstoragelocations==true?'checked':'');?>/><a class="iflink" href="#if-remstorage">?</a>
					<p class="iteminfo" id="if-remstorage"><?php echo $lang["setup-if_useremotestorage"];?></p>
				</div>
				<div id="remstorageoptions" class="starthidden">
					<div class="configitem">
						<label for="storagedir"><?php echo $lang["setup-storage_directory"] . ":"; ?></label><input id="storagedir" type="text" name="storagedir" value="<?php echo $storagedir;?>"/><a class="iflink" href="#if-storagedir">?</a>
						<p class="iteminfo" id="if-storagedir"><?php echo $lang["setup-if_storagedirectory"];?></p>
					</div>
					<div class="configitem">
						<label for="storageurl"><?php echo $lang["setup-storage_url"] . ":"; ?></label><input id="storageurl" type="text" name="storageurl" value="<?php echo $storageurl;?>"/><a class="iflink" href="#if-storageurl">?</a>
						<p class="iteminfo" id="if-storageurl"><?php echo $lang["setup-if_storageurl"];?></p>
					</div>
				</div>
			</div>

			<h2><?php echo $lang["setup-ftp_settings"];?></h2>
			<div class="advsection" id="ftpsettings">
				<div class="configitem">
					<label for="ftp_server"><?php echo $lang["ftpserver"] . ":"; ?></label><input id="ftp_server" name="ftp_server" type="text" value="<?php echo $ftp_server;?>"/><a class="iflink" href="#if-ftpserver">?</a>
					<p class="iteminfo" id="if-ftpserver"><?php echo $lang["setup-if_ftpserver"];?></p>
				</div>
				<div class="configitem">
					<label for="ftp_username"><?php echo $lang["ftpusername"] . ":"; ?></label><input id="ftp_username" name="ftp_username" type="text" value="<?php echo $ftp_username;?>"/>
				</div>
				<div class="configitem">
					<label for="ftp_password"><?php echo $lang["ftppassword"] . ":"; ?></label><input id="ftp_password" name="ftp_password" type="text" value="<?php echo $ftp_password;?>"/>
				</div>
				<div class="configitem">
					<label for="ftp_defaultfolder"><?php echo $lang["ftpfolder"] . ":"; ?></label><input id="ftp_defaultfolder" name="ftp_defaultfolder" type="text" value="<?php echo $ftp_defaultfolder;?>"/>
				</div>
			</div>
		</div>
		<input type="submit" id="submit" name="submit" value="<?php echo $lang["setup-begin_installation"];?>"/>
	</div>
</form>
<?php endif; ?>
<?php if (($develmode)&& isset($config_output)){?>
		<div id="configoutput">
			<h1><?php echo $lang["setup-configuration_file_output"] . ":"; ?></h1>
			<pre><?php echo $config_output; ?></pre>
		</div>
	<?php } ?>
</div>
</body>
</html>
