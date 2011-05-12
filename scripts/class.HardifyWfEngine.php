<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/scripts/class.HardifyWfEngine.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.05.2011, 12:13:09 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage scripts
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_scripts_Runner
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/scripts/class.Runner.php');

/* user defined includes */
// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD1-includes begin
// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD1-includes end

/* user defined constants */
// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD1-constants begin
// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD1-constants end

/**
 * Short description of class wfEngine_scripts_HardifyWfEngine
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage scripts
 */
class wfEngine_scripts_HardifyWfEngine
    extends tao_scripts_Runner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute MODE_SMOOTH2HARD
     *
     * @access public
     * @var int
     */
    const MODE_SMOOTH2HARD = 1;

    /**
     * Short description of attribute MODE_HARD2SMOOTH
     *
     * @access public
     * @var int
     */
    const MODE_HARD2SMOOTH = 2;

    /**
     * Short description of attribute mode
     *
     * @access protected
     * @var int
     */
    protected $mode = 0;

    // --- OPERATIONS ---

    /**
     * Short description of method preRun
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function preRun()
    {
        // section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD6 begin
        
    	if(isset($this->parameters['compile']) && $this->parameters['compile'] == true){
    		$this->mode = self::MODE_SMOOTH2HARD;
    	}
    	if(isset($this->parameters['decompile']) && $this->parameters['decompile'] == true){
    		$this->mode = self::MODE_HARD2SMOOTH;
    	}
    	
        // section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD6 end
    }

    /**
     * Short description of method run
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function run()
    {
        // section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD4 begin
        
    	switch($this->mode){ 
    		case self::MODE_SMOOTH2HARD:
    			$this->out("Compiling triples to relational database", array('color' => 'light_blue'));
    			

    			
    			$options = array(
    				'recursive'				=> true,
    				'append'				=> true,
					'createForeigns'		=> true,
					'referencesAllTypes'	=> true,
					'rmSources'				=> false
    			);
    			
    			$switcher = new core_kernel_persistence_Switcher(array(CLASS_PROCESSVARIABLES));
    			
    			/*
    			 * Compiled wfEngine data
    			 */
    			$this->out("\nCompiling wfEngine classes", array('color' => 'light_blue'));
    			
    			//class used by the wfEngine
    			$wfClasses = array(
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassCallOfservicesResources",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassServiceDefinitionResources",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassServicesResources",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassConnectors",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassTransitionRules",
		//			"http://www.tao.lu/middleware/wfEngine.rdf#ClassProcessVariables",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassSupportServices",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassCallOfServices",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassActualParameters",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassFormalParameters",
		//			"http://www.tao.lu/middleware/wfEngine.rdf#ClassRole"
    				"http://www.tao.lu/middleware/wfEngine.rdf#ClassTokens",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassProcessInstances",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassActivityExecutions",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassServiceDefinitions",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassProcessDefinitions",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassActivities",
    			);
    			
    			foreach($wfClasses as $classUri){
    				$class = new core_kernel_classes_Class($classUri);
    				$this->out(" - Hardifying ".$class->getLabel(), array('color' => 'light_green'));
    				$switcher->hardify($class, $options);
    			}
    			
    			/*
    			 * Compiled test takers
    			 */
    			$this->out("\nCompiling test takers", array('color' => 'light_blue'));
    			
    			$testTakerClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
				$userClass		= new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User');
				
				$this->out(" - Hardifying ".$testTakerClass->getLabel(), array('color' => 'light_green'));
				
				$switcher->hardify($testTakerClass, array_merge($options, array('topClass' => $userClass)));	
    			
    			/*
    			 * Compiled results
    			
    			$this->out("\nCompiling results", array('color' => 'light_blue'));
    			
    			$resultClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOResult.rdf#Result');
				
    			$this->out(" - Hardifying ".$resultClass->getLabel(), array('color' => 'light_green'));
    			
    			$switcher->hardify($resultClass, array_merge($options, array('createForeigns' => false)));
				  */
				
    			unset($switcher);
    			
    			
    			
    			break;
    		case self::MODE_HARD2SMOOTH:
    			$this->err('this mode is not yet implemented', true);
    			break;
    		
    		default:
    			$this->err('Unknow mode', true);
    	}
    	
        // section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD4 end
    }

    /**
     * Short description of method postRun
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function postRun()
    {
        // section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD8 begin
        
    	$this->out("\nRebuild table indexes, it can take a while...");
    	
    	//Need to OPTIMIZE / FLUSH the tables
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
    	$tables = $dbWrapper->dbConnector->MetaTables('TABLES');
    	
    	$size = count($tables);
    	$i = 0;
    	while($i < $size){
    		
    		$percent = round(($i / $size) * 100);
    		if($percent < 10){
    			$percent = '0'.$percent;
    		}
    		$this->out(" $percent %", array('color' => 'light_green', 'inline' => true, 'prefix' => "\r"));
    		
    		$dbWrapper->execSql("OPTIMIZE TABLE `{$tables[$i]}`");
    		$dbWrapper->execSql("FLUSH TABLE `{$tables[$i]}`");
    		
    		$i++;
    	}
    	
    	$this->out("\nFinished !\n", array('color' => 'light_blue'));
    	
        // section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD8 end
    }

} /* end of class wfEngine_scripts_HardifyWfEngine */

?>