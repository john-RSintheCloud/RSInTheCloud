<?php
/**
 * Initial setup page.
 *
 * @package ResourceSpace
 * @subpackage Pages_Misc
 */

//Development Mode:  Set to true to change the config.php check to devel.config.php and output to devel.config.php instead.  Also displays the config file output in a div at the bottom of the page.
$develmode = false;
$confPath = APPLICATION_PATH . "../_config";
if ($develmode)
	$outputfile = $confPath . '/devel.config.php';
else
	$outputfile = $confPath . '/config.php';


// Define some vars to prevent warnings (quick fix)
$configstoragelocations=false;
$storageurl="";
$storagedir=""; # This variable is used in the language files.

require_once $confPath . '/config.default.php';
//Check if config file already exists and warn if it does.
$errMsg = '';
if (file_exists($outputfile)){
    $errMsg = ' <div id="errorheader"><?php echo $lang["setup-alreadyconfigured"];?></div> ';
    include $outputfile;
}

$defaultlanguage = get_post('defaultlanguage');
$lang = set_language($defaultlanguage);


/**

 *  * Ad Hoc Function to support setup page
 *
 * Copied from includes/db.php
 *
 * @param string $value String representation of byte size (including K, M, or G
 * @return int Number of KB
 */
function ResolveKB($value) { //Copied from includes/db.php
    $value=trim(strtoupper($value));
    if (substr($value,-1,1)=="K")
        {
        return substr($value,0,strlen($value)-1);
        }
    if (substr($value,-1,1)=="M")
        {
        return substr($value,0,strlen($value)-1) * 1024;
        }
    if (substr($value,-1,1)=="G")
        {
        return substr($value,0,strlen($value)-1) * 1024 * 1024;
        }
    return $value;
}
/**
 * Generates a random string of requested length.
 *
 * Used to generate initial spider and scramble keys.
 *
 * @param int $length Optional, default=12
 * @return string Random character string.
 */
function generatePassword($length=12) {
    $vowels = 'aeuyAEUY';
    $consonants = 'bdghjmnpqrstvzBDGHJLMNPQRSTVWXZ23456789';
    $password = '';
    $alt = time() % 2;
    for ($i = 0; $i < $length; $i++) {
        if ($alt == 1) {
            $password .= $consonants[(rand() % strlen($consonants))];
            $alt = 0;
        } else {
            $password .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;
        }
    }
    return $password;
}
/**
 * Santitizes input from a given request key.
 *
 * Uses filter_var and string sanitation
 *
 * @param string $key _REQUEST key to sanitize and return
 * @return string Santized _REQUEST key.
 **/
function get_post($key){
    return filter_var(@$_REQUEST[$key], FILTER_SANITIZE_STRING);
}
/**
 * Returns true if a given $_REQUEST key is set.
 *
 * @param string $key _REQUEST key to test.
 * @return bool
 */

function get_post_bool($key){
    if (isset($_REQUEST[$key]))
        return true;
    else
        return false;
}
/**
 * Trims whitespace and trailing slash.
 *
 * @param string $data
 * @return string
 */

function sslash($data){
    $stripped = rtrim($data);
    $stripped = rtrim($data, ' /');
    return $stripped;
}
/**
 * Opens an HTTP request to a host to determine if the url is reachable.
 *
 * Returns true if url is reachable.
 *
 * @param string $url
 * @return bool
 */

function url_exists($url)
{
    $parsed_url = parse_url($url);
    $host = @$parsed_url['host'];
    $path = @$parsed_url['path'];
    $port = @$parsed_url['port'];
    if (empty($path)) $path = "/";
    if (!isset($port)) {$port=0;}
    if ($port==0) {$port=80;}
    // Build HTTP 1.1 request header.
    $headers =  "GET $path HTTP/1.1\r\n" .
                "Host: $host\r\n" .
                "User-Agent: RS-Installation/1.0\r\n\r\n";
    $fp = fsockopen($host, $port, $errno, $errmsg, 5); //5 second timeout.  Assume that if we can't open the socket connection quickly the host or port are probably wrong.
    if (!$fp) {
        return false;
    }
    fwrite($fp, $headers);
    while(!feof($fp)) {
        $resp = fgets($fp, 4096);
        if(strstr($resp, 'HTTP/1.')){
            fclose($fp);
            $tmp = explode(' ',$resp);
            $response_code = $tmp[1];
            if ($response_code == 200)
                return true;
            else
                return false;
        }
    }
    fclose($fp);
    return false;
}
/**
 * Sets the language to be used.
 *
 * @param string $defaultlanguage
 * @return array
 */
function set_language($defaultlanguage)
{
	global $languages;
	global $storagedir, $applicationname, $homeanim_folder; # Used in the language files.
	if (file_exists("../languages/en.php")) {include "../languages/en.php";}
	if ($defaultlanguage==''){
		//See if we can auto-detect the most likely language.  The user can override this.
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
			$httplanguage = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
			if(array_key_exists($httplanguage[0],$languages)){
				$defaultlanguage = $httplanguage[0];
			}
		}
	}
	if ($defaultlanguage!='en'){
		if (file_exists("../languages/".$defaultlanguage.".php")){
			include "../languages/".$defaultlanguage.".php";
		}
	}
	return $lang;
}

	if (!(isset($_REQUEST['submit']))){ //No Form Submission, lets setup some defaults
		if (!isset($storagedir) | $storagedir=="")
			{
			$storagedir = dirname(__FILE__)."/../filestore";
			$lang = set_language($defaultlanguage); # Updates $lang with $storagedir which is used in some strings.
			}
		if (isset($_SERVER['HTTP_HOST']))
			$baseurl = 'http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strlen($_SERVER['PHP_SELF'])-16);
		else
			$baseurl = 'http://'.php_uname('n'); //Set the baseurl to the machine hostname.

		//Generate default random keys.
		$scramble_key = generatePassword();
		$spider_password = generatePassword();
        $api_scramble_key = generatePassword();
		//Setup search paths (Currently only Linux/Mac OS X)
		$os=php_uname('s');
		if($os=='Linux' || $os=="Darwin"){
			$search_paths[]='/usr/bin';
			$search_paths[]='/sw/bin';
			$search_paths[]='/usr/share/bin';
			$search_paths[]='/usr/local/bin';
			$search_paths[]='/opt/local/bin';
		}
		//Check if we're on windows and set config_windows if we are.
		elseif(stristr($os,'windows')){
			$config_windows = true;
		}
		if (isset($search_paths)){
			foreach($search_paths as $path){
				if (file_exists($path.'/convert'))
					$imagemagick_path = $path;
				if (file_exists($path.'/gs'))
					$ghostscript_path = $path;
				if (file_exists($path.'/ffmpeg'))
					$ffmpeg_path = $path;
				if (file_exists($path.'/exiftool'))
					$exiftool_path = $path;
				if (file_exists($path.'/antiword'))
					$antiword_path= $path;
				if (file_exists($path.'/pdftotext'))
					$pdftotext_path = $path;
			}
		}

	}
	else { //Form was submitted, lets do it!
		//Generate config.php Header
		//Note: The opening php tag is missing and is added when the file is written.
		//This allows the config to be displayed in the bottom div when in development mode.
		$config_windows = get_post_bool('config_windows');
		$exe_ext = $config_windows==true?'.exe':'';
		$config_output="";
		$config_output .= "###############################\r\n";
		$config_output .= "## ResourceSpace\r\n";
		$config_output .= "## Local Configuration Script\r\n";
		$config_output .= "###############################\r\n\r\n";
		$config_output .= "# All custom settings should be entered in this file.\r\n";
		$config_output .= "# Options may be copied from config.default.php and configured here.\r\n\r\n";

		//Grab MySQL settings
		$mysql_server = get_post('mysql_server');
		$mysql_username = get_post('mysql_username');
		$mysql_password = get_post('mysql_password');
		$mysql_db = get_post('mysql_db');
		//Make a connection to the database using the supplied credentials and see if we can create and drop a table.
		if (@mysql_connect($mysql_server, $mysql_username, $mysql_password)){
			$mysqlversion=mysql_get_server_info();

			if ($mysqlversion<'5') {
				$errors['databaseversion'] = true;
			}
			else {
				if (@mysql_select_db($mysql_db)){
					if (@mysql_query("CREATE table configtest(test varchar(30))")){
						@mysql_query("DROP table configtest");
					}
					else {$errors['databaseperms'] = true;}
				}
				else {$errors['databasedb'] = true;}
			}
		}
		else {
			switch (mysql_errno()){
				case 1045:  //User login failure.
					$errors['databaselogin'] = true;
					break;
				default: //Must be a server problem.
					$errors['databaseserver'] = true;
					break;
			}
		}
		if (isset($errors)){
			$errors['database'] = mysql_error();
		}
		else {
			//Test passed: Output MySQL config section
			$config_output .= "# MySQL database settings\r\n";
			$config_output .= "\$mysql_server = '$mysql_server';\r\n";
			$config_output .= "\$mysql_username = '$mysql_username';\r\n";
			$config_output .= "\$mysql_password = '$mysql_password';\r\n";
			$config_output .= "\$mysql_db = '$mysql_db';\r\n";
			$config_output .= "\r\n";
		}

		//Check MySQL bin path (not required)
		$mysql_bin_path = sslash(get_post('mysql_bin_path'));
		if ((isset($mysql_bin_path)) && ($mysql_bin_path!='')){
			if (!file_exists($mysql_bin_path.'/mysqldump'.$exe_ext))
				$errors['mysqlbinpath'] = true;
			else $config_output .="\$mysql_bin_path = '$mysql_bin_path';\r\n\r\n";
		}

		//Check baseurl (required)
		$baseurl = sslash(get_post('baseurl'));
		# In certain PHP versions there is a bug in filter_var using FILTER_VALIDATE_URL causing correct URLs containing a hyphen to fail.
		if (filter_var("http://www.filter-test.com", FILTER_VALIDATE_URL))
			{
			# The filter is working.
			$filterresult = filter_var($baseurl, FILTER_VALIDATE_URL);
			}
		else
			{
			# The filter is not working, use the hostname of the $baseurl and replace the problematic characters.
			$testbaseurl = str_replace(
				parse_url($baseurl,PHP_URL_HOST),
				str_replace(
					array("_", "-"),
					array("^", "x"), # _ is not allowed for hostname, - is allowed
					parse_url($baseurl,PHP_URL_HOST)),
				$baseurl);
			$filterresult = filter_var($testbaseurl, FILTER_VALIDATE_URL);
			}
		if ((isset($baseurl)) && ($baseurl!='') && ($baseurl!='http://my.site/resourcespace') && ($filterresult)){
			//Check that the base url seems correct by attempting to fetch the license file
			if (url_exists($baseurl.'/license.txt')){
				$config_output .= "# Base URL of the installation\r\n";
				$config_output .= "\$baseurl = '$baseurl';\r\n\r\n";
			}
			else { //Under certain circumstances this test may fail, but the URL is still correct, so warn the user.
				$warnings['baseurlverify']= true;
			}
		}
		else {
			$errors['baseurl'] = true;
		}

		//Verify email addresses are valid
		$config_output .= "# Email settings\r\n";
		$email_from = get_post('email_from');
		if ($email_from != ''){
			if (filter_var($email_from, FILTER_VALIDATE_EMAIL) && ($email_from!='resourcespace@my.site'))
				$config_output .= "\$email_from = '$email_from';\r\n";
			else
				$errors['email_from']=true;
		}
		$email_notify = get_post('email_notify');
		if ($email_notify !=''){
			if (filter_var($email_notify, FILTER_VALIDATE_EMAIL) && ($email_notify!='resourcespace@my.site'))
				$config_output .= "\$email_notify = '$email_notify';\r\n\r\n";
			else
				$errors['email_notify']=true;
		}

		//Check the spider_password (required) and scramble_key (optional)
		$spider_password = get_post('spider_password');
		if ($spider_password!='')
			$config_output .= "\$spider_password = '$spider_password';\r\n";
		else
			$errors['spider_password']=true;
		$scramble_key = get_post('scramble_key');
		if ($scramble_key!='')
			$config_output .= "\$scramble_key = '$scramble_key';\r\n\r\n";
		else
			$warnings['scramble_key']=true;
        $api_scramble_key = get_post('api_scramble_key');
        if ($api_scramble_key!='')
			$config_output .= "\$api_scramble_key = '$api_scramble_key';\r\n\r\n";
		else
			$warnings['api_scramble_key']=true;

		$config_output .= "# Paths\r\n";
		//Verify paths actually point to a useable binary
		$imagemagick_path = sslash(get_post('imagemagick_path'));
		$ghostscript_path = sslash(get_post('ghostscript_path'));
		$ffmpeg_path = sslash(get_post('ffmpeg_path'));
		$exiftool_path = sslash(get_post('exiftool_path'));
		$antiword_path = sslash(get_post('antiword_path'));
		$pdftotext_path = sslash(get_post('pdftotext_path'));
		if ($imagemagick_path!='')
			if (!file_exists($imagemagick_path.'/convert'.$exe_ext))
				$errors['imagemagick_path'] = true;
			else $config_output .= "\$imagemagick_path = '$imagemagick_path';\r\n";
		if ($ghostscript_path!='')
			if (!file_exists($ghostscript_path.'/gs'.$exe_ext))
				$errors['ghostscript_path'] = true;
			else $config_output .= "\$ghostscript_path = '$ghostscript_path';\r\n";
		if ($ffmpeg_path!='')
			if (!file_exists($ffmpeg_path.'/ffmpeg'.$exe_ext))
				$errors['ffmpeg_path'] = true;
			else $config_output .= "\$ffmpeg_path = '$ffmpeg_path';\r\n";
		if ($exiftool_path!='')
			if (!file_exists($exiftool_path.'/exiftool'.$exe_ext))
				$errors['exiftool_path'] = true;
			else $config_output .= "\$exiftool_path = '$exiftool_path';\r\n";
		if ($antiword_path!='')
			if (!file_exists($antiword_path.'/antiword'.$exe_ext))
				$errors['antiword_path'] = true;
			else $config_output .= "\$antiword_path = '$antiword_path';\r\n";
		if ($pdftotext_path!='')
			if (!file_exists($pdftotext_path.'/pdftotext'.$exe_ext))
				$errors['pdftotext_path'] = true;
			else $config_output .= "\$pdftotext_path = '$pdftotext_path';\r\n\r\n";

		//Deal with some checkboxes
		if (!$allow_account_request = get_post_bool('allow_account_request'))
			$config_output .= "\$allow_account_request = false;\r\n";
        if ($enable_remote_apis = get_post_bool('enable_remote_apis'))
			$config_output .= "\$enable_remote_apis = true;\r\n";
		if (!$allow_password_change = get_post_bool('allow_password_change'))
			$config_output .= "\$allow_password_change = false;\r\n";
		if ($research_request = get_post_bool('research_request'))
			$config_output .= "\$research_request = true;\r\n";
		if ($use_theme_as_home = get_post_bool('use_theme_as_home'))
			$config_output .= "\$use_theme_as_home = true;\r\n";
		if ($disable_languages = get_post_bool('disable_languages'))
			$config_output .= "\$disable_languages = true;\r\n";
		if ($config_windows)
			$config_output .= "\$config_windows = true;\r\n";
		if ($defaultlanguage!='en')
			$config_output .= "\$defaultlanguage = '$defaultlanguage';\r\n";

		//Advanced Settings
		if ($_REQUEST['applicationname']!=$applicationname){
			$applicationname = get_post('applicationname');
			$config_output .= "\$applicationname = '$applicationname';\r\n";
		}
		if (get_post_bool('configstoragelocations')){
			$configstoragelocations = true;
			$storagedir = get_post('storagedir');
			$storageurl = get_post('storageurl');
			$config_output .= "\$storagedir = '$storagedir';\r\n";
			$config_output .= "\$storageurl = '$storageurl';\r\n";
		}
		else {
			$storagedir = dirname(__FILE__)."/../filestore";
			$configstoragelocations = false;
		}
		$ftp_server = get_post('ftp_server');
		$ftp_username = get_post('ftp_username');
		$ftp_password = get_post('ftp_password');
		$ftp_defaultfolder = get_post('ftp_defaultfolder');
		$config_output .= "\$ftp_server = '$ftp_server';\r\n";
		$config_output .= "\$ftp_username = '$ftp_username';\r\n";
		$config_output .= "\$ftp_password = '$ftp_password';\r\n";
		$config_output .= "\$ftp_defaultfolder = '$ftp_defaultfolder';\r\n";

		// set display defaults for new installs
		$config_output .= "\$thumbs_display_fields = array(8,3);\r\n";
		$config_output .= "\$list_display_fields = array(8,3,12);\r\n";
		$config_output .= "\$sort_fields = array(12);\r\n";

                // Set imagemagick default for new installs to expect the newer version with the sRGB bug fixed.
                $config_output .= "\$imagemagick_colorspace = \"sRGB\";\r\n";

	}

    //Output Section

if ((isset($_REQUEST['submit'])) && (!isset($errors))){
	//Form submission was a success.  Output the config file and refrain from redisplaying the form.
	$fhandle = fopen($outputfile, 'w') or die ("Error opening output file.  (This should never happen, we should have caught this before we got here)");
	fwrite($fhandle, "<?php\r\n".$config_output); //NOTE: php opening tag is prepended to the output.
	fclose($fhandle);

    $success = true;
} else {

    $continue = true;
    $phpversion = phpversion();
    if ($phpversion<'4.4')
    {
        $result = $lang["status-fail"] . ": " . str_replace("?", "4.4", $lang["shouldbeversion"]);
        $pass = false;
        $continue = false;
    }
    else
    {
        $result = $lang["status-ok"];
        $pass = true;
    }
    $passResult =  '<p class="' . ($pass==true?'':'failure') .'">'
        . str_replace("?", "PHP", $lang["softwareversion"]) . ": "
        . $phpversion . ($pass==false?'<br>':' ') . "(" . $result . ")</p>
            <p>" . str_replace("%phpinifile", php_ini_loaded_file(), $lang["php-config-file"]) . '</p>';

    if (function_exists('gd_info')){
        $gdinfo = gd_info();
    }
    if (is_array($gdinfo)){
        $version = $gdinfo["GD Version"];
        $result = $lang["status-ok"];
        $pass = true;
    } else {
        $version = $lang["status-notinstalled"];
        $result = $lang["status-fail"];
        $pass = false;
        $continue = false;
    }

    $passResult .=  '<p class="' . ($pass==true?'':'failure') .'">'
        . str_replace("?", "GD", $lang["softwareversion"]) . ": "
        . $version . ($pass==false?'<br>':' ') . "(" . $result . ")</p>";

    $memory_limit=ini_get("memory_limit");
    if (ResolveKB($memory_limit)<(200*1024))
        {
        $result = $lang["status-warning"] . ": " . str_replace("?", "200M", $lang["shouldbeormore"]);
        $pass = false;
        }
    else
        {
        $result = $lang["status-ok"];
        $pass = true;
        }

    $passResult .=  '<p class="' . ($pass==true?'':'failure') .'">'
        . str_replace("?", "memory_limit", $lang["phpinivalue"]) . ": "
        . $memory_limit . ($pass==false?'<br>':' ') . "(" . $result . ")</p>";

    $post_max_size = ini_get("post_max_size");
    if (ResolveKB($post_max_size)<(100*1024))
        {
        $result = $lang["status-warning"] . ": " . str_replace("?", "100M", $lang["shouldbeormore"]);
        $pass = false;
        }
    else
        {
        $result = $lang["status-ok"];
        $pass = true;
        }
    $passResult .=  '<p class="' . ($pass==true?'':'failure') .'">'
        . str_replace("?", "post_max_size", $lang["phpinivalue"]) . ": "
        . $post_max_size . ($pass==false?'<br>':' ') . "(" . $result . ")</p>";

    $upload_max_filesize = ini_get("upload_max_filesize");
    if (ResolveKB($upload_max_filesize)<(100*1024))
        {
        $result = $lang["status-warning"] . ": " . str_replace("?", "100M", $lang["shouldbeormore"]);
        $pass = false;
        }
    else
        {
        $result = $lang["status-ok"];
        $pass = true;
        }

    $passResult .=  '<p class="' . ($pass==true?'':'failure') .'">'
        . str_replace("?", "upload_max_filesize", $lang["phpinivalue"]) . ": "
        . $upload_max_filesize . ($pass==false?'<br>':' ') . "(" . $result . ")</p>";

    $success = is_writable($confPath);
    if ($success===false)
        {
        $result = $lang["status-fail"] . ": " . $lang["setup-include_not_writable"];
        $pass = false;
        $continue = false;
        }
    else
        {
        $result = $lang["status-ok"];
        $pass = true;
        }

    $passResult .=  '<p class="' . ($pass==true?'':'failure') .'">'
        . $lang["setup-checkconfigwrite"] . ($pass==false?'<br>':' ')
        . "(" . $result . ")</p>";

    if (!file_exists($storagedir)) {@mkdir ($storagedir,0777);}
    $success = is_writable($storagedir);
    if ($success===false)
        {
        $result = $lang["status-warning"] . ": " . $lang["nowriteaccesstofilestore"] . "<br/>" . $lang["setup-override_location_in_advanced"];
        $pass = false;
        }
    else
        {
        $result = $lang["status-ok"];
        $pass = true;
        }
    $passResult .=  '<p class="' . ($pass==true?'':'failure') .'">'
        . $lang["setup-checkstoragewrite"] . ($pass==false?'<br>':' ')
        . "(" . $result . ")</p>";

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
<?php } ?>
<?php if (($develmode)&& isset($config_output)){?>
		<div id="configoutput">
			<h1><?php echo $lang["setup-configuration_file_output"] . ":"; ?></h1>
			<pre><?php echo $config_output; ?></pre>
		</div>
	<?php } ?>
</div>
</body>
</html>
