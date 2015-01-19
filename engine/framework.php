<?php
/**
 * Framework
 *
 * The centre-point of the system. Calls controller and includes template files
 * @author Peter McConnell <pemcconnell@gmail.com>
 *
 */
class Framework {

    private $console,
            $settings,
            $session,
            $mvc,
            $oController;

    public function __construct() {
        global $CONSOLE, $SETTINGS, $SESSION, $MVC;
        $this->console = & $CONSOLE;
        $this->settings = & $SETTINGS;
        $this->session = & $SESSION;
        $this->mvc = & $MVC;

        $this->callControllerMethods();
    }

    /**
     * callControllerMethods()
     *
     * Call the requested methods in the controller. Fallbacks included
     */
    public function callControllerMethods() {
        $this->oController = new $this->mvc['CONTROLLERCLASSNAME'];

        # DEFAULT METHOD
        $bControllerMethodCalled = false;
        if ($this->mvc['VIEW'] != $this->settings['mvc']['defaults']['view']) {
            // ATTEMPT TO CALL CUSTOM VIEW METHOD e.g. function view(){}
            if (is_callable(array($this->oController, $this->mvc['VIEW']))) {
                $this->oController->{$this->mvc['VIEW']}($this->mvc['ACTION']);
                $bControllerMethodCalled = true;
            }
        }
        if (!$bControllerMethodCalled) { // CALL DEFAULT METHOD
            $this->oController->{$this->settings['mvc']['defaults']['view']}($this->mvc['ACTION']);
        }

        # EXTENDED METHOD e.g. editExt() for edit()
        $extMethod = $this->mvc['VIEW'] . 'Ext';

        if (is_callable(array($this->oController, $extMethod))) {
            $this->oController->{$extMethod}($this->mvc['ACTION']);
        }
    }

    public function display() {
        $this->oController->controllerEnd();
        extract($this->oController->templatevars);
        $session = $this->session->get();

        ob_start();
        if (!isset($_GET['bodyonly']))
            include ($this->mvc['TEMPLATE']['HEADER']);
        include ($this->mvc['TEMPLATE']['BODY']);
        if (!isset($_GET['bodyonly']))
            include ($this->mvc['TEMPLATE']['FOOTER']);
        return ob_get_clean();
    }

    # MAGIC METHODS

    public function __set($name, $value) {
        if (isset($this->vars[$name]))
            $this->console->warning('overwrote $' . $name . ' using __set');
        $this->vars[$name] = $value;
    }

    public function __get($name) {
        if (isset($this->$name))
            return $this->$name;
        $this->console->warning("Tried to __get an unset variable (" . $name . ")");
        return '';
    }

    public function __destruct() {
        
    }

}

function __autoload($sClassName) {

    if (file_exists(BASE_PATH . 'plugins' . DS . strtolower($sClassName) . '.c.php')) {
        require_once(BASE_PATH . 'plugins' . DS . strtolower($sClassName) . '.c.php');
        return;
    }
    global $CONSOLE;
    $db = debug_backtrace();
    if (isset($db[1]) && isset($db[1]['function']) && ($db[1]['function'] == 'is_callable')) {
        $CONSOLE->warning('Attempted to autoload an invalid plugin (' . $sClassName . ') - This was caused by an "is_callable" method');
        return false;
    }
    $CONSOLE->error('Attempted to autoload an invalid plugin (' . $sClassName . ')');
}
