<?php


/**
 * WorkFlowEngine - class.Wfengine.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatic generated with ArgoUML 0.24 on 11.08.2008, 09:28:22
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000816-includes begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000816-includes end

/* user defined constants */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000816-constants begin


/*
define("PASS", "taoqual", true);
define("MODULE", "taoqual", true);
*/
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000816-constants end

/**
 * Short description of class Wfengine
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Wfengine
{
    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var Wfengine
     */
    private static $instance = null;

    /**
     * Short description of attribute sessionGeneris
     *
     * @access public
     * @var object
     */
    public $sessionGeneris = null;

    /**
     * Short description of attribute user
     *
     * @access public
     * @var WfUser
     */
    public $user = null;

    /**
     * Short description of attribute login
     *
     * @access public
     * @var string
     */
    public $login = '';

    // --- OPERATIONS ---

    /**
     * Short description of method getProcessDefinitions
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getProcessDefinitions()
    {
        $returnValue = array();

        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000819 begin
		$processes = getInstances($this->sessionGeneris,array(CLASS_PROCESS),array(""));
		foreach ($processes["pDescription"] as $key=>$val)
			{
				$process = new ViewProcess($key);
				$returnValue[] = $process;

			}


        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000819 end

        return (array) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param string
     * @param string
     * @return void
     */
    private function __construct($login, $password)
    {
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008B9 begin
//		$session = authenticate(array($login),array($password),array("1"),array(MODULE));
//		$this->sessionGeneris =$session["pSession"];
//		if ($this->sessionGeneris=="Authentication failed")
//		{trigger_error("wrong login/password");}
		$this->login=$login;

        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008B9 end
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param string
     * @param string
     * @return Wfengine
     */
    public function singleton($login = "", $password = "")
    {
        $returnValue = null;

        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008BB begin
		if (!isset(self::$instance)) {

			//checks if the wfengine has not already been in the session, useful otherwise, if the application using the wfengine extract the instance from the session this singleton won't see the instance of it and will create a second instance
			if (!isset($_SESSION["Wfengine"]))
			{
            $c = __CLASS__;
            self::$instance = new $c($login,$password);
			}
			else
			{self::$instance = $_SESSION["Wfengine"];}

        }
        $returnValue = self::$instance;
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008BB end

        return $returnValue;
    }

    /**
     * Short description of method getProcessExecutions
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getProcessExecutions()
    {

        $returnValue = array();

        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008E7 begin
		$processes = getInstances($this->sessionGeneris,array(CLASS_PROCESS_EXECUTIONS),array(""));

		foreach ($processes["pDescription"] as $key=>$pInstance)
			{
				$processInstance = new ViewProcessExecution($key);
				$returnValue[]=$processInstance;
			}


        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008E7 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getUser
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return WfUser
     */
    public function getUser()
    {
        $returnValue = null;

        // section 10-13-1--31--4660acca:119ecd38e96:-8000:00000000000008A0 begin


		if ($this->user == null)
		{	//TODO OPTIMIZE, nevertheless seems that $this->user  is always set before externally
			//$users  = search($this->sessionGeneris, array(PROPERTY_USER_LOGIN,$this->login),array(),false);
			$db = core_kernel_classes_DbWrapper::singleton(DATABASE_NAME);
			$query = "SELECT subject FROM `statements` WHERE predicate='".PROPERTY_USER_LOGIN."' AND object ='".$this->login."' ";


			
			$db->dbConnector->debug = true;
			$result = $db->execSql($query);
			$db->dbConnector->debug = false;
			var_dump($result->fields);
			while (!$result-> EOF){
	
				
				$result->MoveNext();
			}
		

			//$hdl = fopen("monitoring","a+"); fwrite($hdl,microtime(true)." ".__FILE__." ".__LINE__.$users[0][0]."\r\n");fclose($hdl);

			$this->user = new WfUser($users[0][0], $this->login);
		}

		$returnValue = $this->user;
        // section 10-13-1--31--4660acca:119ecd38e96:-8000:00000000000008A0 end

        return $returnValue;
    }

} /* end of class Wfengine */

?>