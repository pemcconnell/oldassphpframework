<?php
if (!isset($_GET['uri'])) { // SHOULD NEVER HAPPEN, BUT ACTS AS A FAILSAFE
    $_GET['uri'] = '';
}

// HTACCESS FAIL / ZEUS SUPPORT
if (strpos($_GET['uri'], '?')!==false) {
    $aP = explode('?', $_GET['uri'], 2);
    $_GET['uri'] = $aP[0];
    $gs = array($aP[1]);
    if (strpos($aP[1], '&')!==false) {
        $gs = explode('&', $aP[1]);
    }
    foreach ($gs as $gstr) {
        $a = explode('=', $gstr);
        $_GET[$a[0]] = isset($a[1]) ? $a[1] : '';
    }
}

$GBL_apcPageKey = serialize($_GET);
//apc_clear_cache();
//Uncomment to force zero cache for active page
//APC::delete('apcMVC_' . $GBL_apcPageKey);

if (!($MVC = APC::fetch('apcMVC_' . $GBL_apcPageKey))) {
    // INIT
    $MVC = array(
        'APCPAGEKEY' => $GBL_apcPageKey,
        'ZONE' => $SETTINGS['mvc']['defaults']['zone'],
        'CONTROLLER' => $SETTINGS['mvc']['defaults']['controller'],
        'VIEW' => $SETTINGS['mvc']['defaults']['view'],
        'ACTION' => '',
        'TEMPLATE' => array(
            'HEADER' => '',
            'FOOTER' => '',
            'BODY' => ''
        ),
        'OTHER' => array(),
        'QUERYSTRING' => $_SERVER['QUERY_STRING']
    );

    // WORK OUT DESIRED MVC PATTERN
    $uri = $_GET['uri'];

    $MVC['PRE-ROUTER'] = array();

    if ($uri != '') {
        $prerouter = $MVC['PRE-ROUTER'];

        /**
         * splitUri breaks the URL apart so that the framework 
         * knows which files to get
         * 
         * @param string $sUri       current url
         * @param string $sArrayName name of the global mvc var
         *
         * @return null
         */
        function splitUri($sUri, $sArrayName = 'MVC') 
        {
            global $$sArrayName, $SETTINGS;
            $MVC = & $$sArrayName;

            $hasSlash = (strpos($sUri, '/') !== false);
            $isZone = in_array(strtolower($sUri), $SETTINGS['mvc']['zones']);

            if ($hasSlash || $isZone) {
                if ($hasSlash) {
                    $aUriParts = explode('/', $sUri);
                } else {
                    $aUriParts = array($sUri);
                }
                if (in_array(strtolower($aUriParts[0]), $SETTINGS['mvc']['zones'])) {
                    $MVC['ZONE'] = strtolower($aUriParts[0]);
                    array_shift($aUriParts);
                }
                if (isset($aUriParts[0]) && ($aUriParts[0] != '')) {
                    $MVC['CONTROLLER'] = strtolower($aUriParts[0]);
                    array_shift($aUriParts);
                    if (isset($aUriParts[0]) && ($aUriParts[0] !== '')) {
                        $MVC['VIEW'] = strtolower($aUriParts[0]);
                        array_shift($aUriParts);
                        if (isset($aUriParts[0]) && ($aUriParts[0] != '')) {
                            $MVC['ACTION'] = $aUriParts[0];
                            array_shift($aUriParts);
                            $MVC['OTHER'] = $aUriParts;
                        }
                    }
                } else {
                    $MVC['CONTROLLER'] = $SETTINGS['mvc']['defaults']['homecontroller'];
                }
            } else {
                $MVC['CONTROLLER'] = $sUri;
            }
        }

        // SPLIT UP URL & CHECK TO SEE IF IT NEEDS ROUTED
        $routedUri = false;
        if (!isset($ROUTER[$uri])) {
            $routedUri = routerMethod($uri);
            if (!$routedUri) {
                foreach ($ROUTER as $k => $v) {
                    if (preg_match('/^' . $k . '(.*)?/', $uri)) {
                        $routedUri = preg_replace(
                            '/^' . $k . '(.*)?/', $v . '\1', $uri
                        );
                    }
                }
            }
        } else {
            $routedUri = $ROUTER[$uri];
        }
        if (!$routedUri) {
            splitUri($uri);
        } else {
            splitUri($routedUri);
            splitUri($uri, 'prerouter');
        }
    } else {
        $MVC['CONTROLLER'] = $SETTINGS['mvc']['defaults']['homecontroller'];
    }

    $TEMPLATEMVC = $MVC;

    // SEEK DESIRED FILES. SET FALLBACKS IF NEEDED
    // CONTROLLER TEMPLATE
    $controllerPath =       BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                            $MVC['CONTROLLER'] . '.php';
    if (!file_exists($controllerPath)) {
        $controllerPath =   BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                            $SETTINGS['mvc']['defaults']['controller'] . '.php';
    }
    // CONTROLLER CLASS
    $controllerClassName = ucfirst($MVC['CONTROLLER']) . 'Controller';
    $controllerClassPath =  BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                            $MVC['CONTROLLER'] . '._controller.php';
    // ZONE
    $MVC['ZONECONTROLLERPATH'] =    BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                                    '_basecontroller.php';
    
    if (!file_exists($MVC['ZONECONTROLLERPATH'])) {
        $MVC['ZONECONTROLLERPATH'] = '';
    }
    
    if (!file_exists($controllerClassPath)) { // e.g. admin/category._controller.php
        $foundController = false;
        if ($MVC['CONTROLLER'] == $SETTINGS['mvc']['defaults']['homecontroller']) {
            // Attempted to find a 'home' controller which failed. 
            // Attempt 'default' controller
            $controllerClassPath =  BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                                    $SETTINGS['mvc']['defaults']['controller'] . 
                                    '._controller.php';
            if (file_exists($controllerClassPath)) {
                $MVC['CONTROLLER'] = $SETTINGS['mvc']['defaults']['controller'];
                $controllerClassName = ucfirst($MVC['CONTROLLER']) . 'Controller';
                $foundController = true;
            }
        }
        if (!$foundController) {
            $controllerClassName = ucfirst($MVC['ZONE']) . 'BaseController';
            $controllerClassPath =  BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                                    '_basecontroller.php';
            if ($MVC['ZONECONTROLLERPATH'] == '') { // e.g. admin/_basecontroller.php
                if ($MVC['ZONE'] != $SETTINGS['mvc']['defaults']['zone']) {
                    $controllerClassName = ucfirst(
                        $SETTINGS['mvc']['defaults']['zone']
                    ) . 'BaseController';
                    $controllerClassPath =  BASE_PATH . 'mvc' . DS . 
                                            $SETTINGS['mvc']['defaults']['zone'] . 
                                            DS . '_basecontroller.php';
                    // e.g. otherzone/_basecontroller.php
                    if (!file_exists($controllerClassPath)) { 
                        $controllerClassName = 'BaseController';
                        $controllerClassPath =  BASE_PATH . 'engine' . DS . 
                                                '_basecontroller.php';
                    }
                } else {
                    $controllerClassName = 'BaseController';
                    $controllerClassPath =  BASE_PATH . 'engine' . DS . 
                                            '_basecontroller.php';
                }
            }
        }
    }
    $MVC['CONTROLLERCLASSNAME'] = $controllerClassName;
    $MVC['CONTROLLERCLASSPATH'] = $controllerClassPath;
    
    
    $MVC['MODELCLASSNAME'] = 'BaseModel';
    $MVC['ZONEMODELPATH'] = $MVC['MODELPATH'] = '';
    
    // ZONE LEVEL e.g. ./mvc/admin/_basemodel.php
    if (file_exists(BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . '_basemodel.php')) {
        $MVC['ZONEMODELPATH'] =     BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                                    '_basemodel.php';
        $MVC['MODELCLASSNAME'] = ucfirst($MVC['ZONE']) . 'BaseModel';
    }
    // CONTROLLER LEVEL e.g. ./mvc/admin/pages._model.php
    if (strpos($controllerClassPath, '._controller.php') !== false) {
        $f = str_replace(
            '._controller.php', '._model.php', $controllerClassPath
        );
        if (file_exists($f)) {
            $MVC['MODELPATH'] = $f;
            $MVC['MODELCLASSNAME'] = ucfirst($MVC['CONTROLLER']) . 'Model';
        }
    }

    // WORK OUT TEMPLATE PATHS
    // HEADER
    $headerpath = false;
    if ($TEMPLATEMVC['CONTROLLER'] != $MVC['CONTROLLER']) {
        $headerpath =   BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                        $TEMPLATEMVC['CONTROLLER'] . '._header.php';
        if (!file_exists($headerpath)) {
            $headerpath = false;
        }
    }
    if (!$headerpath) {
        $headerpath =   BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                        $SETTINGS['mvc']['defaults']['controller'] . 
                        '._header.php';
        $f =    BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                $MVC['CONTROLLER'] . '.' . $MVC['VIEW'] . '._header.php';
        $fb =   BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                $MVC['CONTROLLER'] . '._header.php';
        if (file_exists($f)) {
            // VIEW-LEVEL HEADER e.g. /admin/pages.edit._header.php
            $headerpath = $f;
        } elseif (file_exists($fb)) {
            // CONTROLLER-LEVEL HEADER e.g. /admin/pages._header.php
            $headerpath = $fb;
        }
    }
    // FOOTER
    $footerpath = false;
    if ($TEMPLATEMVC['CONTROLLER'] != $MVC['CONTROLLER']) {
        $footerpath =   BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                        $TEMPLATEMVC['CONTROLLER'] . '._footer.php';
        if (!file_exists($footerpath)) {
            $footerpath = false;
        }
    }
    if (!$footerpath) {
        $footerpath =   BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                        $SETTINGS['mvc']['defaults']['controller'] . '._footer.php';
        $f =    BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                $MVC['CONTROLLER'] . '.' . $MVC['VIEW'] . 
                '._footer.php';
        $fb =   BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                $MVC['CONTROLLER'] . '._footer.php';
        if (file_exists($f)) {
            // VIEW-LEVEL FOOTER e.g. /admin/pages.edit._footer.php
            $footerpath = $f;
        } elseif (file_exists($fb)) {
            // CONTROLLER-LEVEL FOOTER e.g. /admin/pages._footer.php
            $footerpath = $fb;
        }
    }
    // BODY
    $bodypath = false;
    if ($TEMPLATEMVC['CONTROLLER'] != $MVC['CONTROLLER']) {
        $bodypath = BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                    $TEMPLATEMVC['CONTROLLER'] . '.php';
        if (!file_exists($bodypath)) {
            $bodypath = false;
        } else {
            $MVC['CONTROLLER'] = $TEMPLATEMVC['CONTROLLER'];
        }
    }
    if (!$bodypath) {
        $bodypath = BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                    $MVC['CONTROLLER'] . '.php';
        $f =    BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                $MVC['CONTROLLER'] . '.' . $MVC['VIEW'] . '.php';
        if (($MVC['VIEW'] != $SETTINGS['mvc']['defaults']['view']) && file_exists($f)) {
            $bodypath = $f;
        } elseif (!file_exists($bodypath)) {
            $bodypath = BASE_PATH . 'mvc' . DS . $MVC['ZONE'] . DS . 
                        $SETTINGS['mvc']['defaults']['controller'] . '.php';
        }
    }

    $MVC['TEMPLATE']['HEADER'] = $headerpath;
    $MVC['TEMPLATE']['FOOTER'] = $footerpath;
    $MVC['TEMPLATE']['BODY'] = $bodypath;

    // MAKE SURE PRE-ROUTER HAS ALL AVAILABLE FIELDS
    foreach ($MVC as $k => $v) {
        if (!isset($MVC['PRE-ROUTER'][$k])) {
            $MVC['PRE-ROUTER'][$k] = $v;
        }
    }

    $MVC['CONTROLLERCLASSNAME'] = str_replace('-', '_', $MVC['CONTROLLERCLASSNAME']);
    $MVC['MODELCLASSNAME'] = str_replace('-', '_', $MVC['MODELCLASSNAME']);

    // CLEAN UP
    unset(
        $controllerClassName, $prerouter, $controllerClassPath, $headerpath, 
        $footerpath, $bodypath, $uri, $hasSlash, $isZone, $aUriParts, 
        $foundController, $TEMPLATEMVC, $f, $fb
    );

    APC::store('apcMVC_' . $GBL_apcPageKey, $MVC);
}

// MODELS
require BASE_PATH . 'engine' . DS . '_basemodel.php';
if ($MVC['ZONEMODELPATH'] != '') {
    include $MVC['ZONEMODELPATH'];
}
if ($MVC['MODELPATH'] != '') {
    include $MVC['MODELPATH'];
}

// CONTROLLERS
require BASE_PATH . 'engine' . DS . '_basecontroller.php';
if ($MVC['ZONECONTROLLERPATH'] != '') {
    include $MVC['ZONECONTROLLERPATH'];
}
if ($MVC['CONTROLLERCLASSPATH'] != $MVC['ZONECONTROLLERPATH']) {
    include $MVC['CONTROLLERCLASSPATH'];
}