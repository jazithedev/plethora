<?php

namespace Plethora\Validator\RulesSetBuilder;

use Plethora\Validator\RulesSetBuilder;

/**
 * @package        Plethora
 * @subpackage     Validator\RulesSetBuilder
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Database extends RulesSetBuilder {
    /**
     * Static factory method.
     *
     * @static
     * @access     public
     * @return     Database
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function factory() {
        return new Database();
    }

    /**
     * Checks if value is unique.
     *
     * @access     public
     * @param      string $mValue
     * @param      string $sTableClass
     * @param      string $sColumn
     * @param      string $sCommuniquePattern
     * @param      string $sAdditionalWhere
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function unique($mValue, $sTableClass, $sColumn, $sCommuniquePattern = NULL, $sAdditionalWhere = '') {
        $this->addRule(['\Plethora\Validator\Rules\Database::unique', [$mValue, $sTableClass, $sColumn, $sCommuniquePattern, $sAdditionalWhere]]);

        return $this;
    }

    /**
     * Checks if value exists in database.
     *
     * @access     public
     * @param      string $mValue
     * @param      string $sTableClass
     * @param      string $sColumn
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function dbKeyValidation($mValue, $sTableClass, $sColumn) {
        $this->addRule(['\Plethora\Validator\Rules\Database::dbKeyValidation', [$mValue, $sTableClass, $sColumn]]);

        return $this;
    }
}