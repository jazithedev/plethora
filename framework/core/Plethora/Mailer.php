<?php

namespace Plethora;

use DirectoryIterator;
use Plethora\Config;
use Plethora\Mail;

/**
 * Mailer
 *
 * @author         Zalazdi
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @package        Mail
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Mailer {

    /**
     * Transport object
     *
     * @access  private
     * @var     Mailer\Transport
     * @since   1.0.0-alpha
     */
    private $oTransport;

    /**
     * Mail queue.
     *
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private $iQueue;

    /**
     * Constructor
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct() {
        $this->iQueue = Config::get('mailer.queue', 5);

        # set transport instance
        $sTransportClass  = '\\Plethora\Mailer\Transport\\'.ucfirst(Config::get('mailer.transport'));
        $this->oTransport = new $sTransportClass;

        # prepare mailer cache path
        $filesPath = Config::get('mailer.cache_path', FALSE);

        if($filesPath === FALSE) {
            throw new Exception\Fatal('Mailer cache path has not been specified in config file.');
        }

        if(!file_exists($filesPath)) {
            mkdir($filesPath, 0755);
        }

        # log that mailer class has been initialized
        Log::insert('Mailer class initialized!');
    }

    /**
     * Factory method.
     *
     * @access   public
     * @return   Mailer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new Mailer;
    }

    /**
     * Send e-mail now.
     *
     * @access   public
     * @param    Mail $oMessage
     * @return   bool
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function send(Mail $oMessage) {
        if($oMessage->isComplete() == FALSE) {
            throw new Exception('Mail isn\'t complete');
        }

        return $this->oTransport->send($oMessage) ? TRUE : FALSE;
    }

    /**
     * Queue a mail
     *
     * Save mail in file and send it when Mailer::sendQueue will be called
     *
     * @access   public
     * @param    Mail $oMessage
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function queue(Mail $oMessage) {
        if($oMessage->isComplete() === FALSE) {
            return FALSE;
        }

        $filesPath      = Config::get('mailer.cache_path', FALSE);
        $serializedMail = serialize($oMessage);
        $fileName       = $filesPath.'mail_'.time().'_'.md5($serializedMail).'.txt';

        return (file_put_contents($fileName, $serializedMail)) ? TRUE : FALSE;
    }

    /**
     * Send queued mails.
     *
     * @access   public
     * @return   bool
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function sendQueue() {
        $filesPath = Config::get('mailer.cache_path', FALSE);

        for($i = 1; $i <= $this->iQueue; ++$i) {
            $rFile = NULL;

            foreach(new DirectoryIterator($filesPath) as $f) {
                if(!$f->isDot()) {
                    $rFile = $filesPath.$f->getFilename();
                    break;
                }
            }

            if($rFile == NULL) {
                break;
            }

            $oMessage = unserialize(file_get_contents($rFile));
            /* @var $oMessage Mail */

            if($oMessage instanceof Mail) {
                $this->send($oMessage);
            } else {
                throw new Exception('File isn\'t instance of Mail class');
            }

            if(!unlink($rFile)) {
                throw new Exception('File delete failure');
            }
        }

        return TRUE;
    }

}