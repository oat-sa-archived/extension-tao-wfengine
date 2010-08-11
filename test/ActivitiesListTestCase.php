<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';

class ActivitiesListTestCase extends UnitTestCase{
	
	protected $proc;
	protected $activitiesList;
	
	public function setUp(){
		TestRunner::initTest();
/*
		if(!isset($_SESSION["session"])) {
		
		$session = authenticate(array(LOGIN),array(PASS),array("1"),array(MODULE));
			if ($session["pSession"][0] != "Authentication failed")
			{
				$_SESSION["bd"]				= MODULE;
				$_SESSION["session"]		= $session["pSession"];
				$_SESSION["ok"]				= true; $_SESSION["guilg"] = "EN";
				$_SESSION["type"]			= "i";
				$_SESSION["cuser"]			= LOGIN;
				$_SESSION["Wfengine"] 		= Wfengine::singleton(LOGIN,PASS);
				
			}
		}

		core_control_FrontController::connect(LOGIN,md5(PASS),MODULE);

		$factory = new ProcessExecutionFactory();
		$factory->name = 'Test Process Execution';
		$factory->intervieweeInst = '#test4';
		$factory->execution = PIAAC_PROCESS_URI;
		try {
			$this->proc = $factory->create();
		}
		catch(common_Exception $e) {
			echo ($e->getMessage());
			var_dump($e->getTraceAsString());
		}
		
		$rdfList = core_kernel_classes_RdfListFactory::create('Test Rdf List', 'Test RDF for dummies');
		$this->proc->performTransition(true);
		$rdfList->add(new core_kernel_classes_Resource($this->proc->currentActivity[0]->uri));
		$this->proc->performTransition(true);
		$rdfList->add(new core_kernel_classes_Resource($this->proc->currentActivity[0]->uri));
			
		$activitiesListClass = new core_kernel_classes_Class(CLASS_ACTIVITIES_LIST);
		$activitiesListInst = core_kernel_classes_ResourceFactory::create($activitiesListClass,'ActivitiesLIst Test Case');
			

		$this->activitiesList = new ActivitiesList($activitiesListInst);

		$this->activitiesList->setSelector(new core_kernel_classes_Resource(RESOURCE_ACTIVITIES_SELECTOR_SEQ));
		$this->activitiesList->setRdfList($rdfList);

		
	}
	
	public function testFactory(){
		var_dump($this->activitiesList);
		$this->fail('not implemented yet');
		*/
	}
	
	

}
?>