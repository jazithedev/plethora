<?php

namespace Plethora;

use Doctrine;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\XcacheCache;

/**
 * Simplified DataBase queries handler using Doctrine's EntityManager.
 *
 * @package          Plethora
 * @author           Krzysztof Trzos
 * @copyright    (c) 2016, Krzysztof Trzos
 * @since            1.0.0-alpha
 * @version          1.0.0-alpha
 */
class DB
{
    /**
     * Variable which stores Doctrine's EntityManager object
     *
     * @static
     * @access  private
     * @var     \Doctrine\ORM\EntityManager
     * @since   1.0.0-alpha
     */
    private static $entityManager = NULL;

    /**
     * Stores Query object for particular instance of DB class
     *
     * @access  private
     * @var     Doctrine\ORM\Query
     * @since   1.0.0-alpha
     */
    private $oQuery = NULL;

    /**
     * Last result done by DB class
     *
     * @static
     * @access  public
     * @var     mixed
     * @since   1.0.0-alpha
     */
    public static $mResult;

    /**
     * Contains all models from self::create() or self::update()
     *
     * @static
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private static $modelsDirectories = [];

    /**
     * List of all Models paths.
     *
     * @static
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private static $modelsPaths = [];

    /**
     * List of all Models names.
     *
     * @static
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private static $modelsNames = [];

    /**
     * Constructor
     *
     * @access   public
     * @param    Doctrine\ORM\Query $oQuery
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct(Doctrine\ORM\Query $oQuery)
    {
        $this->oQuery = $oQuery;
    }

    /**
     * Factory method
     *
     * @static
     * @access   public
     * @param    Doctrine\ORM\Query $oQuery
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory(Doctrine\ORM\Query $oQuery)
    {
        return new DB($oQuery);
    }

    /**
     * Doctrine model loader
     *
     * @static
     * @access   private
     * @return   array  models
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private static function loadModels()
    {
        foreach(Router::getModules() as $aModuleData) {
            $sPathToModels = PATH_MODULES.$aModuleData['path'].DS.'classes'.DS.'Model';

            if(file_exists($sPathToModels)) {
                static::scanModelDir($sPathToModels, '\\Model');
            }
        }

        return static::$modelsDirectories;
    }

    /**
     * Scan directories for Models.
     *
     * @static
     * @access   private
     * @param    string $pathToModels
     * @param    string $class
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private static function scanModelDir($pathToModels, $class)
    {
        static::$modelsDirectories[] = $pathToModels;

        foreach(scandir($pathToModels) as $file) {
            if(!in_array($file, ['.', '..'])) {
                $pathToFile = $pathToModels.DS.$file;

                if(!is_dir($pathToFile)) {
                    static::$modelsPaths[] = $pathToFile;
                    static::$modelsNames[] = $class.'\\'.str_replace('.php', '', $file);

                    # check if particular model file exists in application (is overwritten
                    # on the purpose of this application)
                    $modelDir     = str_replace(['/', '\\'], [DS, DS], $class);
                    $modelAppPath = PATH_APP.'classes'.$modelDir.DS.$file;
                    $existInApp   = file_exists($modelAppPath);

                    # require model file
                    if($existInApp) {
                        require_once $modelAppPath;
                    } else {
                        require_once $pathToFile;
                    }
                } else {
                    static::scanModelDir($pathToFile, $class.'\\'.$file);
                }
            }
        }
    }

    /**
     * Gets Model class metadata.
     *
     * @static
     * @access   public
     * @param    string $className
     * @return   \Doctrine\ORM\Mapping\ClassMetadata
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function metadata($className)
    {
        return static::getEntityManager()->getClassMetadata($className);
    }

    /**
     * Assigns Doctrine's EntityManager to this class
     *
     * @static
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function create()
    {
        if(!empty(self::$modelsDirectories)) {
            return TRUE;
        }

        static::loadModels();

        if(is_null(self::$entityManager)) {
            // the connection configuration
            $dbParams = Config::get('database.config.'.Config::get('base.mode'));

            // Set up caches
            if(Config::get('base.mode') === 'production') {
                $cache     = new ArrayCache;
                $isDevMode = FALSE;
            } else {
                $cache     = new ArrayCache;
                $isDevMode = TRUE;
            }

            $config = Setup::createAnnotationMetadataConfiguration(static::$modelsDirectories, $isDevMode);
            $config->setMetadataCacheImpl($cache);
            $config->setQueryCacheImpl($cache);
            $config->setSQLLogger(new Doctrine\DBAL\Logging\DebugStack());

            // Proxy configuration
            $config->setProxyDir(Config::get('database.proxy_dir'));
            $config->setProxyNamespace(Config::get('database.proxy_namespace'));

            $entityManager = EntityManager::create($dbParams, $config);

            static::$entityManager = $entityManager;
        }

        return TRUE;
    }

    /**
     * Returns array with models names.
     *
     * @static
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getModelsNames()
    {
        return static::$modelsNames;
    }

    /**
     * Returns array with models paths.
     *
     * @static
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function getModelsPaths()
    {
        return static::$modelsPaths;
    }

    /**
     * Find entity by id.
     *
     * @static
     * @access   public
     * @param    string  $sModel
     * @param    integer $iId
     * @return   ModelCore
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function find($sModel, $iId)
    {
        return self::$entityManager->find($sModel, $iId);
    }

    /**
     * Creates Query object
     *
     * @static
     * @access   public
     * @param    string $sQuery
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function query($sQuery)
    {
        $oQuery = self::$entityManager->createQuery($sQuery);

        return static::factory($oQuery);
    }

    /**
     * Create list of entities.
     *
     * @static
     * @access   public
     * @param    string $sTable
     * @param    mixed  $mWhere
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function queryList($sTable, $mWhere = NULL)
    {
        $oQueryBuilder = static::queryBuilder()
            ->select('t')
            ->from($sTable, 't');

        if(!empty($mWhere)) {
            $oQueryBuilder->where($mWhere);
        }

        return static::factory($oQueryBuilder->getQuery());
    }

    /**
     * Create query with counted amount of entities as a result.
     *
     * @static
     * @access   public
     * @param    string $sTable
     * @param    mixed  $mWhere
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function count($sTable, $mWhere = NULL)
    {
        $oQueryBuilder = static::queryBuilder()
            ->select('COUNT(t)')
            ->from($sTable, 't');

        if(!is_null($mWhere)) {
            $oQueryBuilder->where($mWhere);
        }

        $aResult = static::factory($oQueryBuilder->getQuery())->execute();

        return array_shift($aResult);
    }

    /**
     * Get query builder.
     *
     * @static
     * @access   public
     * @return   \Doctrine\ORM\QueryBuilder
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function queryBuilder()
    {
        return static::$entityManager->createQueryBuilder();
    }

    /**
     * Sets params value in a query object
     *
     * @access   public
     * @param    string $sParamName
     * @param    string $mParamValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function param($sParamName, $mParamValue)
    {
        $this->oQuery->setParameter($sParamName, $mParamValue);

        return $this;
    }

    /**
     * Set query parameters.
     *
     * @access   public
     * @param    array $aParams
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function params(array $aParams)
    {
        foreach($aParams as $aParam) {
            $this->param($aParam[0], $aParam[1]);
        }

        return $this;
    }

    /**
     * Creates LIMIT clause.
     *
     * @access   public
     * @param    integer $iVal
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function limit($iVal)
    {
        $this->oQuery->setMaxResults($iVal);

        return $this;
    }

    /**
     * Creates offset.
     *
     * @access   public
     * @param    integer $iVal
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function offset($iVal)
    {
        $this->oQuery->setFirstResult($iVal);

        return $this;
    }

    /**
     * Get query object.
     *
     * @access   public
     * @return   Doctrine\ORM\Query
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getQuery()
    {
        return $this->oQuery;
    }

    /**
     * Executes query and saving query SQL to list (if needed)
     *
     * @access   public
     * @param    boolean $noResults returns no results, if TRUE
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function execute($noResults = FALSE)
    {
        if(!$noResults) {
            static::$mResult = $this->oQuery->getResult();
        } else {
            static::$mResult = $this->oQuery->execute();
        }

        return static::$mResult;
    }

    /**
     * Get list of values.
     *
     * @access   public
     * @param    string $sKey
     * @param    string $sValue
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getList($sKey, $sValue)
    {
        static::$mResult = $this->oQuery->getArrayResult();

        $aResults = [];
        foreach(static::$mResult as $mResult) {
            $aResults[$mResult[$sKey]] = $mResult[$sValue];
        }

        return $aResults;
    }

    /**
     * Returns single result from query
     *
     * @access   public
     * @param    bool $bAsArray
     * @return   mixed
     * @throws   Doctrine\ORM\NonUniqueResultException
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function single($bAsArray = FALSE)
    {
        $this->limit(1);

        try {
            if($bAsArray) {
                self::$mResult = $this->oQuery->getSingleResult(2);
            } else {
                self::$mResult = $this->oQuery->getSingleResult();
            }
        } catch(Doctrine\Orm\NoResultException $e) {
            self::$mResult = FALSE;
        }

        return self::$mResult;
    }

    /**
     * Making database flush (saving all changes)
     *
     * @static
     * @access    public
     */
    public static function flush()
    {
        self::$entityManager->flush();
    }

    /**
     * Usuwanie rekordu o identyfikatorze $iId w modelu $sModel za pomocą
     * klauzuli DELETE. Zwraca TRUE, jeżeli rekord został prawidłowo usunięty.
     *
     * @static
     * @access   public
     * @param    integer $iId
     * @param    string  $sModel
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function delete($iId, $sModel)
    {
        DB::query("DELETE FROM ".$sModel." m WHERE m.id = :id")->param('id', $iId)->execute(TRUE);

        return DB::$mResult ? TRUE : FALSE;
    }

    /**
     * Deleting objects with Doctrine's remove() help;
     *
     * @static
     * @access   public
     * @param    ModelCore $oEntity
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function remove($oEntity)
    {
        DB::$entityManager->remove($oEntity);
    }

    /**
     * Transmission of the new object to the database.
     *
     * @static
     * @access   public
     * @param    ModelCore $oEntity
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function persist($oEntity)
    {
        DB::$entityManager->persist($oEntity);
    }

    /**
     * Gets a reference to the entity identified by the given type and identifier
     * without actually loading it, if the entity is not yet loaded.
     *
     * @static
     * @access    public
     * @param    string  $sModel The name of the entity type.
     * @param    integer $iId    The entity identifier.
     * @return    object The entity reference.
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public static function getReference($sModel, $iId)
    {
        return DB::$entityManager->getReference($sModel, $iId);
    }

    /**
     * Gets the database connection object used by the EntityManager.
     *
     * @static
     * @access    public
     * @return    \Doctrine\DBAL\Connection
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public static function getConnection()
    {
        return DB::$entityManager->getConnection();
    }

    /**
     * Get entity manager.
     *
     * @static
     * @access    public
     * @return    \Doctrine\ORM\EntityManager
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public static function getEntityManager()
    {
        return DB::$entityManager;
    }

    /**
     * Add conditions to query.
     *
     * @access    public
     * @param    mixed $mPredicates The restriction predicates.
     * @return    DB
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function where($mPredicates)
    {
        $this->oQuery->getEntityManager()->createQueryBuilder()->where($mPredicates);

        return $this;
    }

    /**
     * Return result of last query.
     *
     * @static
     * @access    public
     * @param    string $sKey
     * @return    mixed
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public static function result($sKey = NULL)
    {
        return empty($sKey) ? static::$mResult : static::$mResult[$sKey];
    }

}
