<?php

namespace Plethora\Helper;

use Plethora\Exception;

/**
 * Helper class storing sets (groups) of anonymous functions in a purpose for their subsequent calls at the right
 * moment.
 *
 * @package        Plethora
 * @subpackage     Helper
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class FunctionsSets {

    /**
     * Array of anonymous functions sets.
     *
     * array(
     *     'set1' => [
     *         0 => [
     *                 'function' => \Closure
     *                 'arguments' => []
     *             ]
     *         1 => [
     *                 'function' => \Closure
     *                 'arguments' => []
     *             ]
     *     ],
     *     'set2' => [
     *         0 => [
     *                 'function' => \Closure
     *                 'arguments' => []
     *             ]
     *     ],
     *     ...
     * )
     *
     * @access  public
     * @var     array
     * @since   1.0.0-alpha
     */
    protected $aFunctionsSets = [];

    /**
     * Add new function to particular set.
     *
     * Function accepts infinite amount of arguments, when the first one is actions set, and the last one is instance
     * of \Closure (anonymous functions).
     *
     * @access   public
     * @param    mixed $mSet
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function add($mSet) {
        $iNumArgs = func_num_args();

        if($iNumArgs < 2) {
            throw new Exception\Fatal('Wrong number of arguments.');
        }

        $aArguments = func_get_args();
        $cFunction  = $aArguments[$iNumArgs - 1];

        if(!$cFunction instanceof \Closure) {
            throw new Exception\Fatal('Last argument is not an anonymous function!');
        }

        unset($aArguments[0], $aArguments[$iNumArgs - 1]);

        if(!isset($this->aFunctionsSets[$mSet])) {
            $this->aFunctionsSets[$mSet] = [];
        }

        $this->aFunctionsSets[$mSet][] = [
            'function'  => $cFunction,
            'arguments' => $aArguments,
        ];
    }

    /**
     * Extract all anonymous functions from particular set and call them.
     *
     * @access   public
     * @param    mixed $mSet
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function extract($mSet) {
        $aOutput = [];

        if(isset($this->aFunctionsSets[$mSet])) {
            foreach($this->aFunctionsSets[$mSet] as $aFunctionOnAction) {
                $cFunction  = $aFunctionOnAction['function'];
                $aArguments = $aFunctionOnAction['arguments'];

                $aOutput[] = call_user_func_array($cFunction, $aArguments);
            }
        }

        return $aOutput;
    }

}