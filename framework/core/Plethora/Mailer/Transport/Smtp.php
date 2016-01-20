<?php

namespace Plethora\Mailer\Transport;

use Plethora\Config;
use Plethora\Exception;
use Plethora\Log;
use Plethora\Mail;
use Plethora\Mailer as Mailer;

/**
 * SMTP Mailer transport
 *
 * @package        Plethora
 * @subpackage     Form\Separator
 * @author         Zalazdi
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Smtp extends Mailer\Transport {

    /**
     * Connect resource
     *
     * @access  private
     * @var     resource
     * @since   1.0.0-alpha
     */
    private $connection;

    /**
     * Is connected?
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $isConnected = FALSE;

    /**
     * Server hostname
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $hostname; // Default localhost

    /**
     * Server port
     *
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private $port; // Default 25

    /**
     * Need authorization?
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $useAuth; // Defalut FALSE

    /**
     * Username
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $username; // Default NULL

    /**
     * Password
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $password; // Default NULL

    /**
     * Timeout
     *
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private $timeout; // Default 5

    /**
     * Crypto
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $crypto; // Default NULL

    /**
     * New line delimiter
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $newline; // Default \r\n

    /**
     * Constructor.
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */

    public function __construct() {
        $this->hostname = Config::get('mailer.hostname', 'localhost');
        $this->port     = Config::get('mailer.port', 25);
        $this->useAuth  = Config::get('mailer.auth', FALSE);
        $this->username = Config::get('mailer.username');
        $this->password = Config::get('mailer.password');
        $this->timeout  = Config::get('mailer.timeout', 5);
        $this->crypto   = Config::get('mailer.crypto', '');
        $this->newline  = Config::get('mailer.newline', "\r\n");

        Log::insert('SMTP Transport class initialized!');
    }

    /**
     * Destructor.
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __destruct() {
        if($this->isConnected()) {
            $this->disconnect();
        }
    }

    /**
     * Connect with server
     *
     * @access   private
     * @return   bool
     * @throws   Exception
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function connect() {
        $errno            = NULL;
        $errstr           = NULL;
        $sSSL             = NULL;
        $this->connection = fsockopen($sSSL.$this->hostname, $this->port, $errno, $errstr, $this->timeout);

        if($this->crypto == 'ssl') {
            $sSSL = 'ssl://';
        } elseif($this->crypto == 'tls') {
            $sSSL = 'tcp://';
        }

        if(!is_resource($this->connection)) {
            throw new Exception('Connection problem');
        }

        $this->getData();

        if($this->crypto == 'tls') {
            $this->sendCommand('hello');
            $this->sendCommand('starttls');

            $bCrypto = stream_socket_enable_crypto($this->connection, TRUE, STREAM_CRYPTO_METHOD_SSLv23_CLIENT);

            if($bCrypto !== TRUE) {
                throw new Exception('Problem with crypto');
            }
        }

        $this->isConnected = TRUE;

        return $this->sendCommand('hello');
    }

    /**
     * Disconnect
     *
     * @access   private
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function disconnect() {
        $this->sendCommand('quit');

        fclose($this->connection);

        $this->isConnected = FALSE;
    }

    /**
     * Authenticate with server
     *
     * @access   private
     * @return   bool
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function authenticate() {
        if(!$this->useAuth) {
            return TRUE;
        }

        if($this->username == '' AND $this->password == '') {
            return FALSE;
        }

        // AUTH LOGIN
        $this->sendData('AUTH LOGIN');
        $sReply1 = $this->getData();

        if(strncmp($sReply1, '334', 3) !== 0) {
            throw new Exception('Failed SMTP login');
        }

        // LOGIN
        $this->sendData(base64_encode($this->username));
        $sReply2 = $this->getData();

        if(strncmp($sReply2, '334', 3) !== 0) {
            throw new Exception('Failed login auth');
        }

        // PASS
        $this->sendData(base64_encode($this->password));
        $sReply3 = $this->getData();

        if(strncmp($sReply3, '235', 3) !== 0) {
            throw new Exception('Failed password auth');
        }

        return TRUE;
    }

    /**
     * Send command to server.
     *
     * @access   private
     * @param    string $sCmd
     * @param    string $sData
     * @return   bool
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function sendCommand($sCmd, $sData = NULL) {
        $iResp = NULL;
        switch($sCmd) {
            case 'hello':
                if($this->useAuth) {
                    $this->sendData('EHLO '.$this->hostname);
                } else {
                    $this->sendData('HELO '.$this->hostname);
                }

                $iResp = 250;
                break;

            case 'starttls':
                $this->sendData('STARTTLS');
                $iResp = 220;
                break;

            case 'from':
                $this->sendData('MAIL FROM:<'.$sData.'>');
                $iResp = 250;
                break;

            case 'to':
                $this->sendData('RCPT TO:<'.$sData.'>');
                $iResp = 250;
                break;

            case 'data':
                $this->sendData('DATA');
                $iResp = 354;
                break;

            case 'quit':
                $this->sendData('QUIT');
                $iResp = 221;
                break;
        }

        $sResponse = $this->getData();

        if(substr($sResponse, 0, 3) != $iResp) {
            throw new Exception\Fatal('Bad response ('.$sResponse.')');
        }

        return TRUE;
    }

    /**
     * Send data to server.
     *
     * @access   private
     * @param    string $sData
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function sendData($sData) {
        if(!fwrite($this->connection, $sData.$this->newline)) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Get server response.
     *
     * @access   private
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function getData() {
        $sData = NULL;

        while($sStr = fgets($this->connection, 1024)) {
            $sData .= $sStr;

            if($sStr[3] == " ") {
                break;
            }
        }

        return $sData;
    }

    /**
     * Is connected with server?
     *
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function isConnected() {
        return $this->isConnected;
    }

    /**
     * Send new message.
     *
     * @access   pubic
     * @param    Mail $oMessage
     * @return   bool
     * @throws   Exception
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function send(Mail $oMessage) {
        if(!$this->isConnected) {
            $this->connect();
            $this->authenticate();
        }

        foreach($oMessage->getFrom() as $sVal) {
            $this->sendCommand('from', $sVal);
        }

        foreach($oMessage->getTo() as $sVal) {
            $this->sendCommand('to', $sVal);
        }

        if(count($oMessage->getCC()) > 0) {
            foreach($oMessage->getCC() as $sVal) {
                $this->sendCommand('to', $sVal);
            }
        }

        if(count($oMessage->getBcc()) > 0) {
            foreach($oMessage->getBcc() as $sVal) {
                $this->sendCommand('to', $sVal);
            }
        }

        $this->sendCommand('data');
        $this->sendData($oMessage->getMessageHeaders().preg_replace('/^\./m', '..$1', $oMessage->getMessageBody()));
        $this->sendData('.');

        $sReply = $this->getData();

        if(strncmp($sReply, '250', 3) !== 0) {
            throw new Exception('Error with sending body');
        }

        return TRUE;
    }

}
