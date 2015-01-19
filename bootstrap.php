<?php

// COMMON PLUGINS
require (BASE_PATH . 'plugins' . DS . 'apc.c.php');
require (BASE_PATH . 'plugins' . DS . 'html.c.php');

// ENGINE
require (BASE_PATH . 'inc' . DS . 'env.php');
require (BASE_PATH . 'settings.php');
require (BASE_PATH . 'engine' . DS . 'console.php');
require (BASE_PATH . 'engine' . DS . 'session.php');
require (BASE_PATH . 'inc' . DS . 'functions.php');
require (BASE_PATH . 'router.php');
require (BASE_PATH . 'engine' . DS . 'mvc.php'); // CREATES $MVC GLOBAL
require (BASE_PATH . 'engine' . DS . 'framework.php');
