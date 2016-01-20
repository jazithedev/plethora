<?php

namespace Controller\Ajax;

use Controller\Ajax;
use Plethora\DB;
use Plethora\ModelCore;
use Plethora\Route;
use Plethora\Theme;

/**
 * Parent controller for AJAX requests.
 *
 * @author         Krzysztof Trzos
 * @package        Plethora
 * @subpackage     Controller\Ajax
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Backend extends Ajax {

    /**
     * Constructor.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function __construct() {
        parent::__construct();

        Theme::initBackend();
    }

    /**
     * Action used to do multileveled sort on model entities.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function actionSortList() {
        // check access
        if(!\UserPermissions::hasPerm('backend_ajax_sort_list')) {
            Route::factory('home')
                ->redirectTo();
        }

        // @TODO: check permissions
        $sObjects     = filter_input(INPUT_POST, 'objects');
        $sModel       = filter_input(INPUT_POST, 'model');
        $aObjectsTmp  = [];
        $aOrderNumber = [];

        // if list of objects is empty
        if(empty($sObjects)) {
            $this->setStatus('error');

            return __('List of objects is empty.');
        }

        // parse objects array from query string
        parse_str($sObjects, $aObjectsTmp);

        $aObjects = $aObjectsTmp['object'];

        // rewrite each object
        foreach($aObjects as $iID => $sParentID) {
            if($sParentID === 'null') {
                $sParentID = 0;
            }

            $iParentID = (int)$sParentID;

            if(!isset($aOrderNumber[$iParentID])) {
                $aOrderNumber[$iParentID] = 0;
            }

            $aObjects[$iID] = [
                'order_parent' => $iParentID,
                'order'        => $aOrderNumber[$iParentID],
            ];

            $aOrderNumber[$iParentID]++;
        }

        // check if particular model has `order` property
        if(!property_exists($sModel, 'order_number')) {
            $this->setStatus('error');

            return __('Wrong node type.');
        }

        // get all model instances
        $aEntities = DB::query('SELECT t FROM '.$sModel.' t WHERE t.id IN (:list)')
            ->param('list', array_keys($aObjects))
            ->execute();


        foreach($aEntities as $oEntity) {
            /* @var $oEntity ModelCore|ModelCore\Traits\Sortable */
            $aObjData = $aObjects[$oEntity->getId()];

            $oEntity->setOrderNumber($aObjData['order']);
            $oEntity->setOrderParent($aObjData['order_parent']);
            $oEntity->save();

            DB::flush();
        }

        return 'saved';
    }
}
