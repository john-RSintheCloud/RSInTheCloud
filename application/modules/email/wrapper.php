<?php

/**
 * email Wrapper
 * A wrapper round the file and folder handling functions,
 * to allow a future move to OO design.
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Refactor
 */

function bulk_mail($userlist, $subject, $text, $html = false) {
    global $email_from, $lang, $applicationname;

    # Attempt to resolve all users in the string $userlist to user references.
    if (trim($userlist) == "") {
        return ($lang["mustspecifyoneuser"]);
    }
    $userlist = resolve_userlist_groups($userlist);
    $ulist = trim_array(explode(",", $userlist));

    $emails = resolve_user_emails($ulist);
    $emails = $emails['emails'];

    $templatevars['text'] = stripslashes(str_replace("\\r\\n", "\n", $text));
    $body = $templatevars['text'];

    # Send an e-mail to each resolved user
    for ($n = 0; $n < count($emails); $n++) {
        if ($emails[$n] != "") {
            send_mail($emails[$n], $subject, $body, $applicationname, $email_from, "emailbulk", $templatevars, $applicationname, "", $html);
        }
    }

    # Return an empty string (all OK).
    return "";
}

function send_mail($email, $subject, $message, $from = "", $reply_to = "", $html_template = "", $templatevars = null, $from_name = "", $cc = "") {
    # Send a mail - but correctly encode the message/subject in quoted-printable UTF-8.
    # NOTE: $from is the name of the user sending the email,
    # while $from_name is the name that should be put in the header, which can be the system name
    # It is necessary to specify two since in all cases the email should be able to contain the user's name.
    # old mail function remains the same to avoid possible issues with phpmailer
    # send_mail_phpmailer allows for the use of text and html (multipart) emails,
    # and the use of email templates in Manage Content

    global $always_email_from_user;
    if ($always_email_from_user) {
        global $username, $useremail, $userfullname;
        $from_name = ($userfullname != "") ? $userfullname : $username;
        $from = $useremail;
        $reply_to = $useremail;
    }

    global $always_email_copy_admin;
    if ($always_email_copy_admin) {
        global $email_notify;
        $cc.="," . $email_notify;
    }

    # Send a mail - but correctly encode the message/subject in quoted-printable UTF-8.
    global $use_phpmailer;
    if ($use_phpmailer) {
        send_mail_phpmailer($email, $subject, $message, $from, $reply_to, $html_template, $templatevars, $from_name, $cc);
        return true;
    }

    # No email address? Exit.
    if (trim($email) == "") {
        return false;
    }

    # Include footer
    global $email_footer;
    global $disable_quoted_printable_enc;

    # Work out correct EOL to use for mails (should use the system EOL).
    if (defined("PHP_EOL")) {
        $eol = PHP_EOL;
    } else {
        $eol = "\r\n";
    }

    $message.=$eol . $eol . $eol . $email_footer;

    if ($disable_quoted_printable_enc == false) {
        $message = rs_quoted_printable_encode($message);
        $subject = rs_quoted_printable_encode_subject($subject);
    }

    global $email_from;
    if ($from == "") {
        $from = $email_from;
    }
    if ($reply_to == "") {
        $reply_to = $email_from;
    }
    global $applicationname;
    if ($from_name == "") {
        $from_name = $applicationname;
    }

    if (substr($reply_to, -1) == ",") {
        $reply_to = substr($reply_to, 0, -1);
    }

    $reply_tos = explode(",", $reply_to);

    # Add headers
    $headers = "";
    #$headers .= "X-Sender:  x-sender" . $eol;
    $headers .= "From: ";
    #allow multiple emails, and fix for long format emails
    for ($n = 0; $n < count($reply_tos); $n++) {
        if ($n != 0) {
            $headers.=",";
        }
        if (strstr($reply_tos[$n], "<")) {
            $rtparts = explode("<", $reply_tos[$n]);
            $headers.=$rtparts[0] . " <" . $rtparts[1];
        } else {
            mb_internal_encoding("UTF-8");
            $headers.=mb_encode_mimeheader($from_name, "UTF-8") . " <" . $reply_tos[$n] . ">";
        }
    }
    $headers.=$eol;
    $headers .= "Reply-To: $reply_to" . $eol;

    if ($cc != "") {
        global $userfullname;
        #allow multiple emails, and fix for long format emails
        $ccs = explode(",", $cc);
        $headers .= "Cc: ";
        for ($n = 0; $n < count($ccs); $n++) {
            if ($n != 0) {
                $headers.=",";
            }
            if (strstr($ccs[$n], "<")) {
                $ccparts = explode("<", $ccs[$n]);
                $headers.=$ccparts[0] . " <" . $ccparts[1];
            } else {
                mb_internal_encoding("UTF-8");
                $headers.=mb_encode_mimeheader($userfullname, "UTF-8") . " <" . $ccs[$n] . ">";
            }
        }
        $headers.=$eol;
    }

    $headers .= "Date: " . date("r") . $eol;
    $headers .= "Message-ID: <" . date("YmdHis") . $from . ">" . $eol;
    #$headers .= "Return-Path: returnpath" . $eol;
    //$headers .= "Delivered-to: $email" . $eol;
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "X-Mailer: PHP Mail Function" . $eol;
    if (!is_html($message)) {
        $headers .= "Content-Type: text/plain; charset=\"UTF-8\"" . $eol;
    } else {
        $headers .= "Content-Type: text/html; charset=\"UTF-8\"" . $eol;
    }
    $headers .= "Content-Transfer-Encoding: quoted-printable" . $eol;
    mail($email, $subject, $message, $headers);
}

if (!function_exists("send_mail_phpmailer")) {

    function send_mail_phpmailer($email, $subject, $message = "", $from = "", $reply_to = "", $html_template = "", $templatevars = null, $from_name = "", $cc = "") {

        # if ($use_phpmailer==true) this function is used instead.
        # Mail templates can include lang, server, site_text, and POST variables by default
        # ex ( [lang_mycollections], [server_REMOTE_ADDR], [text_footer] , [message]
        # additional values must be made available through $templatevars
        # For example, a complex url or image path that may be sent in an
        # email should be added to the templatevars array and passed into send_mail.
        # available templatevars need to be well-documented, and sample templates
        # need to be available.
        # Include footer
        global $email_footer, $storagedir;
        $phpversion = phpversion();
        if ($phpversion >= '5.3') {
            if (file_exists(dirname(__FILE__) . "/../lib/phpmailer_v5.2.6/class.phpmailer.php")) {
                include_once(dirname(__FILE__) . "/../lib/phpmailer_v5.2.6/class.phpmailer.php");
                include_once(dirname(__FILE__) . "/../lib/phpmailer_v5.2.6/extras/class.html2text.php");
            }
        } else {
            // less than 5.3
            if (file_exists(dirname(__FILE__) . "/../lib/phpmailer/class.phpmailer.php")) {
                include_once(dirname(__FILE__) . "/../lib/phpmailer/class.phpmailer.php");
                include_once(dirname(__FILE__) . "/../lib/phpmailer/class.html2text.php");
            }
        }

        global $email_from;
        if ($from == "") {
            $from = $email_from;
        }
        if ($reply_to == "") {
            $reply_to = $email_from;
        }
        global $applicationname;
        if ($from_name == "") {
            $from_name = $applicationname;
        }

        #check for html template. If exists, attempt to include vars into message
        if ($html_template != "") {
            # Attempt to verify users by email, which allows us to get the email template by lang and usergroup
            $to_usergroup = sql_query("select lang,usergroup from user where email ='$email'", "");

            if (count($to_usergroup) != 0) {
                $to_usergroupref = $to_usergroup[0]['usergroup'];
                $to_usergrouplang = $to_usergroup[0]['lang'];
            } else {
                $to_usergrouplang = "";
            }

            if ($to_usergrouplang == "") {
                global $defaultlanguage;
                $to_usergrouplang = $defaultlanguage;
            }

            if (isset($to_usergroupref)) {
                $modified_to_usergroupref = hook("modifytousergroup", "", $to_usergroupref);
                if ($modified_to_usergroupref !== null) {
                    $to_usergroupref = $modified_to_usergroupref;
                }

                $results = sql_query("select language,name,text from site_text where page='all' and name='$html_template' and specific_to_group='$to_usergroupref'");
            } else {
                $results = sql_query("select language,name,text from site_text where page='all' and name='$html_template' and specific_to_group is null");
            }

            global $site_text;
            for ($n = 0; $n < count($results); $n++) {
                $site_text[$results[$n]["language"] . "-" . $results[$n]["name"]] = $results[$n]["text"];
            }

            $language = $to_usergrouplang;


            if (array_key_exists($language . "-" . $html_template, $site_text)) {
                $template = $site_text[$language . "-" . $html_template];
            } else {
                global $languages;

                # Can't find the language key? Look for it in other languages.
                reset($languages);
                foreach ($languages as $key => $value) {
                    if (array_key_exists($key . "-" . $html_template, $site_text)) {
                        $template = $site_text[$key . "-" . $html_template];
                        break;
                    }
                }
            }



            if (isset($template) && $template != "") {
                preg_match_all('/\[[^\]]*\]/', $template, $test);
                foreach ($test[0] as $variable) {

                    $variable = str_replace("[", "", $variable);
                    $variable = str_replace("]", "", $variable);


                    # get lang variables (ex. [lang_mycollections])
                    if (substr($variable, 0, 5) == "lang_") {
                        global $lang;
                        $$variable = $lang[substr($variable, 5)];
                    }

                    # get server variables (ex. [server_REMOTE_ADDR] for a user request)
                    else if (substr($variable, 0, 7) == "server_") {
                        $$variable = $_SERVER[substr($variable, 7)];
                    }

                    # [embed_thumbnail] (requires url in templatevars['thumbnail'])
                    else if (substr($variable, 0, 15) == "embed_thumbnail") {
                        $thumbcid = uniqid('thumb');
                        $$variable = "<img style='border:1px solid #d1d1d1;' src='cid:$thumbcid' />";
                    }

                    # deprecated by improved [img_] tag below
                    # embed images (find them in relation to storagedir so that templates are portable)...  (ex [img_storagedir_/../gfx/whitegry/titles/title.gif])
                    else if (substr($variable, 0, 15) == "img_storagedir_") {
                        $$variable = "<img src='cid:" . basename(substr($variable, 15)) . "'/>";
                        $images[] = dirname(__FILE__) . substr($variable, 15);
                    }

                    # embed images - ex [img_gfx/whitegry/titles/title.gif]
                    else if (substr($variable, 0, 4) == "img_") {

                        $image_path = substr($variable, 4);
                        if (substr($image_path, 0, 1) == "/") { // absolute paths
                            $images[] = $image_path;
                        } else { // relative paths
                            $image_path = str_replace("../", "", $image_path);
                            $images[] = dirname(__FILE__) . "/../" . $image_path;
                        }
                        $$variable = "<img src='cid:" . basename($image_path) . "'/>";
                        $images[] = $image_path;
                    }

                    # attach files (ex [attach_/var/www/resourcespace/gfx/whitegry/titles/title.gif])
                    else if (substr($variable, 0, 7) == "attach_") {
                        $$variable = "";
                        $attachments[] = substr($variable, 7);
                    }

                    # get site text variables (ex. [text_footer], for example to
                    # manage html snippets that you want available in all emails.)
                    else if (substr($variable, 0, 5) == "text_") {
                        $$variable = text(substr($variable, 5));
                    }

                    # try to get the variable from POST
                    else {
                        $$variable = getval($variable, "");
                    }

                    # avoid resetting templatevars that may have been passed here
                    if (!isset($templatevars[$variable])) {
                        $templatevars[$variable] = $$variable;
                    }
                }

                if (isset($templatevars)) {
                    foreach ($templatevars as $key => $value) {
                        $template = str_replace("[" . $key . "]", nl2br($value), $template);
                    }
                }
                $body = $template;
            }
        }

        if (!isset($body)) {
            $body = $message;
        }

        global $use_smtp, $smtp_secure, $smtp_host, $smtp_port, $smtp_auth, $smtp_username, $smtp_password;
        $mail = new PHPMailer();
        // use an external SMTP server? (e.g. Gmail)
        if ($use_smtp) {
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = $smtp_auth;  // authentication enabled/disabled
            $mail->SMTPSecure = $smtp_secure; // '', 'tls' or 'ssl'
            $mail->Host = $smtp_host; // hostname
            $mail->Port = $smtp_port; // port number
            $mail->Username = $smtp_username; // username
            $mail->Password = $smtp_password; // password
        }
        $reply_tos = explode(",", $reply_to);
        // only one from address is possible, so only use the first one:
        if (strstr($reply_tos[0], "<")) {
            $rtparts = explode("<", $reply_tos[0]);
            $mail->From = str_replace(">", "", $rtparts[1]);
            $mail->FromName = $rtparts[0];
        } else {
            $mail->From = $reply_tos[0];
            $mail->FromName = $from_name;
        }

        // if there are multiple addresses, that's what replyto handles.
        for ($n = 0; $n < count($reply_tos); $n++) {
            if (strstr($reply_tos[$n], "<")) {
                $rtparts = explode("<", $reply_tos[$n]);
                $mail->AddReplyto(str_replace(">", "", $rtparts[1]), $rtparts[0]);
            } else {
                $mail->AddReplyto($reply_tos[$n], $from_name);
            }
        }

        # modification to handle multiple comma delimited emails
        # such as for a multiple $email_notify
        $emails = $email;
        $emails = explode(',', $emails);
        $emails = array_map('trim', $emails);
        foreach ($emails as $email) {
            if (strstr($email, "<")) {
                $emparts = explode("<", $email);
                $mail->AddAddress(str_replace(">", "", $emparts[1]), $emparts[0]);
            } else {
                $mail->AddAddress($email);
            }
        }

        if ($cc != "") {
            # modification for multiple is also necessary here, though a broken cc seems to be simply removed by phpmailer rather than breaking it.
            $ccs = $cc;
            $ccs = explode(',', $ccs);
            $ccs = array_map('trim', $ccs);
            global $userfullname;
            foreach ($ccs as $cc) {
                if (strstr($cc, "<")) {
                    $ccparts = explode("<", $cc);
                    $mail->AddCC(str_replace(">", "", $ccparts[1]), $ccparts[0]);
                } else {
                    $mail->AddCC($cc, $userfullname);
                }
            }
        }
        $mail->CharSet = "utf-8";

        if (is_html($body)) {
            $mail->IsHTML(true);
        } else {
            $mail->IsHTML(false);
        }

        $mail->Subject = $subject;
        $mail->Body = $body;

        if (isset($embed_thumbnail) && isset($templatevars['thumbnail'])) {
            $mail->AddEmbeddedImage($templatevars['thumbnail'], $thumbcid, $thumbcid, 'base64', 'image/jpeg');
        }
        if (isset($images)) {
            foreach ($images as $image) {
                $mail->AddEmbeddedImage($image, basename($image), basename($image), 'base64', 'image/gif');
            }
        }
        if (isset($attachments)) {
            foreach ($attachments as $attachment) {
                $mail->AddAttachment($attachment, basename($attachment));
            }
        }
        if (is_html($body)) {
            $h2t = new html2text($body);
            $text = $h2t->get_text();
            $mail->AltBody = $text;
        }
        if (!$mail->Send()) {
            echo "Message could not be sent. <p>";
            echo "Mailer Error: " . $mail->ErrorInfo;
            exit;
        }
        hook("aftersendmailphpmailer", "", $email);
    }

}

function rs_quoted_printable_encode($string, $linelen = 0, $linebreak = "=\r\n", $breaklen = 0, $encodecrlf = false) {
    // Quoted printable encoding is rather simple.
    // Each character in the string $string should be encoded if:
    //  Character code is <0x20 (space)
    //  Character is = (as it has a special meaning: 0x3d)
    //  Character is over ASCII range (>=0x80)
    $len = strlen($string);
    $result = '';
    for ($i = 0; $i < $len; $i++) {
        if (($linelen >= 76) && (false)) { // break lines over 76 characters, and put special QP linebreak
            $linelen = $breaklen;
            $result.= $linebreak;
        }
        $c = ord($string[$i]);
        if (($c == 0x3d) || ($c >= 0x80) || ($c < 0x20)) { // in this case, we encode...
            if ((($c == 0x0A) || ($c == 0x0D)) && (!$encodecrlf)) { // but not for linebreaks
                $result.=chr($c);
                $linelen = 0;
                continue;
            }
            $result.='=' . str_pad(strtoupper(dechex($c)), 2, '0');
            $linelen += 3;
            continue;
        }
        $result.=chr($c); // normal characters aren't encoded
        $linelen++;
    }
    return $result;
}

function rs_quoted_printable_encode_subject($string, $encoding = 'UTF-8') {
    // use this function with headers, not with the email body as it misses word wrapping
    $len = strlen($string);
    $result = '';
    $enc = false;
    for ($i = 0; $i < $len;  ++$i) {
        $c = $string[$i];
        if (ctype_alpha($c))
            $result.=$c;
        else if ($c == ' ') {
            $result.='_';
            $enc = true;
        } else {
            $result.=sprintf("=%02X", ord($c));
            $enc = true;
        }
    }
    //L: so spam agents won't mark your email with QP_EXCESS
    if (!$enc)
        return $string;
    return '=?' . $encoding . '?q?' . $result . '?=';
}


function email_user_request() {
    # E-mails the submitted user request form to the team.
    global $applicationname, $email_from, $user_email, $baseurl, $email_notify, $lang, $custom_registration_fields, $custom_registration_required;

    # Add custom fields
    $c = "";
    if (isset($custom_registration_fields)) {
        $custom = explode(",", $custom_registration_fields);

        # Required fields?
        if (isset($custom_registration_required)) {
            $required = explode(",", $custom_registration_required);
        }

        for ($n = 0; $n < count($custom); $n++) {
            if (isset($required) && in_array($custom[$n], $required) && getval("custom" . $n, "") == "") {
                return false; # Required field was not set.
            }

            $c.=i18n_get_translated($custom[$n]) . ": " . getval("custom" . $n, "") . "\n\n";
        }
    }

    # Required fields (name, email) not set?
    if (getval("name", "") == "") {
        return false;
    }
    if (getval("email", "") == "") {
        return false;
    }

    # Build a message
    $message = $lang["userrequestnotification1"] . "\n\n" . $lang["name"] . ": " . getval("name", "") . "\n\n" . $lang["email"] . ": " . getval("email", "") . "\n\n" . $lang["comment"] . ": " . getval("userrequestcomment", "") . "\n\n" . $lang["ipaddress"] . ": '" . $_SERVER["REMOTE_ADDR"] . "'\n\n" . $c . "\n\n" . $lang["userrequestnotification2"] . "\n$baseurl";

    send_mail($email_notify, $applicationname . ": " . $lang["requestuserlogin"] . " - " . getval("name", ""), $message, "", $user_email, "", "", getval("name", ""));

    return true;
}
