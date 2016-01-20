<?php

namespace Plethora;

/**
 * Widget class, used to insert some smaller templates into main views
 *
 * @package        Plethora
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @version        1.0.0-alpha
 */
class Widget {

    /**
     * Path to basic View for all Widgets.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected static $sMainView = 'base/widget';

    /**
     * Basic View (container) for all Widgets.
     *
     * @access  protected
     * @var     View
     * @since   1.0.0-alpha
     */
    protected static $oMainView = NULL;

    /**
     * Execute custom widget action.
     *
     * @access   public
     * @param    View $view
     * @return   string
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function execute(View $view) {
        $aDbt    = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $sCaller = isset($aDbt[1]['function']) ? $aDbt[1]['function'] : NULL;

        if(static::$oMainView === NULL) {
            static::$oMainView = View::factory(static::$sMainView);
        }

        $sClass1 = strtolower(str_replace('\\', '_', get_called_class()));
        $sClass2 = $sClass1.'_'.strtolower(str_replace('action', '', $sCaller));

        return static::$oMainView
            ->set('sClasses', $sClass1.' '.$sClass2)
            ->set('oContent', $view)
            ->render();
    }

}
