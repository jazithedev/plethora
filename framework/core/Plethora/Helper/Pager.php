<?php

namespace Plethora\Helper;

use Doctrine\ORM\QueryBuilder;
use Plethora\Helper;
use Plethora\Log;
use Plethora\View;

/**
 * Pager helper.
 *
 * @package          Plethora
 * @subpackage       Helper
 * @author           Krzysztof Trzos
 * @copyright    (c) 2016, Krzysztof Trzos
 * @since            1.0.0-alpha
 * @version          1.0.0-alpha
 */
class Pager extends Helper {

    /**
     * Actual page number.
     *
     * @access     private
     * @var        integer
     * @since      1.0.0-alpha
     */
    private $iPage = 1;

    /**
     * Total amount of rows.
     *
     * @access     private
     * @var        integer
     * @since      1.0.0-alpha
     */
    private $iAmount = 0;

    /**
     * Number of results per page.
     *
     * @access     private
     * @var        integer
     * @since      1.0.0-alpha
     */
    private $iResultsPerPage = 15;

    /**
     * Total amount of pages.
     *
     * @access     private
     * @var        integer
     * @since      1.0.0-alpha
     */
    private $iPagesAmount = 10;

    /**
     * Factory method.
     *
     * @static
     * @access     public
     * @param      QueryBuilder $oQuery
     * @param      string       $sTableAlias
     * @param      int          $iResultsPerPage
     * @return     Pager
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function factory(QueryBuilder &$oQuery, $sTableAlias, $iResultsPerPage = 15) {
        return new Pager($oQuery, $sTableAlias, $iResultsPerPage);
    }

    /**
     * Constructor.
     *
     * @access     public
     * @param      QueryBuilder $oQuery
     * @param      string       $sMainTableAlias
     * @param      integer      $iResultsPerPage
     * @throws     \Doctrine\ORM\NoResultException
     * @throws     \Doctrine\ORM\NonUniqueResultException
     * @internal   param string $sGetName
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function __construct(QueryBuilder &$oQuery, $sMainTableAlias, $iResultsPerPage = 15) {
        Log::insert('Pager helper initialized for query: "'.$oQuery->getDQL().'".');

        $iPageParamValue = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

        if(!empty($iPageParamValue)) {
            $this->iPage = $iPageParamValue;
        }

        $oQueryAmount = clone $oQuery;

        $aAmount = $oQueryAmount->select('COUNT('.$sMainTableAlias.')')
            ->getQuery()
            ->getSingleResult();

        $this->iAmount         = (int)$aAmount[1];
        $this->iResultsPerPage = (int)$iResultsPerPage;
        $iFirstResult          = $this->iResultsPerPage * ($this->iPage - 1);

        $oQuery
            ->setMaxResults($this->iResultsPerPage)
            ->setFirstResult($iFirstResult);
    }

    /**
     * Make all pager calculations.
     *
     * @access     public
     * @return     array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function generatePages() {
        if(!$this->iAmount) {
            return [];
        }

        $aArray = [];

        // checking max number of pages
        $iMaxNumbers = ceil($this->iAmount / $this->iResultsPerPage);

        // if amount of pages is 1, then return empty array (create no pages)
        if($iMaxNumbers == 1) {
            return [];
        }

        // checking current page
        $iPageParamValue = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $iCurrentPage    = empty($iPageParamValue) ? 1 : $iPageParamValue;

        // calculating first and last number of page in pager system
        if($iCurrentPage <= 6) {
            $iFirstNumber = 1;
            $iLimit       = $this->iPagesAmount + 1;
        } else {
            $iFirstNumber = $iCurrentPage - 5;
            $iLimit       = $this->iPagesAmount + $iFirstNumber;
        }

        if($iLimit > $iMaxNumbers) {
            $iFirstNumber = $iFirstNumber - $iLimit + $iMaxNumbers + 1;
            $iLimit       = $iMaxNumbers;
        }

        // adding 'previous' button / link
        if($iCurrentPage != 1) {
            $aArray[] = ['id' => ($iCurrentPage - 1), 'type' => 'previous'];
        }

        // adding pages numbers with links
        for($i = $iFirstNumber; $i <= $iLimit; $i++) {
            if($i >= 1) {
                if($iCurrentPage == $i) {
                    $aArray[] = ['id' => $i, 'type' => 'current'];
                } else {
                    $aArray[] = ['id' => $i, 'type' => 'middle'];
                }
            }
        }

        // adding 'next' button / link
        if($this->iAmount > $this->iResultsPerPage && ($iCurrentPage + 1) <= $iLimit) {
            $aArray[] = ['id' => $iCurrentPage + 1, 'type' => 'next'];
        }

        // returning content
        return $aArray;
    }

    /**
     * Return View of this pager.
     *
     * @access     public
     * @return     View
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getView() {
        return View::factory('base/list/pages')
            ->bind('oPager', $this);
    }

}
