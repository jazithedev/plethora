<?php

namespace Plethora\Helper;

use Plethora\Helper;

/**
 * Encrypter helper for string encrypting
 *
 * @package        Plethora
 * @subpackage     Helper
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Arrays extends Helper
{

    /**
     * @var  string  default delimiter for path()
     */
    public static $delimiter = '.';

    /**
     * Gets the value from an array. If doesn't exists, return default value.
     *
     * @static
     * @access   public
     * @param    $array    array
     * @param    $key      string
     * @param    $default  string
     * @return   null|string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function get(array $array, $key, $default = NULL)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    /**
     * Retrieves multiple keys from an array. If the key does not exist in the
     * array, the default value will be added instead.
     *
     *     // Get the values "username", "password" from $_POST
     *     $auth = Arr::extract($_POST, array('username', 'password'));
     *
     * @static
     * @access   public
     * @param    $aArray    array   array to extract keys from
     * @param    $aKeys     array   list of key names
     * @param    $mDefault  mixed   default value
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function extract(array $aArray, array $aKeys, $mDefault = NULL)
    {
        $aFound = [];

        foreach($aKeys as $sKey) {
            $aFound[$sKey] = isset($aArray[$sKey]) ? $aArray[$sKey] : $mDefault;
        }

        return $aFound;
    }

    /**
     * Create multidimensional array.
     *
     * @static
     * @access   public
     * @param    array  $array
     * @param    string $sKeys
     * @param    mixed  $mValue
     * @param    string $sSeparator
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function createMultiKeys(array &$array, $sKeys, $mValue, $sSeparator = '.')
    {
        foreach(explode($sSeparator, $sKeys) as $sNode) {
            if(!isset($array[$sNode])) {
                $array[$sNode] = [];
            }

            $array = &$array[$sNode];
        }

        $array = $mValue;
    }

    /**
     * Gets a value from an array using a dot separated path.
     *
     *     // Get the value of $array['foo']['bar']
     *     $value = Arr::path($array, 'foo.bar');
     *
     * Using a wildcard "*" will search intermediate arrays and return an array.
     *
     *     // Get the values of "color" in theme
     *     $colors = Arr::path($array, 'theme.*.color');
     *
     *     // Using an array of keys
     *     $colors = Arr::path($array, array('theme', '*', 'color'));
     *
     * @static
     * @access    public
     * @param     $array     array   array to search
     * @param     $path      mixed   key path string (delimiter separated) or array of keys
     * @param     $default   mixed   default value if the path is not set
     * @param     $delimiter string  key path delimiter
     * @return    mixed
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public static function path(array $array, $path, $default = NULL, $delimiter = NULL)
    {
        if(!is_array($array)) {
            return $default; // This is not an array!
        }

        if(is_array($path)) {
            $aKeys = $path; // The path has already been separated into keys
        } else {
            if(array_key_exists($path, $array)) {
                return $array[$path]; // No need to do extra processing
            }

            if($delimiter === NULL) {
                $delimiter = self::$delimiter; // Use the default delimiter
            }

            // Remove starting delimiters and spaces
            $path = ltrim($path, "{$delimiter} ");

            // Remove ending delimiters, spaces, and wildcards
            $path = rtrim($path, "{$delimiter} *");

            // Split the keys by delimiter
            $aKeys = explode($delimiter, $path);
        }
        do {
            $key = array_shift($aKeys);

            if(ctype_digit($key)) {
                $key = (int)$key; // Make the key an integer
            }

            if(isset($array[$key])) {
                if($aKeys) {
                    if(is_array($array[$key])) {
                        $array = $array[$key]; // Dig down into the next part of the path
                    } else {
                        break; // Unable to dig deeper
                    }
                } else {
                    return $array[$key]; // Found the path requested
                }
            } elseif($key === '*') {
                // Handle wildcards

                $values = [];
                foreach($array as $arr) {
                    if($value = self::path($arr, implode('.', $aKeys))) {
                        $values[] = $value;
                    }
                }

                if($values) {
                    return $values; // Found the values requested
                } else {
                    break; // Unable to dig deeper
                }
            } else {
                break; // Unable to dig deeper
            }
        } while($aKeys);

        // Unable to find the value requested
        return $default;
    }

    /**
     * Check if array has all of the given keys.
     *
     * @static
     * @access   public
     * @param    array $aArray
     * @param    array $aKeys
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function hasKeys(array $aArray, array $aKeys)
    {
        foreach($aKeys as $sKey) {
            if(!isset($aArray[$sKey])) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Get first value of an array.
     *
     * @static
     * @access   public
     * @param    array $aArray
     * @return   mixed
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function first(array $aArray)
    {
        return reset($aArray);
    }

    /**
     * Get last value of an array.
     *
     * @static
     * @access   public
     * @param    array $aArray
     * @return   mixed
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function last(array $aArray)
    {
        return end($aArray);
    }

    /**
     * Deep merging with keys overwriting.
     *
     * @static
     * @access   public
     * @param    array $a
     * @param    array $b
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function merge(array &$a, array $b)
    {
        foreach($b as $child => $value) {
            if(isset($a[$child])) {
                if(is_array($a[$child]) && is_array($value)) {
                    static::merge($a[$child], $value);
                }
            } else {
                $a[$child] = $value;
            }
        }

        return $a;
    }

    /**
     * An in_array() equivalent but get an array as first argument. If any
     * element from $aNeedle array exists in $aHaystack, method will return
     * TRUE. In other case, FALSE.
     *
     * @access   public
     * @param    array $aNeedle
     * @param    array $aHaystack
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function anyInArray($aNeedle, $aHaystack)
    {
        foreach($aNeedle as $mValue) {
            if(in_array($mValue, $aHaystack)) {
                return TRUE;
            }
        }

        return FALSE;
    }

}
