<?php

namespace PlethoraInstall;

use Plethora\Helper;

/**
 * Main class used to install Plethora.
 *
 * @author         Krzysztof Trzos
 * @package        PlethoraInstall
 * @subpackage     classes
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Core
{

    /**
     * Main view of install system.
     *
     * @access    private
     * @var       \PlethoraInstall\InstallView
     * @since     1.0.0-alpha
     */
    private $oViewBody = NULL;

    /**
     * Stores all basic data about installation.
     *
     * @access    private
     * @var       array
     * @since     1.0.0-alpha
     */
    private $aCached = [];

    /**
     * Factory method.
     *
     * @static
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory()
    {
        return new Core();
    }

    /**
     * Constructor.
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct()
    {
        $this->oViewBody = InstallView::factory('body');
        $this->stepsController();
    }

    /**
     * Controller of all installation steps.
     *
     * @access   private
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function stepsController()
    {
        $sStep         = filter_input(INPUT_POST, 'step');
        $this->aCached = $this->getCached();

        if(!empty($this->aCached) && !in_array($sStep, [NULL, 'first_batch']) && !file_exists($this->getAppPath())) {
            setcookie('install-last_step', 2);
            $this->setCachedSingle('step_done', 1);
            $this->parseJsonOutput('', 'refresh');
        }

        switch($sStep) {
            case 'first_batch':
                $this->stepCheckFirstBatch();
                break;
            case 'prepare_files':
                $this->stepPrepareFiles();
                break;
            case 'update_database':
                $this->stepUpdateDatabase();
                break;
            case 'create_user':
                $this->stepCreateUser();
                break;
            case 'end':
                $this->stepEnd();
                break;
            default:
                $this->userView();
        }
    }

    /**
     * This method generates all UI of framework setup.
     *
     * @access   private
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function userView()
    {
        $bMeetsReqs       = FALSE;
        $bPHPVersionCheck = version_compare(PHP_VERSION, '5.4.12', '>=');
        $bRegisterGlobals = ini_get('register_globals');

        // @TODO CHECK INITIAL PHP CONDITIONS
        if(TRUE) {
            $bMeetsReqs = TRUE;
        }

        $oViewContent = InstallView::factory('content');
        $oViewContent->set('sAppName', APP_NAME);
        $oViewContent->set('aCached', $this->aCached);
        $oViewContent->bind('bPHPVersionCheck', $bPHPVersionCheck);
        $oViewContent->bind('bRegisterGlobals', $bRegisterGlobals);
        $oViewContent->bind('bMeetsReqs', $bMeetsReqs);

        // check step #3
        $bFilesPrepared = TRUE;

        if(empty($this->aCached) || $this->aCached['step_done'] < 2 || !empty($this->aCached) && static::isDirEmpty($this->getAppPath())) {
            $bFilesPrepared = FALSE;
        }

        $oViewContent->bind('bFilesPrepared', $bFilesPrepared);

        // check step #4
        $bDataBaseUpdated = FALSE;

        if(!empty($this->aCached) && $this->aCached['step_done'] >= 3) {
            $bDataBaseUpdated = TRUE;
        }

        $oViewContent->bind('bDataBaseUpdated', $bDataBaseUpdated);

        // check step #5
        $oViewContent->set('bUserCreated', $this->checkIfAdminCreated());

        // bind View
        $this->oViewBody->bind('oBody', $oViewContent);
    }

    /**
     * STEP - Prepare first batch of data and save it to a single file for
     * next setup steps.
     *
     * @access   private
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function stepCheckFirstBatch()
    {
        $appName           = filter_input(INPUT_POST, 'app_name');
        $sAppDescription   = filter_input(INPUT_POST, 'app_descr');
        $sAppEmail         = filter_input(INPUT_POST, 'app_email');
        $sDatabaseHost     = filter_input(INPUT_POST, 'db_host');
        $sDatabaseName     = filter_input(INPUT_POST, 'app_db_name');
        $sDatabaseUser     = filter_input(INPUT_POST, 'db_uname');
        $sDatabasePassword = filter_input(INPUT_POST, 'db_upassw');

        // @TODO: validate ALL data

        try {
            $connData = 'mysql:host='.$sDatabaseHost.';dbname='.$sDatabaseName;
            $oDbConn  = new \PDO($connData, $sDatabaseUser, $sDatabasePassword);
            $oDbConn->query("SELECT * FROM test_this_conn LIMIT 1");

            if($oDbConn->errorCode() === '42S02') {
                if(!$this->cachedExists()) {
                    $sAppPath = $this->getAppPath();

                    if(!file_exists($sAppPath)) {
                        mkdir($this->getAppPath(), 0755);
                    } else {
                        $msg =
                            'Application of this name already exists! Remove `\application\\'.APP_NAME.'` directory '.
                            'or change the name of `\public_html\\'.APP_NAME.'` directory.';
                        $this->parseJsonOutput($msg, 'error');
                    }

                    $this->setCached([
                        'app_name'  => $appName,
                        'app_descr' => $sAppDescription,
                        'app_email' => $sAppEmail,
                        'db_host'   => $sDatabaseHost,
                        'db_name'   => $sDatabaseName,
                        'db_user'   => $sDatabaseUser,
                        'db_pass'   => $sDatabasePassword,
                        'step_done' => 1,
                    ]);

                    $this->parseJsonOutput('Data saved. Application main directory created.');
                } else {
                    $this->updateCached([
                        'app_name'  => $appName,
                        'app_descr' => $sAppDescription,
                        'app_email' => $sAppEmail,
                        'db_host'   => $sDatabaseHost,
                        'db_name'   => $sDatabaseName,
                        'db_user'   => $sDatabaseUser,
                        'db_pass'   => $sDatabasePassword,
                    ]);

                    $this->parseJsonOutput('Data has been modified successfully.');
                }
            } else {
                $aErr = $oDbConn->errorInfo();
                $this->parseJsonOutput($aErr[2], 'error');
            }
        } catch(\PDOException $e) {
            $this->parseJsonOutput($e->getMessage(), 'error');
            die();
        }

        $this->parseJsonOutput('ERROR', 'error');
    }

    /**
     * STEP - In this step all needed (by application) files are prepared.
     *
     * @access   private
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function stepPrepareFiles()
    {
        // new directories in app directory from public_html
        $aNewDirs = [
            'themes'.DS.APP_NAME,
            'uploads',
            'thumbs',
            'temp',
        ];

        foreach($aNewDirs as $sDir) {
            $sNewDirPath = PATH_PUBLIC_APP.DS.$sDir;

            if(!file_exists($sNewDirPath)) {
                mkdir($sNewDirPath);
            }
        }

        // create directories/files in applications/APP_NAME directory
        $aFilesAndDirs = [
            'cache'    => [
                'i18n' => [
                    'en.txt',
                    'pl.txt',
                    'info.txt',
                ]
            ],
            'classes'  => [
                'Controller' => [
                    'Backend.php',
                    'Frontend.php'
                ]
            ],
            'config'   => [
                'backend.php',
                'base.php',
                'cache.php',
                'database.php',
                'mailer.php',
                'meta.php',
                'modules.php',
                'recaptcha.php',
                'routing.php',
                'session.php',
            ],
            'logs'     => [],
            'proxies'  => [],
            'sessions' => [],
            'views'    => [],
        ];

        if(!static::isDirEmpty($this->getAppPath())) {
            $this->parseJsonOutput('This operation was done earlier.', 'error');
        }

        $this->scanFilesList($aFilesAndDirs);
        $this->setCachedSingle('step_done', 2);
        $this->parseJsonOutput('All files prepared successfully.');
    }

    /**
     * STEP - Update database on the modules basis.
     *
     * @access   private
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function stepUpdateDatabase()
    {
        $sUpdateOutput = Database::factory()->update();

        if($sUpdateOutput === 'ok') {
            $this->setCachedSingle('step_done', 3);
            $this->parseJsonOutput('Database schema updated successfully.');
        } else {
            file_put_contents('.'.DS.'db_update.log', $sUpdateOutput);
            $this->parseJsonOutput('Error has occured. Please see `db_update.log` file copied to application directory to check what is wrong.', 'error');
        }
    }

    /**
     * STEP - Create superuser of this application.
     *
     * @access   private
     * @return   void
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function stepCreateUser()
    {
        // check if user was created earlier
        if($this->checkIfAdminCreated()) {
            $this->parseJsonOutput('User created earlier.', 'error');
        }

        // connect to database
        $oDbConn = new \PDO('mysql:host='.$this->aCached['db_host'].';dbname='.$this->aCached['db_name'], $this->aCached['db_user'], $this->aCached['db_pass']);

        // get data from inputs
        $userName  = filter_input(INPUT_POST, 'user_name');
        $userMail  = filter_input(INPUT_POST, 'user_email');
        $userPass  = filter_input(INPUT_POST, 'user_pass');
        $userPass2 = filter_input(INPUT_POST, 'user_pass2');

        // data validation
        if(empty($userName)) {
            $this->parseJsonOutput('User login cannot be empty!', 'error');
        }

        if(empty($userMail)) {
            $this->parseJsonOutput('User e-mail cannot be empty!', 'error');
        }

        if(empty($userPass)) {
            $this->parseJsonOutput('User password cannot be empty!', 'error');
        }

        if(empty($userPass2)) {
            $this->parseJsonOutput('Password must be confirmed!', 'error');
        }

        if($userPass !== $userPass2) {
            $this->parseJsonOutput('Passwords do not match.', 'error');
        }

        // encrypt password
        require_once PATH_CORE.DS.'Plethora'.DS.'Helper'.DS.'Encrypter.php';

        $sEncrypted = Helper\Encrypter::factory()->encrypt($userName, $userPass);

        // add user to database
        $query =
            "INSERT INTO users (id, login, email, password, activation, registration_date) VALUES ".
            "(1, '".$userName."', '".$userMail."', '".$sEncrypted."', 1, NOW())";

        $oDbConn->query($query);

        // check if database returned an error
        $aErrInfo = $oDbConn->errorInfo();

        // if no error
        if($aErrInfo[0] === '00000') {
            $this->setCachedSingle('step_done', 4);
            $this->parseJsonOutput('User created. You can go to the next step.');
        } elseif($aErrInfo[0] === '23000') {
            $this->parseJsonOutput('Table "users" is not empty. User with id == 1 already exists!', 'error');
        } else {
            $this->parseJsonOutput($aErrInfo[2], 'error');
        }
    }

    /**
     * This is the last step of framework installation. After clicking proper
     * button, this function will be executed and site will refresh to the
     * front page of newly installed web application.
     *
     * @access   private
     * @return   void
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function stepEnd()
    {
        if(file_exists('.'.DS.'cached.tmp')) {
            unlink('.'.DS.'cached.tmp');
        }

        if(file_exists('.'.DS.'db_update.log')) {
            unlink('.'.DS.'db_update.log');
        }

        if(file_exists(PATH_PUBLIC_APP.DS.'install.php')) {
            unlink(PATH_PUBLIC_APP.DS.'install.php');
        }

        unset($_COOKIE['install-last_step']);
        setcookie('install-last_step', NULL, -1, '/');

        $this->parseJsonOutput('done');
    }

    /**
     * Create single file.
     *
     * @access   private
     * @param    string $sFilePath
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function makeFile($sFilePath)
    {
        $sFileContent = '';
        $sPath        = PATH_ROOT.'framework'.DS.'install'.DS.'patterns'.DS.'application'.$sFilePath;

        if(file_exists($sPath)) {
            $sFileContent = file_get_contents($sPath);

            switch($sFilePath) {
                case DS.'config'.DS.'base.php':
                    $sFileContent = str_replace(
                        [
                            '{{APP_NAME}}',
                            '{{APP_DESCR}}',
                            '{{E_MAIL}}',
                            '{{CRON_TOKEN}}',
                            '{{THEME_BACKEND}}',
                            '{{THEME_FRONTEND}}',
                        ], [
                        $this->aCached['app_name'],
                        $this->aCached['app_descr'],
                        $this->aCached['app_email'],
                        substr(str_shuffle(MD5(microtime())), 0, 10),
                        'adminlte',
                        APP_NAME,
                    ], $sFileContent
                    );
                    break;
                case DS.'config'.DS.'database.php':
                    $sFileContent = str_replace(
                        [
                            '{{PROXY_PREFIX}}',
                            '{{HOST}}',
                            '{{DBNAME}}',
                            '{{USER}}',
                            '{{PASS}}',
                        ], [
                        ucfirst(str_replace('_', '', APP_NAME)),
                        $this->aCached['db_host'],
                        $this->aCached['db_name'],
                        $this->aCached['db_user'],
                        $this->aCached['db_pass'],
                    ], $sFileContent
                    );
                    break;
            }
        }

        file_put_contents($this->getAppPath().$sFilePath, $sFileContent);
    }

    /**
     * Scan the list of directories / files and create them.
     *
     * @access   private
     * @param    array  $aList
     * @param    string $sParent
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function scanFilesList(array $aList, $sParent = '')
    {
        foreach($aList as $sDir => $mValue) {
            if(is_array($mValue)) {
                $sDirPath    = $sParent.DS.$sDir;
                $sAppDirPath = $this->getAppPath().$sDirPath;

                if(!file_exists($sAppDirPath)) {
                    mkdir($sAppDirPath, 0755);
                }

                $this->scanFilesList($mValue, $sDirPath);
            } else {
                $this->makeFile($sParent.DS.$mValue);
            }
        }
    }

    /**
     * Render output of installation system.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function renderOutput()
    {
        return $this->oViewBody->render();
    }

    /**
     * Create output for AJAX requests.
     *
     * @access   private
     * @param    string $sMessage
     * @param    string $sStatus
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function parseJsonOutput($sMessage, $sStatus = 'success')
    {
        $aOutput = [
            'msg'    => $sMessage,
            'status' => $sStatus,
        ];

        echo json_encode($aOutput);
        die;
    }

    /**
     * Get path to the directory of application which is being installed right
     * now.
     *
     * @access   private
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function getAppPath()
    {
        return PATH_ROOT.'application'.DS.APP_NAME;
    }

    /**
     * Set cached data.
     *
     * @access   private
     * @param    array $aValues
     * @return   void
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function setCached(array $aValues)
    {
        $sPath = '.'.DS.'cached.tmp';

        $this->aCached = $aValues;

        file_put_contents($sPath, serialize($aValues));
    }

    /**
     * Set single data to cached file.
     *
     * @access   private
     * @param    string $sKey
     * @param    string $sValue
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function setCachedSingle($sKey, $sValue)
    {
        $aContent        = $this->getCached();
        $aContent[$sKey] = $sValue;

        $this->setCached($aContent);
    }

    /**
     * Update cached data.
     *
     * @access   private
     * @param    array $aValues
     * @return   void
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function updateCached(array $aValues)
    {
        $sPath = '.'.DS.'cached.tmp';

        if(!file_exists($sPath)) {
            $this->setCached($aValues);
        } else {
            $aCached = $this->getCached();

            foreach($aValues as $sKey => $sValue) {
                $aCached[$sKey] = $sValue;
            }

            $this->aCached = $aCached;

            file_put_contents($sPath, serialize($aCached));
        }
    }

    /**
     * Check if cached data file exists.
     *
     * @access   private
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function cachedExists()
    {
        return file_exists('.'.DS.'cached.tmp');
    }

    /**
     * Get all cached setup data.
     *
     * @access   private
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function getCached()
    {
        $sPath   = '.'.DS.'cached.tmp';
        $aOutput = [];

        if(file_exists($sPath)) {
            $aOutput = unserialize(file_get_contents($sPath));
        }

        return $aOutput;
    }

    /**
     * Check if particular directory is empty.
     *
     * @static
     * @access   public
     * @param    string $sDir
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function isDirEmpty($sDir)
    {
        $oIterator = new \FilesystemIterator($sDir);

        return !$oIterator->valid();
    }

    /**
     * Check if superadmin user was created.
     *
     * @access   private
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function checkIfAdminCreated()
    {
        if(empty($this->aCached) || $this->aCached['step_done'] < 4) {
            return FALSE;
        }

        $oDbConn = new \PDO('mysql:host='.$this->aCached['db_host'].';dbname='.$this->aCached['db_name'], $this->aCached['db_user'], $this->aCached['db_pass']);
        $oQuery  = $oDbConn->query("SELECT id FROM users WHERE id = 1");

        if($oQuery === FALSE) {
            return FALSE;
        }

        $aUser = $oQuery->fetch();

        return !empty($aUser);
    }

}
