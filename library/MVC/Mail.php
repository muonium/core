<?php
namespace library\MVC;

class Mail
{
    protected $_from = "muonium@muonium.ch";
    protected $_to;
    protected $_subject;
    protected $_message;
    
    function __set($attr, $val) {
        $this->$attr = $val;
    }

    function send() {

        $passage_line = "\n";
        // We filter servers that encounter bugs.
        if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $this->_to))
            $passage_line = "\r\n";

        //=====Message in txt format and in HTML format
        $message_txt = strip_tags(str_replace("<br />", $passage_line, str_replace("<br>", $passage_line, $this->_message)));
        $message_html = $this->_message;
        //==========

        //=====Create the boundary
        $boundary = "-----=".md5(rand());
        //==========

        //=====Create the header
        $header = "From: \"Muonium\"<".$this->_from.">".$passage_line;
        $header.= "Reply-to: \"Muonium\" <".$this->_from.">".$passage_line;
        $header.= "MIME-Version: 1.0".$passage_line;
        $header.= "Content-Type: multipart/alternative;".$passage_line." boundary=\"$boundary\"".$passage_line;
        //==========

        //=====Create the message
        $message = $passage_line."--".$boundary.$passage_line;
        //=====Add txt formatted message
        $message.= "Content-Type: text/plain; charset=\"utf-8\"".$passage_line;
        $message.= "Content-Transfer-Encoding: 8bit".$passage_line;
        $message.= $passage_line.$message_txt.$passage_line;
        //==========
        $message.= $passage_line."--".$boundary.$passage_line;
        //=====Add HTML formatted message
        $message.= "Content-Type: text/html; charset=\"utf-8\"".$passage_line;
        $message.= "Content-Transfer-Encoding: 8bit".$passage_line;
        $message.= $passage_line.$message_html.$passage_line;
        //==========
        $message.= $passage_line."--".$boundary."--".$passage_line;
        $message.= $passage_line."--".$boundary."--".$passage_line;
        //==========

        //=====Send the mail
        mail($this->_to, $this->_subject, $message, $header);
    }
};

