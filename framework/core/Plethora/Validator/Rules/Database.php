<?php

namespace Plethora\Validator\Rules;

use Plethora\DB;

/**
 * Number validation methods
 *
 * @package        Plethora
 * @subpackage     Validator\Rules
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Database {

    /**
     * Checks if value is unique
     *
     * @static
     * @access     public
     * @param      string $mValue
     * @param      string $sDontCheckThisID
     * @param      string $sTableClass
     * @param      string $sColumn
     * @param      string $sCommuniquePattern
     * @param      string $sAdditionalWhere
     * @return     boolean|string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function unique($mValue, $sDontCheckThisID, $sTableClass, $sColumn, $sCommuniquePattern = NULL, $sAdditionalWhere = '') {
        // array
        if(is_array($mValue)) {
            $mParam = [];

            foreach($mValue as $v) {
                if($v != "" && !array_search($v, $mParam)) {
                    $mParam[] = $v;
                }
            }

            if(count($mParam) == 0) {
                return TRUE;
            }

            $sOp = "IN (:param)";
            // single value
        } else {
            if($mValue == "") {
                return TRUE;
            }

            $mParam = $mValue;
            $sOp    = "= :param";
        }

        # build query
        $queryBuilder = DB::getEntityManager()->createQueryBuilder();

        $query = $queryBuilder->select('t.id');
        $query->from($sTableClass, 't');
        $query->where("t.".$sColumn." ".$sOp);
        $query->setParameter('param', $mParam);

        if($sDontCheckThisID !== NULL) {
            $query->andWhere('t.id <> :selfid');
            $query->setParameter('selfid', $sDontCheckThisID);
        }

        if($sAdditionalWhere !== '') {
            $query->andWhere($sAdditionalWhere);
        }

        $queryResult = $query->getQuery()->execute();

        // Checking result(s)
        if(count($queryResult) > 0) {
            if(empty($sCommuniquePattern)) {
                if(is_array($mValue)) {
                    return __('One of the given values already exists in the database.');
                } else {
                    return __('This value already exists in the database.');
                }
            } else {
                return __($sCommuniquePattern);
            }
        }

        return TRUE;
    }

    /**
     * Checks if value exists in database
     *
     * @static
     * @access     public
     * @param      string $mValue
     * @param      string $sTableClass
     * @param      string $sColumn
     * @return     boolean|string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function dbKeyValidation($mValue, $sTableClass, $sColumn) {
        # array
        if(is_array($mValue)) {
            if(empty($mValue)) {
                return TRUE;
            }

            $mParam = [];

            foreach($mValue as $v) {
                if($v != "" && !array_search($v, $mParam)) {
                    $mParam[] = $v;
                }
            }

            $iDataAmount = count($mParam);

            if($iDataAmount == 0) {
                return TRUE;
            }
        } # single value
        else {
            if($mValue == "") {
                return TRUE;
            }

            $iDataAmount = 1;
            $mParam      = $mValue;
        }

        # Query
        DB::query("SELECT t.id FROM ".$sTableClass." t WHERE t.".$sColumn." IN (:param)")
            ->param('param', $mParam)
            ->execute();

        # Checking result(s)
        if(count(DB::result()) != $iDataAmount) {
            if(is_array($mValue)) {
                return __('One of the values is incompatible with data from database.');
            } else {
                return __('Value is incompatible with data from database.');
            }
        }

        return TRUE;
    }

}