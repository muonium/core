<?php
namespace library\MVC;
use \config as conf;

require_once(ROOT.'/library/PHPMailer/class.phpmailer.php');
require_once(ROOT.'/library/PHPMailer/class.smtp.php');

class Mail
{
    protected $_to;
    protected $_subject;
    protected $_message;

    function __set($attr, $val) {
        $this->$attr = $val;
    }

    function send() {
        $passage_line = "\n";
        // We filter servers that encounter bugs.
        if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $this->_to)) {
            $passage_line = "\r\n";
        }

        // Message in txt format and in HTML format
        $message_txt = strip_tags(str_replace("<br />", $passage_line, str_replace("<br>", $passage_line, $this->_message)));
        $message_html = $this->_message;

        $mail = new \PHPMailer;
        //$mail->SMTPDebug = 3; // Debug
        $mail->isSMTP();
        $mail->Host = conf\confMail::smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = conf\confMail::user;
        $mail->Password = conf\confMail::password;
        $mail->SMTPSecure = conf\confMail::smtp_secure;
        $mail->Port = conf\confMail::port;

        $mail->setFrom($this->_from, $this->_fromName);
        $mail->addAddress($this->_to);
        $mail->isHTML(true);
        $mail->Subject = $this->_subject;
        $mail->Body    = $message_html;
        $mail->AltBody = $message_txt;

        $mail->send();
    }
};
