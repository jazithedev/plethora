<?php

namespace Plethora;

use Doctrine;

/**
 * Main Model class.
 *
 * @copyright    (c) 2016, Krzysztof Trzos
 * @package          Plethora
 * @subpackage       Model
 * @since            1.0.0-alpha
 * @version          1.0.0-alpha
 */
class ModelCore
{

    /**
     * Main identifier of an entity.
     *
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $id;

    /**
     * Metdata object.
     *
     * @access  protected
     * @var     Doctrine\ORM\Mapping\ClassMetadata
     * @since   1.0.0-alpha
     */
    protected static $oMetadata;

    /**
     * Object with metadata of locales.
     *
     * @access  protected
     * @var     Doctrine\ORM\Mapping\ClassMetadata
     * @since   1.0.0-alpha
     */
    protected $oLocalesMetadata;

    /**
     * Var storing particular Model config.
     *
     * @static
     * @access  protected
     * @var     array
     * @since   1.0.0-alpha
     */
    protected static $aConfigs = NULL;

    /**
     * Fields list which cannot be shown in search engine.
     *
     * @static
     * @access  protected
     * @var     array
     * @since   1.0.0-alpha
     */
    protected static $notInSearchEngine = ['hidden'];

    /**
     * FunctionsSets helper instance.
     *
     * @access  protected
     * @var     Helper\FunctionsSets
     * @since   1.0.0-alpha
     */
    protected $oFunctionsSetsHelper = NULL;

    /**
     * Fields list which type must be changed for the purpose of search engine.
     *
     * @static
     * @access  protected
     * @var     array
     * @since   1.0.0-alpha
     */
    protected static $aChangeThoseTypes = [
        'textarea'         => 'text',
        'editor'           => 'text',
        'tinymce'          => 'text',
        'checkbox'         => 'select',
        'checkboxrelation' => 'select',
    ];

    /**
     * Locales variable.
     *
     * @access protected
     * @var    Doctrine\Common\Collections\ArrayCollection
     * @since  1.0.0-alpha
     */
    protected $locales;

    /**
     * Constructor.
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct()
    {
        if(static::hasLocales()) {
            $this->locales = new Doctrine\Common\Collections\ArrayCollection();
        }
    }

    /**
     * Get config class for this Model.
     *
     * @static
     * @access   public
     * @return   ModelCore\MConfig
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getConfig()
    {
        $sClassName = static::getClass();

        if(!isset(static::$aConfigs[$sClassName])) {
            static::$aConfigs[$sClassName] = static::generateConfig();
        }

        return static::$aConfigs[$sClassName];
    }

    /**
     * Get Model config for particular field.
     *
     * @static
     * @access   public
     * @param    string $sFieldName
     * @return   Form\Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getConfigForField($sFieldName)
    {
        if(static::getConfig()->hasField($sFieldName)) {
            return static::getConfig()->getField($sFieldName);
        } else {
            return FALSE;
        }
    }

    /**
     * Generate config object of particular Model.
     *
     * @static
     * @access   protected
     * @return   ModelCore\MConfig
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected static function generateConfig()
    {
        return new ModelCore\MConfig();
    }

    /**
     * Get config of search engine for particular model.
     *
     * @access   public
     * @return   ModelCore\ConfigSearchEngine
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getConfigSearchEngine()
    {
        return new ModelCore\ConfigSearchEngine();
    }

    /**
     * Method generating search engine object.
     *
     * @access   public
     * @param    array $fields
     * @return   Helper\SearchEngine
     * @throws   Exception\Fatal
     * @throws   Doctrine\ORM\Mapping\MappingException
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function generateSearchEngine(array $fields = [])
    {
        $searchEngine = new Helper\SearchEngine($this);
        $baseConfig   = static::getConfig();

        # search engine config
        foreach($this->getConfigSearchEngine()->getRelsFields() as $varName => $relFields) {
            if(property_exists($this, $varName)) {
                $fieldMapping = static::getMetadata()->getAssociationMapping($varName);
                $relClass     = Helper\Arrays::get($fieldMapping, 'targetEntity');
                $objConfig    = call_user_func($relClass.'::getConfig');
                /* @var $objConfig ModelCore\MConfig */

                foreach($relFields as $fieldName) {
                    try {
                        $fieldName = $baseConfig->hasField($fieldName) ? $varName.'_'.$fieldName : $fieldName;
                        $field     = $objConfig->getField($fieldName);

                        $baseConfig->addField($field);
                        $searchEngine->addRelFieldInfo($fieldName, $fieldName, $varName, $relClass);
                    } catch(Exception $e) {

                    }
                }
            } else {
                throw new Exception\Fatal('Zmienna "'.$varName.'" nie istnieje w klase "'.static::getClass().'".');
            }
        }

        # basic config
        foreach($baseConfig->getFields() as $fieldName => $field) {
            /* @var $field Form\Field */
            if((empty($fields) || in_array($fieldName, $fields)) && !in_array($field->getType(), Helper\SearchEngine::getBannedFieldTypes())) {
                if(!in_array($field->getType(), static::$notInSearchEngine)) {
                    if(isset(static::$aChangeThoseTypes[$field->getType()])) {
                        $field = $field->cloneToOtherType(static::$aChangeThoseTypes[$field->getType()]);
                    }

                    if($field->getQuantity() !== 1) {
                        $field->setQuantity(1);
                        $field->setQuantityMin(1);
                        $field->setQuantityMax(1);
                    }

                    $field->getAttributes()->removeAttribute('disabled');

                    if($field->isRequired()) {
                        $field->setRequiredNot();
                    }

                    $searchEngine->getForm()->addSingleton($field);
                }
            }
        }

        return $searchEngine;
    }

    /**
     * Get particular property of particular Model.
     *
     * @param    string $sName
     * @return   mixed
     * @throws   Exception\Model
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __get($sName)
    {
        $oMetadataLocales   = $this->getLocalesMetadata();
        $sField             = $this->getMetadata()->getFieldName($sName);
        $sLocalesField      = $oMetadataLocales !== FALSE ? $oMetadataLocales->getFieldName($sName) : NULL;
        $aFieldsFromLocales = static::hasLocales() ? $oMetadataLocales->getFieldNames() : [];

        // normal field
        if(property_exists($this, $sField)) {
            return $this->getValue($sField);
        } // locales field
        elseif($sLocalesField !== NULL && in_array($sLocalesField, $aFieldsFromLocales)) {
            return $this->getLocales()->getValue($sLocalesField);
        } // field do not exist in this model
        else {
            throw new Exception\Model(__('Cannot get value from ":name" property, because it does not exists in ":class" class.', ['name' => $sName, 'class' => $this->getClass()]));
        }
    }

    /**
     * Get particular property of particular Model.
     *
     * @access   public
     * @param    string $sName
     * @param    mixed  $mValue
     * @throws   Exception\Model
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __set($sName, $mValue)
    {
        $oMetaData          = $this->getMetadata();
        $oMetadataLocales   = $this->getLocalesMetadata();
        $sField             = $oMetaData->getFieldName($sName);
        $sLocalesField      = $oMetadataLocales !== FALSE ? $oMetadataLocales->getFieldName($sName) : NULL;
        $aFieldsFromLocales = static::hasLocales() ? $oMetadataLocales->getFieldNames() : [];

        // normal field
        if(property_exists($this, $sField)) {
            $this->$sField = $mValue;
        } // locales field
        elseif($sLocalesField !== NULL && in_array($sLocalesField, $aFieldsFromLocales)) {
            foreach($mValue as $sLang => $single) {
                $this->getLocales($sLang)->$sLocalesField = $single;
            }
        } // field do not exist in this model
        else {
            throw new Exception\Model(__('Cannot set value from ":name" property, because it does not exists in ":class" class.', ['name' => $sName, 'class' => $this->getClass()]));
        }
    }

    /**
     * Generate model form basing on model config.
     *
     * @static
     * @access   public
     * @throws   Exception\Fatal
     * @param    string                    $formName
     * @param    ModelCore\ModelFormConfig $config
     * @return   ModelCore\ModelForm
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function form($formName, ModelCore\ModelFormConfig $config = NULL)
    {
        return ModelCore\ModelForm::factory($this, $formName, $config);
    }

    /**
     * Persist all data of this Model to database.
     *
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function save()
    {
        if(static::hasLocales()) {
            foreach($this->getLocales('all') as $locale) {
                /* @var $locale ModelCore\Locales */
                DB::persist($locale);
            }
        }

        DB::persist($this);

        return TRUE;
    }

    /**
     * Get all model fields names.
     *
     * @static
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getFieldsNames()
    {
        return array_merge(static::getMetadata()->getFieldNames(), static::getMetadata()->getAssociationNames());
    }

    /**
     * Get name of particular Model table.
     *
     * @static
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getTableName()
    {
        return static::getMetadata()->getTableName();
    }

    /**
     * Get particular class metadata.
     *
     * @static
     * @access   public
     * @return   Doctrine\ORM\Mapping\ClassMetadata
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getMetadata()
    {
        if(!isset(static::$oMetadata[static::getClass()]) || !(static::$oMetadata[static::getClass()] instanceof Doctrine\ORM\Mapping\ClassMetadata)) {
            static::$oMetadata[static::getClass()] = DB::metadata(static::getClass());
        }

        return static::$oMetadata[static::getClass()];
    }

    /**
     * Get annotation data from particular field.
     *
     * @static
     * @access   public
     * @param    string $sField
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getFieldAnnotations($sField)
    {
        return static::getMetadata()->getFieldMapping($sField);
    }

    /**
     * Get primary key.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getPrimaryKey()
    {
        foreach(static::getFieldsNames() as $sField) {
            $aAnn = static::getFieldAnnotations($sField);

            if(isset($aAnn['id'])) {
                return $this->$sField;
            }
        }

        return NULL;
    }

    /**
     * Get used class name.
     *
     * @static
     * @access   public
     * @return   string
     * @since    1.0.0
     * @version  1.0.0-alpha
     */
    public static function getClass()
    {
        return get_called_class();
    }

    /**
     * Get parent class of used class name.
     *
     * @static
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getClassParent()
    {
        return get_parent_class(static::getClass());
    }

    /**
     * Get adapted values of particular field of Model instance for Views. Used in, for example, backend lists.<br />
     * Method to override.
     *
     * @access   public
     * @param    string $sFieldName
     * @return   string|FALSE
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getValueForView($sFieldName)
    {
        return $this->getValue($sFieldName);
    }

    /**
     * Get value of a single field.
     *
     * @access   public
     * @param    string $sFieldName
     * @return   mixed
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getValue($sFieldName)
    {
        return $this->$sFieldName;
    }

    /**
     * Check if particular model has locales.
     *
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function hasLocales()
    {
        return property_exists(static::getClass(), 'locales') && in_array('locales', static::getMetadata()->getAssociationNames());
    }

    /**
     * Check if particular model has a needed field.
     *
     * @static
     * @access   public
     * @param    string $sField
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function hasField($sField)
    {
        return in_array($sField, static::getFieldsNames());
    }

    /**
     * Get particular field.
     *
     * @static
     * @param    string $sFieldName
     * @return   mixed
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getField($sFieldName)
    {
        if(empty($sFieldName)) {
            throw new Exception\Fatal('Field name cannot be empty.');
        }

        if($this->hasField($sFieldName)) {
            return $this->$sFieldName;
        } elseif($this->getLocales() !== NULL && $this->getLocales()->hasField($sFieldName)) {
            return $this->getLocales()->$sFieldName;
        } else {
            throw new Exception\Fatal('Field with name "'.$sFieldName.'" does not exist.');
        }
    }

    /**
     * Return Locale Model instance.
     *
     * @access   public
     * @param    string $sLanguage
     * @return   array|ModelCore|NULL
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getLocales($sLanguage = NULL)
    {
        if(static::hasLocales()) {
            // if locales has no Models in it
            if(count($this->locales) === 0) {
                foreach(Router::getLangs() as $sLang) {
                    $this->createNewLocale($sLang);
                }
            }

            // all languages
            if($sLanguage === 'all') { // all languages
                return ($this->locales === NULL) ? [] : $this->locales;
            } // single language
            else {
                if($sLanguage === NULL) { // default language
                    $sLanguage = Router::getLang();
                }

                foreach($this->locales as $oLocale) {
                    /* @var $oLocale ModelCore\Locales */
                    if($oLocale->getLanguage() === $sLanguage) {
                        return $oLocale;
                    }
                }

                // create new Locale, if do not exist
                return $this->createNewLocale($sLanguage);
            }
        } else {
            return NULL;
        }
    }

    /**
     * Create new locales Model.
     *
     * @access   public
     * @param    string $sLang
     * @return   ModelCore\Locales
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function createNewLocale($sLang)
    {
        if(in_array($sLang, Core::getLanguages())) {
            $sLocaleClassName = $this->getClass().'\\Locales';

            $oLocale = new $sLocaleClassName();
            /* @var $oLocale ModelCore\Locales */
            $oLocale->setLanguage($sLang);
            $oLocale->setParent($this);

            $this->locales->add($oLocale);

            unset($sLocaleClassName);

            return $oLocale;
        }

        return NULL;
    }

    /**
     * Get class of this Model Locales.
     *
     * @access   public
     * @return   string|FALSE
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getLocalesClass()
    {
        if(static::hasLocales()) {
            return static::getMetadata()->getAssociationTargetClass('locales');
        } else {
            return FALSE;
        }
    }

    /**
     * Get metadata of locales
     *
     * @access   public
     * @return   Doctrine\ORM\Mapping\ClassMetadata|FALSE
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getLocalesMetadata()
    {
        return static::hasLocales() ? DB::metadata(static::getLocalesClass()) : FALSE;
    }

    /**
     * Remove all data of this Model from database.
     *
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function remove()
    {
        if(static::hasLocales()) {
            foreach($this->getLocales('all') as $oLocale) {
                /* @var $oLocale ModelCore\Locales */
                foreach($oLocale->getConfig()->getFields() as $sFieldName => $oField) {
                    /* @var $oField Form\Field */
                    $oField->whenRemovingEntity($this, $oLocale->$sFieldName);
                }

                DB::remove($oLocale);
            }
        }

        foreach($this->getConfig()->getFields() as $sFieldName => $oField) {
            /* @var $oField Form\Field */
            $oField->whenRemovingEntity($this, $this->$sFieldName);
        }

        DB::remove($this);

        return TRUE;
    }

    /**
     * Remove locales of particular language (if exists).
     *
     * @access   public
     * @param    string $sLang
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function removeLocales($sLang)
    {
        foreach($this->locales as $i => $oLocale) {
            /* @var $oLocale ModelCore\Locales */
            if($oLocale->getLanguage() === $sLang) {
                unset($this->locales[$i]);
            }
        }
    }

    /**
     * Get array of fields for frontend list.
     *
     * @static
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getListFields()
    {
        return [];
    }

    /**
     * Get URL of particular entity.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function url()
    {
        return '';
    }

    /**
     * Get title of particular entity.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getEntityTitle()
    {
        return '';
    }

    /**
     * Get identifier of particular model entity.
     *
     * @static
     * @access   public
     * @return   integer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getId()
    {
        return $this->id;
    }
}