<?php
/**
 * errorWrapper
 * A wrapper round the error handling and reporting functions,
 * to allow a future move to OO design.
 *
 * @author John Brookes <john@RSintheClouds.com>
 * @package RSintheClouds
 * @subpackage Refactor
*/

error_reporting(E_ALL);
set_error_handler("errorhandler");

function errorhandler($errno, $errstr, $errfile, $errline)
	{
	global $baseurl,$pagename, $show_report_bug_link,$email_errors;
	if (!error_reporting()) {return true;}
	if (!isset($pagename) || $pagename!="upload_java")
		{
		?>
		</select></table></table></table>
		<div style="border:1px solid black;font-family:verdana,arial,helvetica;position:absolute;top:100px;left:100px; background-color:white;width:400px;padding:20px;border-bottom-width:4px;border-right-width:4px;font-size:15px;color:black;">
		<table cellpadding=5 cellspacing=0><tr><td valign=middle><img src="<?php echo $baseurl?>/pages/admin/gfx/cherrybomb.gif" width="48" height="48"></td><td valign=middle align=left><span style="font-size:22px;">Sorry, an error has occurred.</span></td></tr></table>
		<p style="font-size:11px;color:black;margin-top:20px;">Please <a href="#" onClick="history.go(-1)">go back</a> and try something else.</p>
		<?php global $show_error_messages; if ($show_error_messages) { ?>
		<p style="font-size:11px;color:black;">You can <a href="<?php echo $baseurl?>/pages/check.php">check</a> your installation configuration.</p>
		<hr style="margin-top:20px;"><p style="font-size:11px;color:black;"><?php echo htmlspecialchars("$errfile line $errline: $errstr"); ?></p>
		<?php } ?>
		</div>
		<?php
		if ($email_errors){
			global $email_notify,$email_from,$email_errors_address,$applicationname;
			if ($email_errors_address==""){$email_errors_address=$email_notify;}
			send_mail($email_errors_address,$applicationname." Error",$errfile." line ".$errline.": ".$errstr,$email_from,$email_from,"",null,"Error Reporting",false);
			}
		exit();
		}
	else
		{
		# Special error message format for Java uploader, so the error is correctly displayer
		exit("ERROR: Error processing file\\n\\n $errfile line $errline\\n$errstr");
		}
	}

