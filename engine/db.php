<?php

/**
 * db.php
 *
 * Creates database connection. Currently configured for PDO
 *
 */
class DB
{
	protected	$dbh,
				$console;

	public function __construct()
	{
		global $SETTINGS, $CONSOLE;

		$this->console =& $CONSOLE;

		try {
			$this->dbh = new PDO(	"mysql:host=" . $SETTINGS['db']['host'] . ";dbname=" . $SETTINGS['db']['database'],
									$SETTINGS['db']['username'] ,
									$SETTINGS['db']['password']);
			if(LOCALMODE)
			{
				$this->dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} else {
				$this->dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
			}
		} catch(PDOException $e) {
			$erparams = array();
			$erparams['errstr'] = $e->getMessage();
			$erparams['errno'] = $e->getCode();
			$erparams['errfile'] = __FILE__;
			$erparams['errline'] = __LINE__;

			$ermsg = '<strong>Could not connect to ' . $SETTINGS['db']['database'] . '</strong>';

			$this->console->error($ermsg, $erparams);
			return false;
		}
	}

	public function query($sql, $params = array())
	{
        $sql = trim($sql);
		try {
			$ST = $this->dbh->prepare($sql);
			if(count($params)!=0)
			{
				foreach($params as $key => $v)
				{
					$ST->bindParam($key, $params[$key], PDO::PARAM_STR);
				}
			}
			if(preg_match('@^SELECT@', strtoupper($sql)) !== false)
			{
				$ST->setFetchMode(PDO::FETCH_ASSOC);
			}
			$ST->execute();
            return $ST;
		} catch(PDOException $e) {
			return $this->queryError($sql, $params, $e, debug_backtrace());
		}
	}

	public function lastId()
	{
		return $this->dbh->lastInsertId();
	}

	public function foundRows()
	{
	    $sql = "SELECT FOUND_ROWS() as total;";
	    $sql = $this->query($sql);
	    $row = $sql->fetch();
	    return $row['total'];
	}

	private function queryError($sql, $params, $e, $bt)
	{
		$erparams = array();
		$erparams['errstr'] = $e->getMessage();
		$erparams['errno'] = $e->getCode();
		$erparams['errfile'] = $bt[0]['file'];
		$erparams['errline'] = $bt[0]['line'];

		$ermsg = '<strong>DB Query Error</strong><br />' . $erparams['errstr'] . '<br /><strong>Query:</strong><br />' . $sql . '<br /><strong>Arguments (' . count($params) . '):</strong><br />' . implode('<br />', $params);

		$this->console->error($ermsg, $erparams);
		return false;
	}

	function __destruct()
	{
		$this->dbh = null;
	}
}