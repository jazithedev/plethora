<?php

namespace Model;

use Plethora\Exception;
use Plethora\Config;
use Plethora\DB;
use Plethora\Form\Field as FormField;
use Plethora\Form\Field\CheckboxRelation as CheckboxRelationFormField;
use Plethora\Form\Field\Password as FormFieldPassword;
use Plethora\Helper\Encrypter as EncrypterHelper;
use Plethora\Mail;
use Plethora\Mailer;
use Plethora\ModelCore;
use Plethora\ModelCore\ModelFormConfig;
use Plethora\Route;
use Plethora\Session;
use Plethora\ModelCore\MConfig as MConfig;
use Plethora\Validator\RulesSetBuilder;
use Plethora\View\FieldFormatter\FieldFormatterDate;
use Model\User\Role as RoleModel;
use Doctrine;

/**
 * @Entity
 * @Table(name="users")
 *
 * @author           Krzysztof Trzos
 * @copyright    (c) 2013, Krzysztof Trzos
 * @package          Model
 * @since            1.0.0
 * @version          2.1.2-dev
 */
class User extends ModelCore
{

    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    protected $id;

    /**
     * Get all user roles.
     *
     * @ManyToMany(targetEntity="\Model\User\Role", inversedBy="users")
     * @JoinTable(name="users_assigned_roles")
     *
     * @access    protected
     * @var        Doctrine\Common\Collections\ArrayCollection
     * @since     3.3.0, 2015-01-08
     */
    protected $roles;

    /**
     * User representing image.
     *
     * @ManyToOne(targetEntity="\Model\User\Image", inversedBy="parent")
     * @JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @access    protected
     * @var       User\Image
     * @since     2.0.0-dev
     */
    protected $image;

    /**
     * @Column(type="string", length=20, unique=TRUE)
     */
    protected $login;

    /**
     * @Column(type="string", length=50, nullable=TRUE)
     */
    protected $nickname;

    /**
     * @Column(type="string", length=50, nullable=TRUE)
     */
    protected $firstname;

    /**
     * @Column(type="string", length=50, nullable=TRUE)
     */
    protected $lastname;

    /**
     * @Column(type="string",length=100, unique=TRUE)
     */
    protected $email;

    /**
     * @Column(type="string",length=24)
     */
    protected $password;

    /**
     * @Column(type="string", length=50, nullable=TRUE)
     */
    protected $city;

    /**
     * @Column(type="date", nullable=TRUE)
     */
    protected $birth_date;

    /**
     * @Column(type="text", nullable=TRUE)
     */
    protected $description;

    /**
     * @Column(type="boolean")
     */
    protected $activation = FALSE;

    /**
     * @Column(type="datetime")
     */
    protected $registration_date;

    /**
     * @Column(type="datetime", nullable=TRUE)
     */
    protected $login_date;

    /**
     * @access     private
     * @var        \Model\User
     * @since      2.0.2, 2013-12-25
     * @version    2.0.2, 2013-12-25
     */
    private static $loggedUser = NULL;

    /**
     * Constructor
     *
     * @access     public
     * @since      1.0.0
     * @version    3.1.1, 2014-09-28
     */
    public function __construct()
    {
        parent::__construct();

        $this->registration_date = new \DateTime();
        $this->roles             = new Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get user object.
     *
     * @static
     * @author     Krzysztof Trzos
     * @param      integer $iId
     * @return     \Model\User
     * @since      3.2.3, 2014-11-22
     * @version    3.2.3, 2014-11-22
     */
    public static function getUser($iId)
    {
        return DB::find('\Model\User', $iId);
    }

    /**
     * Fields config for backend.
     *
     * @access     public
     * @return     MConfig
     * @since      1.0.0
     * @version    2.1.0-dev
     */
    protected static function generateConfig()
    {
        # get all permissions list
        $aRoles  = [];
        $aResult = DB::queryList('\Model\User\Role')->execute();

        foreach($aResult as $oRole) {
            /* @var $oRole User\Role */
            $aRoles[] = [
                'value' => $oRole->getId(),
                'label' => $oRole->getName(),
            ];
        }

        # get config from parent
        $config = parent::generateConfig();

        # create fields
        $config->addField(
            FormField\Hidden::singleton('id')
                ->setLabel(__('ID'))
                ->setDisabled()
        );

        $config->addField(
            FormFieldPassword::singleton('password')
                ->setLabel(__('Password'))
                ->addRulesSet(
                    RulesSetBuilder\String::factory()
                        ->containNumbers(':value')
                        ->containText(':value')
                        ->containUppercase(':value')
                        ->containCustomCharacters(':value')
                )
        );

        $config->addField(
            FormField\Text::singleton('login')
                ->setLabel('Login')
                ->addTipParagraph(__('This value can contain only letters, numbers and "-" or "_" characters.'))
                ->setDisabled()
                ->addRulesSet(
                    RulesSetBuilder\String::factory()
                        ->onlyLettersNumsAndChars(':value', '\-_', __('This value can contain only letters, numbers and "-" or "_" characters.'))
                )
                ->addRulesSet(
                    RulesSetBuilder\Database::factory()
                        ->unique(':value', ':valuefrom:id', '\Model\User', 'login')
                )
        );

        $config->addField(
            FormField\Text::singleton('email')
                ->setLabel('E-mail')
                ->setDisabled()
                ->addRulesSet(
                    RulesSetBuilder\String::factory()
                        ->email(':value')
                )
                ->addRulesSet(
                    RulesSetBuilder\Database::factory()
                        ->unique(':value', ':valuefrom:id', '\Model\User', 'email')
                )
        );

        $config->addField(
            FormField\Text::singleton('firstname')
                ->setLabel(__('Firstname'))
                ->addRulesSet(
                    RulesSetBuilder\String::factory()
                        ->onlyLetters(':value')
                )
        );

        $config->addField(
            FormField\Text::singleton('lastname')
                ->setLabel(__('Lastname'))
                ->addRulesSet(
                    RulesSetBuilder\String::factory()
                        ->onlyLetters(':value'))
        );

        $config->addField(
            FormField\Text::singleton('nickname')
                ->setLabel(__('Nickname'))
        );

        $config->addField(
            FormField\ImageModel::singleton('image')
                ->setBrokerModel('\Model\User\Image')
                ->setUploadPath('uploads/users/image')
                ->setLabel(__('Image'))
                ->addRulesSet(
                    RulesSetBuilder\FileModel::factory()
                        ->allowedExt(':value', ['jpg', 'png', 'gif'])
                        ->maxSize(':value', 1024)
                )
        );

        $config->addField(
            FormField\Text::singleton('city')
                ->setLabel(__('City'))
                ->addRulesSet(
                    RulesSetBuilder\String::factory()
                        ->onlyLetters(':value')
                )
        );

        $config->addField(
            FormField\Textarea::singleton('description')
                ->setLabel(__('Description'))
        );

        if(\UserPermissions::hasPerm('users_edit')) {
            $config->addField(
                CheckboxRelationFormField::singleton('roles')
                    ->setRelatedModelName('\Model\User\Role')
                    ->setOptions($aRoles)
                    ->setLabel(__('Roles'))
            );
        }

        $config->addFieldFormatter('registration_date', FieldFormatterDate::factory());
        $config->addFieldFormatter('login_date', FieldFormatterDate::factory());

        # return config
        return $config;
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * Generate model form basing on model config.
     *
     * @static
     * @access     public
     * @throws     Exception\Fatal
     * @param      string          $formName
     * @param      ModelFormConfig $config
     * @return     \ModelForm\User
     * @since      3.5.2, 2015-02-20
     * @version    3.5.2, 2015-02-20
     */
    public function form($formName, ModelFormConfig $config = NULL)
    {
        return \ModelForm\User::factory($this, $formName, $config);
    }

    /**
     * Get ID of particular item.
     *
     * @access     public
     * @return    integer
     * @since      1.0.0
     * @version    3.1.1, 2014-09-28
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Set ID for particular item.
     *
     * @access     public
     * @param    integer $iId
     * @version    3.1.1, 2014-09-28
     */
    public function setId($iId)
    {
        $this->id = $iId;
    }

    /**
     * Get all user roles.
     *
     * @access   public
     * @return   Doctrine\Common\Collections\ArrayCollection
     * @since    3.3.0, 2015-01-08
     * @version  3.3.0, 2015-01-08
     */
    public function &getRoles()
    {
        return $this->roles;
    }

    /**
     * Add new role.
     *
     * @access   public
     * @param    RoleModel $oRole
     * @return   \Model\User
     * @since    3.3.0, 2015-01-08
     * @version  3.3.0, 2015-01-08
     */
    public function addRole(RoleModel $oRole)
    {
        $this->roles->add($oRole);

        return $this;
    }

    /**
     * Get user login.
     *
     * @access    public
     * @return    string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set user login.
     *
     * @access    public
     * @param    string $sValue
     * @return    User
     */
    public function setLogin($sValue)
    {
        $this->login = $sValue;

        return $this;
    }

    # NICKNAME

    /**
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @param string $nickname
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    # FIRSTNAME

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $value
     * @since      0.1.0
     * @version    2.0.0-dev
     */
    public function setFirstname($value)
    {
        $this->firstname = $value;
    }

    # LASTNAME

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $value
     */
    public function setLastname($value)
    {
        $this->lastname = $value;
    }

    /**
     * Get an user e-mail address.
     *
     * @author     Krzysztof Trzos
     * @access     public
     * @return    string
     * @since      1.0.0
     * @version    1.0.0
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set an user e-mail address.
     *
     * @author     Krzysztof Trzos
     * @access     public
     * @param    string $sEmail
     * @return    User
     * @since      1.0.0
     * @version    1.0.0
     */
    public function setEmail($sEmail)
    {
        $this->email = $sEmail;

        return $this;
    }

    /**
     * Get user password.
     *
     * @author    Krzysztof Trzos
     * @access    public
     * @return    string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set users password.
     *
     * @author     Krzysztof Trzos
     * @access     public
     * @param      string $sPassword
     * @since      1.0.0
     * @version    3.4.1, 2015-01-26
     */
    public function setPassword($sPassword)
    {
        $this->password = static::encryptPassword($this->getLogin(), $sPassword);
    }

    /**
     * Encrypt users password.
     *
     * @static
     * @author     Krzysztof Trzos
     * @access     public
     * @param      string $sLogin
     * @param      string $sPassword
     * @return     string
     * @since      3.4.1, 2015-01-26
     * @version    3.4.1, 2015-01-26
     */
    public static function encryptPassword($sLogin, $sPassword)
    {
        $oEncrypter = EncrypterHelper::factory();

        return $oEncrypter->encrypt($sLogin, $sPassword);
    }

    /**
     * Get an user city of residence.
     *
     * @author     Krzysztof Trzos
     * @access     public
     * @return    string
     * @since      1.0.0
     * @version    1.0.0
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set an user city of residence.
     *
     * @author     Krzysztof Trzos
     * @access     public
     * @param    string $sValue
     * @since      1.0.0
     * @version    1.0.0
     */
    public function setCity($sValue)
    {
        $this->city = $sValue;
    }

    # ACTIVATION

    /**
     * @return bool
     */
    public function getActivation()
    {
        return ($this->activation == 1) ? TRUE : FALSE;
    }

    /**
     * @param bool $v
     */
    public function setActivation($v)
    {
        $this->activation = (boolean)$v;
    }

    # BIRTH DATE

    /**
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birth_date;
    }

    /**
     * @access public
     * @param string $v
     */
    public function setBirthDate($v)
    {
        $this->birth_date = new \DateTime($v);
    }


    /**
     * Get users age.
     *
     * @access
     * @return bool|string
     * @since      0.1.0
     * @version    2.0.0-dev
     */
    public function getAge()
    {
        $date = $this->birth_date;
        /* @var $date \DateTime */

        if(is_null($date)) {
            return FALSE;
        } else {
            return date('Y') - $date->format('Y');
        }
    }

    /**
     * Get an user description.
     *
     * @author     Krzysztof Trzos
     * @access     public
     * @return    string
     * @since      1.0.0
     * @version    1.0.0
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set an user description.
     *
     * @author     Krzysztof Trzos
     * @access     public
     * @param    string $sDescription
     * @return    User
     * @since      1.0.0
     * @version    1.0.0
     */
    public function setDescription($sDescription)
    {
        $this->description = $sDescription;

        return $this;
    }

    # LOGIN DATE

    /**
     * @return \DateTime
     */
    public function getLoginDate()
    {
        return $this->login_date;
    }

    /**
     * @param \DateTime $value
     */
    public function setLoginDate(\DateTime $value)
    {
        $this->login_date = $value;
    }

    public function setLoginDateNOW()
    {
        $this->login_date = new \DateTime;
    }

    /**
     * Get full name of a user.
     *
     * @access    public
     * @return    string
     */
    public function getFullName()
    {
        return self::parseFullName($this->firstname, $this->nickname, $this->lastname, $this->login);
    }

    /**
     * Get user full name.
     *
     * @access    public
     * @param    string $fn
     * @param    string $nm
     * @param    string $ln
     * @param    string $login
     * @return    string
     */
    static function parseFullName($fn, $nm, $ln, $login)
    {
        $fn = trim($fn);
        $nm = trim($nm);
        $ln = trim($ln);

        if($nm != "" && $fn != "" && $ln != "") {
            return $fn." \"".$nm."\" ".$ln;
        } elseif($fn != "" && $ln != "") {
            return $fn.' '.$ln;
        } elseif($nm != "" && $fn != "") {
            return $fn." \"".$nm."\"";
        } elseif($nm != "") {
            return $nm;
        } else {
            return $login;
        }
    }

    /**
     * Get user name (nickname or login).
     *
     * @access   public
     * @return   string
     * @since    2.0.2, 2013-12-25
     * @version  2.0.2, 2013-12-25
     */
    public function getName()
    {
        if($this->nickname != '') {
            return $this->nickname;
        } else {
            return $this->login;
        }
    }

    /**
     * Check if user is logged.
     *
     * @static
     * @access   public
     * @return   boolean
     * @since    2.0.2, 2013-12-25
     * @version  2.1.2-dev
     */
    public static function isLogged()
    {
        if(Session::get('uid') === NULL) {
            return FALSE;
        } elseif(static::getLoggedUser() !== NULL) {
            return TRUE;
        } else {
            Session::destroy('uid');
            Session::destroy('username');

            return FALSE;
        }
    }

    /**
     * Get currently logged user.
     *
     * @static
     * @access   public
     * @return   User
     * @since    2.0.2, 2013-12-25
     * @version  2.1.2-dev
     */
    public static function getLoggedUser()
    {
        if(static::$loggedUser === NULL && Session::get('uid') !== NULL) {
            static::$loggedUser = DB::find('\Model\User', Session::get('uid'));
        }

        return static::$loggedUser;
    }

    /**
     * Set currently logged user.
     *
     * @static
     * @access   public
     * @param    User $user
     * @since    2.0.2, 2013-12-25
     * @version  2.0.2, 2013-12-25
     */
    public static function setLoggedUser(User $user)
    {
        static::$loggedUser = $user;
    }

    /**
     * Get adapted values of particular field of Model instance for Views. Used in, for example, backend lists.
     *
     * @author   Krzysztof Trzos
     * @access   public
     * @param    string $sFieldName
     * @return   string
     * @since    1.0.0
     * @version  3.4.6, 2015-02-08
     */
    public function getValueForView($sFieldName)
    {
        $mValue = parent::getValueForView($sFieldName);

        switch($sFieldName) {
            // roles
            case 'roles':
                $sOutput = '';

                if(count($mValue) > 0) {
                    foreach($mValue as $oRoles) {
                        /* @var $oRoles User\Role */
                        $sOutput .= $oRoles->getName().', ';
                    }

                    $sOutput = rtrim($sOutput, ', ');
                } else {
                    $sOutput = '-';
                }

                return $sOutput;
            // login_date
            case 'login_date':
                $loginDate = $mValue;

                /* @var $loginDate \DateTime */

                return empty($loginDate) ? '-' : $loginDate->format('Y-m-d H:i');
        }

        return $mValue;
    }

    /**
     * Generates URL to particular user's profile.
     *
     * @access     public
     * @return     string
     * @since      3.2.2, 2014-11-22
     * @version    3.2.2, 2014-11-22
     */
    public function getProfileURL()
    {
        return Route::factory('user_profile')
            ->url(['id' => $this->getId()]);
    }

    /**
     * Send e-mail to particular user.
     *
     * @access     public
     * @param      string $sSubject
     * @param      string $sBody
     * @return     bool
     * @since      3.5.0, 2015-02-17
     * @version    3.5.0, 2015-02-17
     */
    public function sendEmail($sSubject, $sBody)
    {
        if($this->getEmail() !== NULL) {
            $oMail = new Mail();
            $oMail->setFrom(Config::get('base.email'));
            $oMail->setTo($this->getEmail());
            $oMail->setSubject($sSubject);
            $oMail->setBody($sBody, 'text/html');

            return Mailer::factory()
                ->send($oMail);
        }

        return FALSE;
    }

    /**
     * Get user image.
     *
     * @access     public
     * @return     User\Image
     * @since      2.0.0-dev
     * @version    2.0.0-dev
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get path to users image. If has none, then takie default image.
     *
     * @access   public
     * @return   string
     * @since    2.1.2-dev
     * @version  2.1.2-dev
     */
    public function getImagePath()
    {
        if($this->getImage() === NULL) {
            return 'themes/adminlte/img/avatar5.png';
        } else {
            return $this->getImage()->getFile()->getFullPath();
        }
    }

    /**
     * Get user image styled by "UserLogo" style.
     *
     * @access   public
     * @return   string
     * @since    2.1.2-dev
     * @version  2.1.2-dev
     */
    public function getImageStyled()
    {
        $imgStyles = \Plethora\ImageStyles::factory();
        $styled    = $imgStyles->useStyle('UserLogo', $this->getImagePath());

        return \Plethora\Router::getBase().'/'.$styled;
    }

    /**
     * Set user image.
     *
     * @access     public
     * @param      User\Image $image
     * @return     User
     * @since      2.0.0-dev
     * @version    2.0.0-dev
     */
    public function setImage(User\Image $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Remove all data of this Model from database.
     *
     * @access   public
     * @return   boolean
     * @since    2.1.2-dev
     * @version  2.1.2-dev
     */
    public function remove()
    {
        if((int)$this->id === 1) {
            Route::factory('home')->redirectTo();
        }

        parent::remove();
    }

    /**
     * Return user account register date.
     *
     * @access   public
     * @return   \DateTime
     * @since    2.1.2-dev
     * @version  2.1.2-dev
     */
    public function getRegisterDate()
    {
        return $this->registration_date;
    }
}