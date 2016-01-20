<?php

namespace Controller;

use Plethora\Controller;
use Plethora\Log;
use Plethora\Response;
use Plethora\Router;
use Plethora\View;

/**
 * Parent controller for AJAX requests.
 *
 * @package        Plethora
 * @subpackage     Controller
 * @author         Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Ajax extends Controller {

    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR   = 'error';

    /**
     * Main array used to store output data.
     *
     * @access    protected
     * @var       string
     * @since     1.0.0-alpha
     */
    private $sStatus;

    /**
     * Main array used to store output data.
     *
     * @access    protected
     * @var       array
     * @since     1.0.0-alpha
     */
    private $bResponseAsJson = TRUE;

    /**
     * Constructor.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function __construct() {
        // call parent constructor
        parent::__construct();

        // set default response status
        $this->sStatus = static::STATUS_SUCCESS;

        // Create log about Controller initalization
        Log::insert('Ajax controller class initialized!');
    }

    /**
     * Set status of the final message of AJAX response.
     *
     * @access  public
     * @param   $sStatus
     * @return  Ajax
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function setStatus($sStatus) {
        $this->sStatus = $sStatus;

        return $this;
    }

    /**
     * Get request status.
     *
     * @access  public
     * @return string
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function getStatus() {
        return $this->sStatus;
    }

    /**
     * This method create output for response.
     *
     * @access     protected
     * @param      View $oContent
     * @return     Response
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function createResponse(View $oContent = NULL) {
        if($oContent === NULL) {
            $oContent = $this->{Router::getActionName()}();
            /* @var $oContent View */
            $this->afterAction();
        }

        // render page View
        if($oContent instanceof View) {
            $sContent = $oContent->render();
        } // if View is throwed on output of the action
        else {
            $sContent = $oContent;
        }

        if($this->bResponseAsJson) {
            $sResponse = json_encode(['status' => $this->sStatus, 'content' => $sContent]);

            die($sResponse);
        } else {
            // create response
            $oResponse = new Response();
            $oResponse->setContent($sContent);

            return $oResponse;
        }
    }

    /**
     * This method create output for response.
     *
     * @access     protected
     * @param      string $sContent
     * @return     Response
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function createResponseString($sContent) {
        if($this->bResponseAsJson) { // if response should be a JSON
            $sResponse = json_encode(['status' => $this->sStatus, 'content' => $sContent]);

            die($sResponse);
        } else { // if response is a simple string
            // create response
            $oResponse = new Response();
            $oResponse->setContent($sContent);

            return $oResponse;
        }
    }

}
