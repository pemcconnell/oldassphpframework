<?php

class Session {

    private $bIniated = false,
            $iRefreshEveryXMins = 10,
            $console = false,
            $session = false;

    /**
     * start
     * 
     * Initiate the session
     * 
     * @return null
     */
    public function start() {
        global $CONSOLE;
        $this->console = & $CONSOLE;
        if (!$this->bIniated) {
            session_start();
        }
        if (!isset($_SESSION[HREF]))
            $_SESSION[HREF] = array();
        $this->session = & $_SESSION[HREF]; // SITE SESSION ASSIGNMENT
        $this->bIniated = true;
        $this->runChecks();
    }

    /*
     * get()
     * 
     * params dynamic - crawl up session tree (multi-dimensional array)
     * 
     * @return mixed Value of requested session item
     */
    public function get() {
        if (!$this->bIniated)
            $this->start();
        $b = $this->session;
        $a = & $b;
        $n = func_num_args();
        for ($i = 0; $i < $n; $i++) {
            if (!isset($a[func_get_arg($i)]))
                $this->console->error(func_get_arg($i) . ' doesnt exist as a sess key');
            $a = & $a[func_get_arg($i)];
        }
        return $a;
    }

    /*
     * is_set()
     * 
     * Checks to see if session key is set.
     * 
     * @params dynamic - crawl up session tree (multi-dimensional array)
     * 
     * @return bool
     */

    public function is_set() {
        if (!$this->bIniated)
            $this->start();
        $a = $this->session;
        $n = func_num_args();
        for ($i = 0; $i < $n; $i++) {
            if (!isset($a[func_get_arg($i)]))
                return false;
            $a = & $a[func_get_arg($i)];
        }
        return true;
    }

    /*
     * set()
     * 
     * Note: Last dyamic param is value
     * 
     * @params dynamic - crawl up session tree (multi-dimensional array)
     * 
     * @return mixed
     */

    public function set() {
        $n = func_num_args();
        if ($n == 0)
            return false;
        if (!$this->bIniated)
            $this->start();
        $a = & $_SESSION[HREF];
        $v = func_get_arg($n - 1);
        --$n;
        for ($i = 0; $i < $n; $i++) {
            if (!isset($a[func_get_arg($i)])) {
                $a[func_get_arg($i)] = array();
            }
            $a = & $a[func_get_arg($i)];
        }
        $a = $v;
        return $a;
    }

    public function reset() {
        if (!$this->bIniated)
            $this->start();
        $a = & $this->session;
        $n = func_num_args();
        for ($i = 0; $i < $n; $i++) {
            if (!isset($a[func_get_arg($i)]))
                return false;
            $a = & $a[func_get_arg($i)];
        }

        $a = false;
        unset($a);
        if ($n == 0) { // RESTART
            $this->session = array(HREF);
            session_destroy();
            $this->start();
        }
    }

    private function runChecks() {
        // HELP AVOID SESSION ATTACKS (eg. Session fixation)
        if (!isset($_SESSION[HREF]) || !isset($_SESSION[HREF]['session'])) {
            if (!isset($_SESSION[HREF]))
                $_SESSION[HREF] = array();
            $_SESSION[HREF]['session'] = array(
                'created' => time()
            );
        } elseif ((time() - $_SESSION[HREF]['session']['created']) > ($this->iRefreshEveryXMins * 60)) {
            session_regenerate_id(true);
            $_SESSION[HREF]['session']['created'] = time();
        }
    }

}

$SESSION = new Session;