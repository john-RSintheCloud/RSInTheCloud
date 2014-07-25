<?php


/**
 *  mail class 
 *
 * @author JohnB
 */
class mail
{
    protected $conf = array();
    
    protected $template = array();
    
    protected $email = array();
    
    protected $mailer;


    /**
     * constructor for DI
     * 
     * Needs conf and mailer injecting, others can be set later.
     * 
     * @param array $conf  configuration array (replaces globals)
     * @param array $mailer  sendMail object (injected)
     * @param array $email message, sender, recipient, etc
     * @param array $template optional template and variables
     */
    function __construct($conf, PHPMailer $mailer, $email = array(), $template = array())
    {
        $this->conf = $conf;
        $this->template = $template;
        $this->email = $email;
        $this->mailer = $mailer;
    }

    function setMailOptions($options = array())
    {
        if (empty($options)) return $this;
        
        foreach ($options as $key => $value) {
            $this->email[$key] = $value;
        }
    }
    function setTemplateOptions($options = array())
    {
        if (empty($options)) return $this;
        
        foreach ($options as $key => $value) {
            $this->template[$key] = $value;
        }
    }
    
    
    /**
     *   Legacy function call
     * 
     * @param string $email TO address
     * @param string $subject subject
     * @param string $message
     * @param string $from address
     * @param string $reply_to address
     * @param string $html_template name
     * @param array $templatevars variables to substitute into the template
     * @param string $from_name  name
     * @param string $cc address(es)
     */
    public function send($email, $subject, $message = "", $from = "", $reply_to = "", $html_template = "", $templatevars = null, $from_name = "", $cc = "")
    {
        
        //  set the options and call relevant methods

        if ($from == "") {
            $from = $this->conf->email_from;
        }
        if ($reply_to == "") {
            $reply_to = $this->conf->email_from;
        }
        if ($from_name == "") {
            $from_name = $this->conf->applicationname;
        }

        if ($html_template){
            $template = array(
                'html_template' => $html_template,
                'templatevars' => $templatevars,
            );
            $this->setTemplateOptions($template);
            $body = $this->fillTemplate();
                    
        }
        
    }
    
    public function fillTemplate()
    {
        # Mail templates can include lang, server, site_text, and POST variables by default
        # ex ( [lang_mycollections], [server_REMOTE_ADDR], [text_footer] , [message]
        # additional values must be made available through $templatevars
        # For example, a complex url or image path that may be sent in an
        # email should be added to the templatevars array and passed into send_mail.
        # available templatevars need to be well-documented, and sample templates
        # need to be available.
        
        # Include config  (were globals)
        $email_footer = $this->conf->email_footer;
        $storagedir = $this->conf->storagedir;
        
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
                $this->body = $template;
            }
        }
    }
    
    protected function _sendMail($param)
    {

        if (!isset($this->body)) {
            $this->body = $message;
        }

        if (! file_exists(dirname(__FILE__) . "/../lib/phpmailer_v5.2.6/class.phpmailer.php")) {
            throw new RuntimeException('Where is phpmailer?');
        }
        include_once(dirname(__FILE__) . "/../lib/phpmailer_v5.2.6/class.phpmailer.php");
        include_once(dirname(__FILE__) . "/../lib/phpmailer_v5.2.6/extras/class.html2text.php");

        /**
         * @var PHPMailer
         */
        $mail = $this->mailer;
        
        $useSmtp = $this->conf->use->smtp;
        // use an external SMTP server? (e.g. Gmail)
        if ($useSmtp) {
            $smtp = $this->conf->smtp; //  $use_smtp, $smtp_secure, $smtp_host, $smtp_port, $smtp_auth, $smtp_username, $smtp_password;
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = $smpt->auth;  // authentication enabled/disabled
            $mail->SMTPSecure = $smpt->secure; // '', 'tls' or 'ssl'
            $mail->Host = $smpt->host; // hostname
            $mail->Port = $smpt->port; // port number
            $mail->Username = $smpt->username; // username
            $mail->Password = $smpt->password; // password
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

    public function fillTemplate()
    {
        
    }
}
