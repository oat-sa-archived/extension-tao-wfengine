<?php

error_reporting(-1);

require_once dirname(__FILE__) . '/../../generis/common/inc.extension.php';
require_once dirname(__FILE__). '/../includes/common.php';

define("LOGIN", "tao", true);
define("PASS", "tao", true);

require_once INCLUDES_PATH.'/simpletest/autorun.php';
 

class ProcessExecutionTestCase extends UnitTestCase{
	
	protected $proc;
	
	public function setUp(){
		core_kernel_classes_ApiModelOO::singleton()->logIn(LOGIN,md5(PASS),DATABASE_NAME,true);
		$factory = new ProcessExecutionFactory();
		$factory->name = 'Test Process Execution';
		$factory->execution = 'http://www.tao.lu/middleware/Interview.rdf#126537966613798';
		
		
		$factory->ownerUri = LOGIN;

		$this->proc = $factory->create();


	}
	
	public function test(){
		var_dump($this->proc->currentActivity[0]);
		$activity = $this->proc->currentActivity[0];
		var_dump($activity->getServices());
		
		$this->proc->performTransition();
		
		var_dump($this->proc->currentActivity[0]);
		$activity = $this->proc->currentActivity[0];
		var_dump($activity->getServices());
//		var_dump($this->proc);
		$this->fail('not imp yet');
		
	}


	
}

?>