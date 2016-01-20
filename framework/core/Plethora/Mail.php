<?php

namespace Plethora;

/**
 * Mail class.
 *
 * @package        Plethora
 * @author         Zalazdi
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Mail {

    /**
     * Mail subject.
     *
     * @access  public
     * @var     string
     * @since   1.0.0-alpha
     */
    public $sSubject = '';

    /**
     * From addresses
     *
     * Mail "From" adresses in array
     *
     * @access  public
     * @var     array
     * @since   1.0.0-alpha
     */
    public $aFrom = [];

    /**
     * To addresses.
     *
     * Mail "To" adresses in array
     *
     * @access  public
     * @var     array
     * @since   1.0.0-alpha
     */
    public $aTo = [];

    /**
     * CC addresses.
     *
     * Mail "CC" adresses in array
     *
     * @access  public
     * @var     array
     * @since   1.0.0-alpha
     */
    public $aCC = [];

    /**
     * Bcc addresses.
     *
     * Mail "Bcc" adresses in array
     *
     * @access  public
     * @var     array
     * @since   1.0.0-alpha
     */
    public $aBCC = [];

    /**
     * Message body.
     *
     * @access  public
     * @var     string
     * @since   1.0.0-alpha
     */
    public $sBody = '';

    /**
     * Message body content type (like text/html text/plain)
     *
     * @access  public
     * @var     string
     * @since   1.0.0-alpha
     */
    public $sBodyContentType = '';

    /**
     * Message alternative body
     *
     * @access  public
     * @var     string
     * @since   1.0.0-alpha
     */
    public $sAlternativeBody = '';

    /**
     * Message body content type (like text/html text/plain)
     *
     * @access  public
     * @var     string
     * @since   1.0.0-alpha
     */
    public $sAlternativeBodyContentType = '';

    /**
     * Attachments files.
     *
     * @access  public
     * @var     array
     * @since   1.0.0-alpha
     */
    public $aAttachments = [];

    /**
     * Boundary
     *
     * Generated random string to separate bodys and atachments in mail
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $boundary;

    /**
     * Constructor
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct() {
        $this->boundary = md5(time());
    }

    /**
     * Set subject for particular e-mail.
     *
     * @access   public
     * @param    string $sSubject
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setSubject($sSubject) {
        $this->sSubject = $sSubject;

        return $this;
    }

    /**
     * Get e-mail subject.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getSubject() {
        return $this->sSubject;
    }

    /**
     * Set From adresses.
     *
     * @access   public
     * @param    string $from
     * @return   Mail
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setFrom($from) {
        $this->aFrom[] = $from;

//        if(func_num_args() == 0) {
//            throw new Exception\Fatal('No senders set to this Mail.');
//        }
//
//        $this->aFrom = [];
//
//        foreach(func_get_args() as $arg) {
//            if(is_array($arg)) {
//                foreach($arg as $key => $value) {
//                    if(!is_int($key)) {
//                        $this->aFrom[] = $value.' <'.$key.'>';
//                    } else {
//                        $this->aFrom[] = $value;
//                    }
//                }
//            } else {
//                $this->aFrom[] = $arg;
//            }
//        }

        return $this;
    }

    /**
     * Get "From" adresses.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getFrom() {
        return $this->aFrom;
    }

    /**
     * Set "To" adresses.
     *
     * @access   public
     * @return   Mail
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setTo() {
        if(func_num_args() == 0) {
            throw new Exception\Fatal('No receivers set to this Mail.');
        }

        $this->aTo = [];

        foreach(func_get_args() as $arg) {
            if(is_array($arg)) {
                foreach($arg as $key => $value) {
                    if(!is_int($key)) {
                        $this->aTo[] = $value.' <'.$key.'>';
                    } else {
                        $this->aTo[] = $value;
                    }
                }
            } else {
                $this->aTo[] = $arg;
            }
        }

        return $this;
    }

    /**
     * Get "To" adresses.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getTo() {
        return $this->aTo;
    }

    /**
     * Set "CC" adresses.
     *
     * @access   public
     * @return   Mail
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setCC() {
        if(func_num_args() == 0) {
            return $this;
        }
        $this->aCC = [];
        foreach(func_get_args() as $arg) {
            if(is_array($arg)) {
                foreach($arg as $key => $value) {
                    if(!is_int($key)) {
                        $this->aCC[] = $value.' <'.$key.'>';
                    } else {
                        $this->aCC[] = $value;
                    }
                }
            } else {
                $this->aCC[] = $arg;
            }
        }

        return $this;
    }

    /**
     * Get "CC" adresses.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getCC() {
        return $this->aCC;
    }

    /**
     * Set "Bcc" adresses
     *
     * @access   public
     * @param    array
     * @return   Mail
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setBcc() {
        if(func_num_args() == 0) {
            return $this;
        }
        $this->aBCC = [];
        foreach(func_get_args() as $arg) {
            if(is_array($arg)) {
                foreach($arg as $key => $value) {
                    if(!is_int($key)) {
                        $this->aBCC[] = $value.' <'.$key.'>';
                    } else {
                        $this->aBCC[] = $value;
                    }
                }
            } else {
                $this->aBCC[] = $arg;
            }
        }

        return $this;
    }

    /**
     * Get "Bcc" adresses
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getBcc() {
        return $this->aBCC;
    }

    /**
     * Set body
     *
     * Set body with content type
     *
     * @access   public
     * @param    string $sBody
     * @param    string $sContentType
     * @return   Mail
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setBody($sBody, $sContentType = 'text/html') {
        $this->sBody            = $sBody;
        $this->sBodyContentType = $sContentType;

        return $this;
    }

    /**
     * Get e-mail body.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getBody() {
        return $this->sBody;
    }

    /**
     * Get body content type.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getBodyContentType() {
        return $this->sBodyContentType;
    }

    /**
     * Set alternative body with content type.
     *
     * @access   public
     * @param    string $sBody
     * @param    string $sContentType
     * @return   Mail
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setAlternativeBody($sBody, $sContentType = 'text/plain') {
        $this->sAlternativeBody            = $sBody;
        $this->sAlternativeBodyContentType = $sContentType;

        return $this;
    }

    /**
     * Get alternative body.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getAlternativeBody() {
        return $this->sAlternativeBody;
    }

    /**
     * Get alternative body content type.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getAlternativeBodyContentType() {
        return $this->sAlternativeBodyContentType;
    }

    /**
     * Add attachment to mail.
     *
     * @access   public
     * @param    Mail\Attachment $oAttachment
     * @return   Mail
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function addAttachment(Mail\Attachment $oAttachment) {
        $this->aAttachments[] = $oAttachment;

        return $this;
    }

    /**
     * Get array of attachments
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getAttachments() {
        return $this->aAttachments;
    }

    /**
     * Check if Mail is complete (checking subject, body and body content type).
     *
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function isComplete() {
        if(empty($this->sSubject) || empty($this->sBody) || empty($this->sBodyContentType) || empty($this->aFrom) || empty($this->aTo)) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Get mail headers.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getMessageHeaders() {
        $sHeader = '';

        $sHeader .= "MIME-Version: 1.0\r\n";
        $sHeader .= "Subject: ".$this->getSubject()."\r\n";
        $sHeader .= "From: ".implode(',', $this->getFrom())."\r\n";
        $sHeader .= "To: ".implode(',', $this->getTo())."\r\n";

        if(!empty($this->getCc)) {
            $sHeader .= "Cc: ".implode(',', $this->getCC())."\r\n";
        }

        if(!empty($this->getBcc)) {
            $sHeader .= "Bcc: ".implode(',', $this->getBcc())."\r\n";
        }

        if(($this->getAlternativeBody() == (FALSE || NULL)) && count($this->getAttachments()) == 0) {
            $sHeader .= "Content-Type: ".$this->getBodyContentType()."; charset=UTF-8\r\n";
        } elseif(count($this->getAttachments()) == 0) {
            $sHeader .= "Content-Type: multipart/alternative; boundary=alt-".$this->boundary."\r\n";
        } else {
            $sHeader .= "Content-Type: multipart/related; boundary=mix-".$this->boundary."\r\n";
        }

        $sHeader .= "\r\n";

        return $sHeader;
    }

    /**
     * Get body of the e-mail.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getMessageBody() {
        if($this->getAlternativeBody() == (FALSE || NULL) && count($this->getAttachments()) == 0) {
            $sBody = $this->getBody();
        } elseif(count($this->getAttachments()) == 0) {
            $sBody = "";

            $sBody .= "--alt-".$this->boundary."\r\n";
            $sBody .= "Content-Type: ".$this->getAlternativeBodyContentType()."; charset=UTF-8\r\n\r\n";
            $sBody .= $this->getAlternativeBody()."\r\n";

            $sBody .= "--alt-".$this->boundary."\r\n";
            $sBody .= "Content-Type: ".$this->getBodyContentType()."; charset=UTF-8\r\n\r\n";
            $sBody .= $this->getBody()."\r\n";

            $sBody .= "--alt-".$this->boundary."--\r\n";
        } elseif($this->getAlternativeBody() == (FALSE || NULL)) {
            $sBody = "";

            $sBody .= "--mix-".$this->boundary."\r\n";
            $sBody .= "Content-Type: ".$this->getBodyContentType()."; charset=UTF-8\r\n\r\n";
            $sBody .= $this->getBody()."\r\n\r\n";

            $sBody .= "--mix-".$this->boundary;

            foreach($this->getAttachments() as $id => $oAttachment) {
                /* @var $oAttachment Mail\Attachment */
                $sBody .= "\r\n";
                $sBody .= 'Content-Type: '.$oAttachment->getContentType().'; name='.$oAttachment->getFilename()."\r\n";
                $sBody .= "Content-Transfer-Encoding: base64\r\n";
                $sBody .= "Content-ID: <".$oAttachment->getId().">\r\n";
                $sBody .= "X-Attachment-Id: ".$oAttachment->getId()."\r\n\r\n";
                $sBody .= $oAttachment->getBodyBase64()."\r\n";
                $sBody .= "--mix-".$this->boundary;
            }

            $sBody .= "--\r\n";
        } else {
            $sBody = "";

            $sBody .= "--mix-".$this->boundary."\r\n";
            $sBody .= "Content-Type: multipart/alternative; boundary=alt-".$this->boundary."\r\n\r\n";

            $sBody .= "--alt-".$this->boundary."\r\n";
            $sBody .= "Content-Type: ".$this->getAlternativeBodyContentType()."; charset=UTF-8\r\n\r\n";
            $sBody .= $this->getAlternativeBody()."\r\n\r\n";

            $sBody .= "--alt-".$this->boundary."\r\n";
            $sBody .= "Content-Type: ".$this->getBodyContentType()."; charset=UTF-8\r\n\r\n";
            $sBody .= $this->getBody()."\r\n\r\n";

            $sBody .= "--alt-".$this->boundary."--\r\n";

            $sBody .= "--mix-".$this->boundary;

            foreach($this->getAttachments() as $id => $oAttachment) {
                /* @var $oAttachment Mail\Attachment */
                $sBody .= "\r\n";
                $sBody .= "Content-Type: image/jpeg; name=".$oAttachment->getFilename()."\r\n";
                $sBody .= "Content-Transfer-Encoding: base64\r\n";
                $sBody .= "Content-ID: <".$oAttachment->getId().">\r\n";
                $sBody .= "X-Attachment-Id: ".$oAttachment->getId()."\r\n\r\n";
                $sBody .= $oAttachment->getBodyBase64()."\r\n";
                $sBody .= "--mix-".$this->boundary;
            }

            $sBody .= "--\r\n";
        }

        return $sBody;
    }

}
