<?php

namespace Plethora\ModelCore\Traits;

use Plethora\Exception;
use Plethora\ModelCore;

/**
 * This trait is used to add sortable functionality to all Models.
 *
 * @package        Plethora
 * @subpackage     Model\Traits
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
trait Sortable {
    /**
     * Order number on the list.
     *
     * @Column(type="smallint", nullable=FALSE, name="order_number")
     *
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $order_number = 0;

    /**
     * If this item has a parent in order tree.
     *
     * @Column(type="smallint", nullable=FALSE, name="order_parent")
     *
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $order_parent = 0;

    /**
     * @access   public
     * @return   integer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getOrderNumber() {
        return $this->order_number;
    }

    /**
     * Set order number in the sorted list for this particular entity.
     *
     * @access   public
     * @param    $number
     * @return   $this
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setOrderNumber($number) {
        if(!is_integer($number) || $number < 0) {
            throw new Exception\Fatal('An argument of this function must be an integer!');
        }

        $this->order_number = $number;

        return $this;
    }

    /**
     * Get number of level located above current level.
     *
     * @access   public
     * @return   integer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getOrderParent() {
        return $this->order_parent;
    }

    /**
     * Set order parent in the sorted list for this particular entity.
     *
     * @access   public
     * @param    integer $parent
     * @return   $this
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setOrderParent($parent) {
        if(!is_integer($parent) || $parent < 0) {
            throw new Exception\Fatal('An argument of this function must be an integer!');
        }

        $this->order_parent = $parent;

        return $this;
    }

    /**
     * Return list of model entities in the structure of a tree.
     *
     * @static
     * @access   public
     * @param    array $aItems
     * @return   array
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function tree(array $aItems)
    {
        // check if items has proper properties
        $oObject = reset($aItems);

        if(!$oObject instanceof ModelCore || !property_exists($oObject, 'order_number') || !property_exists($oObject, 'order_parent')) {
            throw new Exception\Fatal('You cannot generate items tree from an item, which don\'t have "order_number" or "order_parent" attributes.');
        }

        usort($aItems, function ($a, $b) {
            /* @var $a \Plethora\ModelCore\Traits\Sortable */
            /* @var $b \Plethora\ModelCore\Traits\Sortable */
            return strcmp($a->getOrderNumber(), $b->getOrderNumber());
        });

        // create list
        $aList = [];

        foreach($aItems as $oObject) {
            /* @var $oObject \Plethora\ModelCore\Traits\Sortable */
            if(!isset($aList[$oObject->getOrderParent()])) {
                $aList[$oObject->getOrderParent()] = [];
            }

            $aList[$oObject->getOrderParent()][] = $oObject;
        }

        // sort items
        $sortList = function ($aList, $iIndex) use (&$sortList) {
            $aOutput = [];

            if(isset($aList[$iIndex]) && is_array($aList[$iIndex])) {
                foreach($aList[$iIndex] as $oObject) {
                    $aOutput[] = [
                        'object'   => $oObject,
                        'siblings' => $sortList($aList, $oObject->id),
                    ];
                }
            }

            return $aOutput;
        };

        // return sorted tree-list
        return $sortList($aList, 0);
    }

    /**
     * Take initial data from particular model, and return them as tree-structured
     * items list.
     *
     * @access   public
     * @param    string $sField
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function treeByField($sField)
    {
        return static::tree($this->{$sField});
    }
}