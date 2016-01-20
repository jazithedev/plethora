<?php

namespace Plethora\Helper;

use Plethora\DB;
use Plethora\Form;
use Plethora\Helper;
use Plethora\Helper\SearchEngine\SearchEngineGeneratedQueries;
use Plethora\ModelCore;
use Plethora\Router;
use Plethora\View;

/**
 * Search engine helper
 *
 * @package        Plethora
 * @subpackage     Form\Separator
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class SearchEngine extends Helper {

    /**
     * @access    protected
     * @var       Form
     * @since     1.0.0-alpha
     */
    private $oForm = NULL;

    /**
     * @access    private
     * @var       ModelCore
     * @since     1.0.0-alpha
     */
    private $oModel = NULL;

    /**
     * This variable is used for storing informations about other than primary tables (related with this one) fields.
     * It stores:
     * - original name of field;
     * - name of field which exists in the form;
     * - related table class name;
     *
     * @access    private
     * @var       array
     * @since     1.0.0-alpha
     */
    private $aRelatedFieldsList = [];

    /**
     * @access    protected
     * @var       string
     * @since     1.0.0-alpha
     */
    private $sFormView = 'base/form/searchengine/form';

    /**
     * @access    protected
     * @var       string
     * @since     1.0.0-alpha
     */
    private $sFormFieldView = 'base/form/searchengine/field';

    /**
     * List of banned form field types for search engine usage.
     *
     * @static
     * @access    private
     * @var       array
     * @since     1.0.0-alpha
     */
    private static $aBannedFieldTypes = ['hidden', 'file', 'image', 'password', 'filemodel', 'imagemodel'];

    /**
     * Contructor. The search engine form is generated in here.
     *
     * @access    public
     * @param     ModelCore $oModel
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function __construct(ModelCore $oModel) {
        $this->oModel = $oModel;
        $sModelClass  = $oModel->getClass();
        $aQueryParams = Router::getQueryStringParams();
        $aDefaults    = [];

        $sFormName = 'search_engine_for_'.str_replace('\\', '_', strtolower($sModelClass));

        if(count($aQueryParams) > 0) {
            foreach($aQueryParams as $sKey => $sValue) {
                $aDefaults[$sKey] = ['und' => [0 => $sValue]];
            }
        }

        $oForm = Form::factory($sFormName, $aDefaults)
            ->setView($this->getFormView())
            ->setFieldsNameWithPrefix(FALSE)
            ->removeCsrfToken();

        $this->setForm($oForm);
    }

    /**
     * Get list of banned form field types for search engine usage.
     *
     * @access     public
     * @return     array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function getBannedFieldTypes() {
        return static::$aBannedFieldTypes;
    }

    /**
     * @access     public
     * @param      string $sOriginalName
     * @param      string $sFormFieldName
     * @param      string $sRelatedVariableFromPrimary
     * @param      string $sModelClassName
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addRelFieldInfo($sOriginalName, $sFormFieldName, $sRelatedVariableFromPrimary, $sModelClassName) {
        $this->aRelatedFieldsList[$sFormFieldName] = SearchEngine\RelFieldInfo::factory($sOriginalName, $sRelatedVariableFromPrimary, $sModelClassName);
    }

    /**
     * @access     private
     * @param      string $sField
     * @return     SearchEngine\RelFieldInfo|FALSE
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    private function getRelFieldInfo($sField = NULL) {
        return ($sField === NULL) ? $this->aRelatedFieldsList : Arrays::get($this->aRelatedFieldsList, $sField, FALSE);
    }

    /**
     * @access     public
     * @return     Form
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getForm() {
        return $this->oForm;
    }

    /**
     * @access     private
     * @param      Form $oForm
     * @return     $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    private function setForm(Form $oForm) {
        $this->oForm = $oForm;

        return $this;
    }

    /**
     * @access     public
     * @return    ModelCore
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getModel() {
        return $this->oModel;
    }

    /**
     * @access    protected
     * @return    string
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function getFormView() {
        return $this->sFormView;
    }

    /**
     * @access    protected
     * @return    string
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function getFormFieldView() {
        return $this->sFormFieldView;
    }

    /**
     * Add new field to search engine.
     *
     * @access   public
     * @param    string $sName
     * @param    string $sType
     * @return   Form\Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function addField($sName, $sType) {
        // create form field for search engine
        $oField = $this->getForm()
            ->add($sName, $sType)
            ->setViewBase($this->getFormFieldView());
        
        /* @var $oField Form\Field */

        // default path to view for search engine field
        $sSearchEngineViewPath = 'base/form/searchengine/field/'.$sType;

        // checking if for particular field type, searchengine view exists
        if(View::viewExists($sSearchEngineViewPath)) {
            $oField->setView($sSearchEngineViewPath);
        }

        // return
        return $oField;
    }

    /**
     * Generate query of particular entity list and search engine.
     *
     * @access   public
     * @return   SearchEngineGeneratedQueries
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function generateQuery() {
        $oQuery = DB::queryBuilder()
            ->select('t')
            ->from($this->getModel()->getClass(), 't');

        $aJoined = [];
        $iAlias  = 0;
        $oForm   = $this->getForm();

        // if search engine form is submitted
        if($oForm->isSubmitted()) {
            $aQueryParams        = [];
            $aIgnoredQueryParams = [];

            foreach(array_keys($oForm->getFields()) as $sFieldName) {
                /* @var $oField Form\Field */
                $mValue = $oForm->get($sFieldName);

                if($mValue !== '') {
                    $aQueryParams[$sFieldName] = $mValue;
                } else {
                    $aIgnoredQueryParams[] = $sFieldName;
                }
            }

            $sURL = Router::currentUrlWithQueryParams($aQueryParams, $aIgnoredQueryParams);

            Router::relocate($sURL);
        }

        // if URL has any filters
        $aQueryParamsForSearch = Router::getQueryStringParams();

        if(count($aQueryParamsForSearch) > 0) {
            foreach($aQueryParamsForSearch as $sFieldName => $mValue) {
                // if value is not empty
                if(!is_array($mValue) && $mValue !== '' && $mValue !== NULL || is_array($mValue) && $mValue !== []) {
                    // changing models for theirs IDs
                    if(is_array($mValue)) {
                        foreach($mValue as &$oValue) {
                            /* @var $oValue \Plethora\ModelCore */
                            if($oValue instanceof ModelCore) {
                                $oValue = $oValue->getId();
                            }
                        }
                    } elseif($mValue instanceof ModelCore) {
                        $mValue = $mValue->getId();
                    }

                    // if field is from primary table
                    if($this->getModel()->getMetadata()->hasField($sFieldName)) {
                        $oQuery->andWhere("t.".$sFieldName." LIKE '%".trim($mValue)."%'");
                    } elseif($this->getModel()->getMetadata()->hasAssociation($sFieldName)) {
                        $sAssocTableAlias = 'a'.$sFieldName;

                        if(is_array($mValue)) {
                            $aConditions = [];

                            foreach($mValue as $mSingleValue) {
                                $aConditions[] = $sAssocTableAlias.".id ='".trim($mSingleValue)."'";
                            }

                            $sCondition = implode(' OR ', $aConditions);
                        } else {
                            $sCondition = $sAssocTableAlias.".id ='".trim($mValue)."'";
                        }

                        $oQuery->join('t.'.$sFieldName, $sAssocTableAlias, \Doctrine\ORM\Query\Expr\Join::WITH, $sCondition);
                    } // if field is from related table
                    else {
                        $aRelFieldInfo = $this->getRelFieldInfo($sFieldName);

                        if($aRelFieldInfo !== FALSE) {
                            if(!in_array($aRelFieldInfo->getVar(), $aJoined)) {
                                $iAlias++;
                                $sAlias = 't'.$iAlias;

                                $aJoined[$sAlias] = $aRelFieldInfo->getVar();

                                $oQuery->join('t.'.$aRelFieldInfo->getVar(), $sAlias);
                            } else {
                                $sAlias = array_search($aRelFieldInfo->getVar(), $aJoined);
                            }

                            $oQuery->andWhere($sAlias.".".$aRelFieldInfo->getOriginalName()." LIKE '%".trim($mValue)."%'");
                        }
                    }
                }
            }
        }

        $oQuery->orderBy('t.id', 'desc');

        return SearchEngineGeneratedQueries::factory($oQuery);
    }

    /**
     * Render search engine.
     *
     * @access     public
     * @return     string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function render() {
        $oForm = $this->getForm();

        foreach($oForm->getFields() as $oField) {
            /* @var $oField Form\Field */
            $oField
                ->setTip('')
                ->setRequiredNot();
        }

        return $oForm->render();
    }
}
