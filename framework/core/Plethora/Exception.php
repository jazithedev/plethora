<?php

namespace Plethora;

/**
 * @package    Plethora
 * @author     Krzysztof Trzos
 * @since      1.0.0-alpha
 * @version    1.0.0-alpha
 */
class Exception extends \Exception
{
    /**
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sHeaderContent = '500 Internal Server Error';

    /**
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $iHttpCode = 500;

    /**
     * @access   public
     * @param    string     $message
     * @param    integer    $code
     * @param    \Exception $previous
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct($message = '', $code = 0, \Exception $previous = NULL)
    {
        Log::insert($message, Log::ERROR);
        parent::__construct($message, $code, $previous);
    }

    /**
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function handler()
    {
        header('HTTP/1.0 '.$this->sHeaderContent);

        $oController = new Controller();
        $oView       = View::factory('base/error_pages/'.$this->iHttpCode);

        echo $oController->independentResponse($oView);

        exit;
    }
}