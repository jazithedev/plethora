<?php

namespace Plethora\Form\Field;

use FileManager;
use Plethora\DB;
use Plethora\Form\Field;
use Plethora\Helper\Arrays;
use Plethora\ModelCore;
use Plethora\View;
use Plethora\Form;
use Plethora\Validator;
use Model\User;
use Doctrine;
use Plethora\Exception;

/**
 * Field form generating an input of "file" type.
 *
 * @package        Plethora
 * @subpackage     Form\Field
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class FileModel extends Field
{

    /**
     * Path to the View for this field.
     *
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sView = 'base/form/field/file_model';

    /**
     * @access    protected
     * @var        array
     * @since     1.0.0-alpha
     */
    protected $aFileBrokers = [];

    /**
     * @access    protected
     * @var        array
     * @since     1.0.0-alpha
     */
    protected $aFile = [];

    /**
     * @access    protected
     * @var        array
     * @since     1.0.0-alpha
     */
    protected $aFileTemp = [];

    /**
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sBrokerModel = NULL;

    /**
     * @access    protected
     * @var        array
     * @since     1.0.0-alpha
     */
    protected $aSentFileData = [];

    /**
     * Path of uploaded file.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sUploadPath;

    /**
     * Create new instance of Field class.
     *
     * @static
     * @access   public
     * @param    string $name field name
     * @param    Form   $form form
     * @return   FileModel
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory($name, Form $form)
    {
        return parent::factory($name, $form);
    }

    /**
     * Class constuctor.
     *
     * @access   public
     * @param    string $name
     * @param    Form   $form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct($name, Form $form)
    {
        parent::__construct($name, $form);

        $this->getAttributes()->setAttribute('type', 'file');
        $this->getFormObject()->addEnctype();
    }

    /**
     * Reset form field attributes which needed to change after changing Form object to which this field belongs.
     *
     * @access   protected
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function resetAllNeededAttributes()
    {
        parent::resetAllNeededAttributes();

        $this->getFormObject()->addEnctype();

        return $this;
    }

    /**
     * Set particular field files upload path.
     *
     * @access   public
     * @param    string $sPathAttr
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setUploadPath($sPathAttr)
    {
        $sPath = str_replace('/', DIRECTORY_SEPARATOR, $sPathAttr);

        $this->sUploadPath = $sPath;

        return $this;
    }

    /**
     * Get particular field files upload path.
     *
     * @access   public
     * @param    boolean $bToURL
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getUploadPath($bToURL = FALSE)
    {
        return $bToURL ? str_replace(DIRECTORY_SEPARATOR, '/', $this->sUploadPath) : $this->sUploadPath;
    }

    /**
     * Do some things after validation.
     *
     * @access     public
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function afterValidation()
    {
        $aSentFileDataAll = $this->getSentFileArray();
        $aValue           = $this->getFormObject()->getDefaultVal($this->getName());
        $aFormSentValues  = $this->getFormObject()->getMethodValue();

        // remove files which fields were DELETED from form
        foreach($aValue as $sLang => $aAllValuesPerLang) {
            foreach($aAllValuesPerLang as $i => $sCurrValue) {
                /* @var $sCurrValue ModelCore\FileBroker */
                $sValueKey               = 'existing_file_'.$this->getName().'_'.$sLang.'_'.$i;
                $sCurrentFileFieldExists = Arrays::get($aFormSentValues, $sValueKey, FALSE);

                if($sCurrentFileFieldExists === FALSE || $sCurrentFileFieldExists !== FALSE && empty($sCurrentFileFieldExists)) {
                    if(!empty($sCurrValue)) {
                        $sCurrValue->remove();
                    }

                    unset($aValue[$sLang][$i]);
                }
            }
        }

        // upload new file
        if($this->getUploadPath() !== '') {
            foreach($aSentFileDataAll as $sLang => $aAllValuesPerLang) {
                foreach(array_keys($aAllValuesPerLang) as $i) {
                    // get temporary and current values for particular fieldset
                    $oTempValue = Arrays::path($this->aFileTemp, $sLang.'.'.$i, FALSE);
                    /* @var $oTempValue \Model\File */
                    $sOldValue = Arrays::path($this->aFile, $sLang.'.'.$i, FALSE);
                    /* @var $oTempValue \Model\File */

                    // if a file is stored as temporary file (because, for example, an error occured in other file earlier)
                    if($oTempValue !== FALSE) {
                        $oTempValue
                            ->moveFile($this->getUploadPath())
                            ->setStatus(1)
                            ->setPath($this->getUploadPath())
                            ->save();

                        if(!empty($sOldValue)) {
                            $oOldFile = DB::find('\Model\File', $sOldValue->getId());
                            /* @var $oOldFile \Model\File */
                            $oOldFile->decreaseUses();
                        }

                        $this->aFile[$sLang][$i] = $oTempValue;

                        unset($this->aFileTemp[$sLang][$i]);
                    }

                    // tell database entity manager to save this FileBroker
                    $oBroker = Arrays::path($this->aFileBrokers, $sLang.'.'.$i, FALSE);
                    /* @var $oBroker ModelCore\FileBroker */

                    // check if particular broker has a file
                    if($oBroker->getFile() !== NULL) {
                        $oBroker->save();

                        // set particular FileBroker as field value
                        Arrays::createMultiKeys($aValue, $sLang.'.'.$i, $oBroker);
                    } // if broker dont have a file, remove it from value
                    else {
                        Arrays::createMultiKeys($aValue, $sLang.'.'.$i, NULL);

                        if($oBroker->getId() !== NULL) {
                            $oBroker->remove();
                        }
                    }
                }
            }
        }

        $this->setValue($aValue);
    }

    /**
     * Make some operations when form was checked with validator and this
     * particular field was valid.
     *
     * @access     public
     * @param    string $sLang
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function afterValidationWhenValid($sLang)
    {
        // get trough all filebrokers and check if some of them have files to upload
        $allDefaultValuesForLang = Arrays::get($this->aFileBrokers, $sLang, []);

        foreach($allDefaultValuesForLang as $i => &$broker) {
            /* @var $broker ModelCore\FileBroker */
            $dataBatch = $broker->getTempData();

            if($dataBatch !== []) {
                // generate FILE instance from particular batch of sent file
                $file = $this->createFileBySentData($dataBatch);
                $file->save();

                DB::flush(); // this flush is used to save temporary file in DB

                Arrays::createMultiKeys($this->aFileTemp, $sLang.'.'.$i, $file);

                $broker
                    ->setFile($file)
                    ->clearTempData();
            }
        }
//		}
    }

    /**
     * Reset form values.
     *
     * @access   protected
     * @return   Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function resetValue()
    {
        // get default values
        $defaultValues = $this->getFormObject()->getDefaultVal($this->getName());

        // get models instances based on defaults
        foreach($defaultValues as $sLang => $allDefaultValuesForLang) {
            foreach($allDefaultValuesForLang as $i => $mSingleValue) {
                $fileBroker = DB::find($this->sBrokerModel, $mSingleValue);
                /* @var $fileBroker ModelCore */

                Arrays::createMultiKeys($this->aFileBrokers, $sLang.'.'.$i, $fileBroker);
                Arrays::createMultiKeys($this->aFile, $sLang.'.'.$i, $fileBroker->file);

                $this->setValue($fileBroker, $i, $sLang);
            }
        }

//        // if form is submitted
//        if($this->getFormObject()->isSubmitted()) {
//
//        }

        // return $this
        return $this;
    }

    /**
     * Method is called by Form object when this particular form is used (sent).
     *
     * @access   protected
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function whenFormSubmitted()
    {
        // get sent data
        $sentFileData = $this->getSentFileArray();

        // loop trough all sent data
        foreach($sentFileData as $sLang => $allDefaultValuesForLang) {
            foreach($allDefaultValuesForLang as $i => $dataBatch) {
                // create file broker (if not exists)
                $broker = Arrays::path($this->aFileBrokers, $sLang.'.'.$i, FALSE);

                if($broker === FALSE) {
                    $parent = $this->isMultilanguage() ?
                        $this->getFormObject()->getModel()->getLocales() :
                        $this->getFormObject()->getModel();

                    $broker = new $this->sBrokerModel;
                    /* @var $broker ModelCore\FileBroker */

                    if(!$broker instanceof ModelCore\FileBroker) {
                        throw new Exception\Fatal(
                            'Given bad class name (`'.get_class($broker).'`). '.
                            'Not a `ModelCore\FileBroker` class.'
                        );
                    }

                    $broker->setParent($parent);
                }

                // if file was uploaded earlier and is in "temporary file" field
                $formValues = $this->getFormObject()->getMethodValue();
                $tempValue  = Arrays::get($formValues, 'temp_file_'.$this->getName().'_'.$sLang.'_'.$i);

                if(!empty($tempValue)) {
                    $oFile = DB::find('\Model\File', $tempValue);
                    /* @var $oFile \Model\File */

                    Arrays::createMultiKeys($this->aFileTemp, $sLang.'.'.$i, $oFile);
                }

                // if file has been sent by $_FILE method
                if(isset($dataBatch['tmp_name']) && $dataBatch['tmp_name'] !== '' && $dataBatch['size'] >= 0) {
                    $broker->setTempData($dataBatch);
                }

                // set file to filebroker
                $oFileForBroker = Arrays::path($this->aFileTemp, $sLang.'.'.$i, FALSE);

                if($oFileForBroker !== FALSE) {
                    $broker->setFile($oFileForBroker);
                }

                // set broker as fields value
                Arrays::createMultiKeys($this->aFileBrokers, $sLang.'.'.$i, $broker);
                $this->setValue($broker, $i, $sLang);
            }
        }
    }


    /**
     * Create \Model\File instance on the basis of sent form data.
     *
     * @access   private
     * @param    array $dataBatch
     * @return   \Model\File
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function createFileBySentData($dataBatch)
    {
        $oLoggedUser   = User::getLoggedUser();
        $aExplodedFile = explode('.', $dataBatch['name']);
        $sPath         = PATH_TEMP.'form_files'.DS.$this->getFormObject()->getName().DS.$this->getName();
        $sPath         = str_replace([PATH_PUBLIC, DS], ['', '/'], $sPath);

        $oFileManager = \FileManager::factory();
        $oFileManager->prepareDir($sPath);
        $oFileManager->parseFileData($dataBatch, $aExplodedFile[0]);
        $oFileManager->upload($sPath, FileManager::UPLOAD_SAVE_BOTH);

        $oFile = new \Model\File();
        $oFile->setPath($sPath);
        $oFile->setSize($dataBatch['size']);
        $oFile->setExt($oFileManager->getExt());
        $oFile->setName($oFileManager->getName());
        $oFile->setMime($oFileManager->getMime());
        $oFile->setStatus(0);

        if($oLoggedUser !== NULL) {
            $oFile->setAuthor($oLoggedUser);
        }

        return $oFile;
    }

    /**
     * Create singleton version of particular type of form field.
     *
     * @static
     * @access     public
     * @param    string $sName
     * @return    FileModel
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function singleton($sName)
    {
        return static::singletonByType($sName, 'FileModel');
    }

    /**
     * Set new \Plethora\Form object to particular form Field if is singleton.
     *
     * @access   public
     * @param    Form $form
     * @return   Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setFormIfSingleton(Form &$form)
    {
        $form->addEnctype();

        parent::setFormIfSingleton($form); // TODO: Change the autogenerated stub
    }

    /**
     * Render only one value of the field.
     *
     * @access   protected
     * @param    array   $aFieldValueContent
     * @param    string  $sLang
     * @param    integer $i
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function renderSingleValue(array &$aFieldValueContent, $sLang, $i)
    {
        // render file
        if(empty($this->sBrokerModel)) {
            $aFieldValueContent[$i] = View::factory('base/alert')
                ->set('sType', 'warning')
                ->set('sMsg', __('To make this field work properly, there must be a broker model name set.'))
                ->render();
        } else {
            $aFieldValueContent[$i] = View::factory($this->getView())
                ->bind('sLang', $sLang)
                ->bind('iValueNumber', $i)
                ->bind('oField', $this)
                ->set('oCurrentFile', Arrays::path($this->aFile, $sLang.'.'.$i, NULL))
                ->set('oTmpFile', Arrays::path($this->aFileTemp, $sLang.'.'.$i, NULL))
                ->render();
        }

        // return TRUE value to tell, that field rendered successfully
        return TRUE;
    }

    /**
     * Render only one value of the field.
     *
     * @access   protected
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function renderSingleValuePattern()
    {
        return View::factory($this->getView())
            ->set('sLang', 'LANGUAGE')
            ->set('iValueNumber', 'NUMBER')
            ->set('oField', $this)
            ->set('oCurrentFile', NULL)
            ->set('oTmpFile', NULL)
            ->render();
    }

    /**
     * @access   public
     * @param    string  $sLang
     * @param    integer $i
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function renderFileExistanceAttrs($sLang, $i)
    {
        $sAttrs = $this
            ->getAttributes()
            ->renderAttributes(
                [
                    'type' => 'hidden',
                    'id'   => '',
                    'name' => 'existing_file_'.$this->getName().'_'.$sLang.'_'.$i,
                ]
            );

        return $sAttrs;
    }

    /**
     * @access     public
     * @param    string  $sLang
     * @param    integer $i
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function renderTempFileAttrs($sLang, $i)
    {
        $sAttrs = $this
            ->getAttributes()
            ->renderAttributes(
                [
                    'type' => 'hidden',
                    'id'   => '',
                    'name' => 'temp_file_'.$this->getName().'_'.$sLang.'_'.$i,
                ]
            );

        return $sAttrs;
    }

    /**
     * Make some operations related to this field before Model Entity will be
     * completely removed.
     *
     * @access    public
     * @param    ModelCore $oModel
     * @param    mixed     $mValue
     * @return   boolean
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function whenRemovingEntity(ModelCore $oModel, $mValue)
    {
        parent::whenRemovingEntity($oModel, $mValue);

        if(empty($mValue)) {
            return FALSE;
        }

        $aValues = [];

        if(!is_array($mValue)) {
            $aValues[] = $mValue;
        }

        foreach($aValues as $oFileBroker) {
            /* @var $oSingleFileBroker ModelCore\FileBroker */
            /* @var $oFileBroker ModelCore\FileBroker */
            if($oFileBroker instanceof Doctrine\ORM\PersistentCollection) {
                foreach($oFileBroker as $oSingleFileBroker) {
                    $oSingleFileBroker->remove();
                }
            } else {
                $oFileBroker->remove();
            }
        }

        return TRUE;
    }

    /**
     * @access   public
     * @param    string $model
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setBrokerModel($model)
    {
        if(!class_exists($model)) {
            Exception\Fatal('Broker with "'.$model.'" class does not exist.');
        }

        $this->sBrokerModel = $model;

        return $this;
    }

    /**
     * Get particular key (or whole array) about uploaded file.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getSentFileArray()
    {
        if($this->aSentFileData !== []) {
            return $this->aSentFileData;
        }

        // check if form is submitted/checked and don't have any errors
        if(!isset($_FILES[$this->getFormObject()->getName()])) {
            return [];
        }

        // get data from $_FILES array
        $aOutput = [];
        $aFiles  = $_FILES[$this->getFormObject()->getName()];
        /* @var $aFiles array */
        $aFilesCurrentField = [];

        if(!isset($aFiles['name'][$this->getName()])) {
            return [];
        }

        foreach($aFiles as $sFileData => $aFields) {
            $aFilesCurrentField[$sFileData] = $aFields[$this->getName()];
        }

        foreach($aFilesCurrentField as $sFileData => $aLang) {
            foreach($aLang as $sLang => $aAllValues) {
                if(!isset($aOutput[$sLang])) {
                    $aOutput[$sLang] = [];
                }

                foreach($aAllValues as $i => $sFileDataValue) {
                    if(!isset($aOutput[$sLang][$i])) {
                        $aOutput[$sLang][$i] = [];
                    }

                    $aOutput[$sLang][$i][$sFileData] = $sFileDataValue;
                }
            }
        }

        // return all files data
        return $this->aSentFileData = $aOutput;
    }

    /**
     * Set field as required (can't be empty).
     *
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setRequired()
    {
        $oRulesSet = Validator\RulesSetBuilder\FileModel::factory();
        /* @var $oRulesSet Validator\RulesSetBuilder\FileModel */

        $this->addRulesSet($oRulesSet->notEmpty(':value'));
        $this->bRequired = TRUE;

        if($this->iQuantityMin === 0) {
            $this->setQuantityMin(1);
        }

        return $this;
    }

    /**
     * Get field value.
     *
     * @access   public
     * @param    string  $lang
     * @param    integer $valueNumber
     * @return   mixed
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getValue($lang = NULL, $valueNumber = NULL)
    {
        return parent::getValue($lang, $valueNumber); // TODO: Change the autogenerated stub
    }


}
