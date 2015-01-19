<?php

/**
  * General global functions
  */

/**
 * debugA
 * 
 * Debug an item. Supports all datatypes
 * 
 * @param mixed $a        Item to be debugged
 * @param bool $bDontDie  Boolean toggle the die();
 * 
 * @return null
 */
function debugA($a, $bDontDie = false)
{
	echo '<hr />Top Count: <strong>' . count($a) . '</strong>';
	echo '<pre>';
            if(is_array($a)) {
                print_r($a);
            } elseif(is_object($a)) {
                var_dump($a);
            } else {
                echo htmlspecialchars($a);
            }
	echo '</pre>';
	if(!$bDontDie) die();
}