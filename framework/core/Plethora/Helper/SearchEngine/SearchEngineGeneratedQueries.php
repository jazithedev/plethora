<?php

namespace Plethora\Helper\SearchEngine;

use Doctrine\ORM;
use Plethora\DB;
use Plethora\Helper;

/**
 * @package        Plethora
 * @subpackage     Form\Separator
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class SearchEngineGeneratedQueries extends Helper {

    /**
     * @access  private
     * @var     ORM\QueryBuilder
     * @since   1.0.0-alpha
     */
    private $oCountingQuery = NULL;

    /**
     * @access  private
     * @var     ORM\QueryBuilder
     * @since   1.0.0-alpha
     */
    private $query = NULL;

    /**
     * @access    private
     * @var        integer
     * @since     1.0.0-alpha
     */
    private $iCount = 0;

    /**
     * Constructor.
     * 
     * @access   public
     * @param    ORM\QueryBuilder $query
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct(ORM\QueryBuilder $query) {
        $this->query          = $query;
        $this->oCountingQuery = clone $this->query;
        $this->oCountingQuery->select('COUNT(DISTINCT t.id)');
        $this->iCount = $this->getCountingQuery()->single();
    }

    /**
     * @access   public
     * @param    ORM\QueryBuilder $oQuery
     * @return   SearchEngineGeneratedQueries
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory(ORM\QueryBuilder $oQuery) {
        return new SearchEngineGeneratedQueries($oQuery);
    }

    /**
     * @access   public
     * @return   DB
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getQuery() {
        return DB::factory($this->query->getQuery());
    }

    /**
     * Get query builder.
     *
     * @access     public
     * @return    ORM\QueryBuilder
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getQueryBuilder() {
        return $this->query;
    }

    /**
     * @access     public
     * @return    DB
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    private function getCountingQuery() {
        return DB::factory($this->oCountingQuery->getQuery());
    }

    /**
     * @access     public
     * @return    integer
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getCount() {
        return intval($this->iCount[1]);
    }

}