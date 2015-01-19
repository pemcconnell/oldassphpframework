<?php
/**
 * apc.c.php
 * 
 * A light APC caching class
 * 
 * @author Peter McConnell <pemcconnell@googlemail.com>
 */

class APC
{
    public static 	$bEnabled = false; // Boolean indicator of apc being enabled
    
    private static	$_iTTL = 1200;     // Lifetime of cache
    
    /**
     * init 
     * 
     * Initiates the class. This is called from the bottom of the apc.c.php file
     * 
     * @return bool Returns bool state of the APC extension being enabled 
     */
    public function init() {
        self::$bEnabled = extension_loaded('apc');
        return self::$bEnabled;
    }
    
    /**
     * fetch
     * 
     * Retrieves cached bytecode
     * 
     * @param string $sKey Key name of the cached bytecode
     * 
     * @return mixed Returns bytecode if found, null if not
     */
    static public function fetch($sKey) {
        $bRes = false;
        $vData = apc_fetch($sKey, $bRes);
        return ($bRes) ? $vData :null;
    }
    
    /**
     * store
     * 
     * Stores bytecode to the cache
     * 
     * @param string $sKey  Key name of the cache reference
     * @param mixed  $vData Value to be cached.
     * 
     * @return mixed Returns the value of the bytecode as it was cached
     */
    static public function store($sKey, $vData) {
    	return apc_store($sKey, $vData, self::$_iTTL);
    }
    
    /**
     * delete
     * 
     * Deletes bytecode from the cache
     * 
     * @param string $sKey  Key name of the cache reference
     * 
     * @return bool Returns true
     */
    static public function delete($sKey)
    {
        $bRes = false;
        apc_fetch($sKey, $bRes);
        return ($bRes) ? apc_delete($sKey) : true;
    }
    
    /**
     * createKey
     * 
     * Deletes bytecode from the cache
     * 
     * @param string $sKey    Key name of the cache reference
     * @param array  $aParams Array of any associated parameters. This is so as 
     * to ensure the value is relevant to the data being handled.
     * 
     * @return string Returns the key name
     */
    static public function createKey($sKey, $aParams = array())
    {
	    foreach($aParams as $v)
	    {
		    $sKey .= '__';
		    if(is_string($v) || is_numeric($v)) $sKey .= $v;
		    elseif(is_array($v)) $sKey .= serialize($v);
		    else $sKey .= $v;
	    }
	    return $sKey;
    }
}

if(!APC::init())
{
	die('APC not installed');
}