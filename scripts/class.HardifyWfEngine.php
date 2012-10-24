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
require_once(dirname(__FILE__).'/../../tao/scripts/class.Runner.php');

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

		if (!defined('DEBUG_PERSISTENCE'))
			define ('DEBUG_PERSISTENCE', false);
		 
		switch($this->mode){
			case self::MODE_SMOOTH2HARD:
				 
				self::out("Compiling triples to relational database", array('color' => 'light_blue'));
				 
				$options = array(
    				'recursive'				=> true,
    				'append'				=> true,
					'createForeigns'		=> true,
					'referencesAllTypes'	=> true,
					'rmSources'				=> true
				);
				 
				$switcher = new core_kernel_persistence_Switcher(array('http://www.tao.lu/middleware/wfEngine.rdf#ClassProcessVariables'));
				 
				self::out("\nCompiling Languages classes", array('color' => 'light_blue'));
				
				$languagesClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#Languages');
				
				self::out(" - Hardifying ".$languagesClass->getLabel(), array('color' => 'light_green'));
				
				$switcher->hardify($languagesClass, array_merge($options, array(
                	'additionalProperties'  => array(new core_kernel_classes_Property('http://www.w3.org/1999/02/22-rdf-syntax-ns#value')),
                    'createForeigns'        => false
				)));
				 
				// Compiled wfEngine data
				self::out("\nCompiling wfEngine classes", array('color' => 'light_blue'));
				 
				//class used by the wfEngine
				$wfClasses = array(
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassSupportServices",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassCallOfservicesResources",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassServiceDefinitionResources",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassServicesResources",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassConnectors"
				);
				 
				foreach($wfClasses as $classUri){
					$class = new core_kernel_classes_Class($classUri);
					$this->out(" - Hardifying ".$class->getLabel(), array('color' => 'light_green'));
					$switcher->hardify($class, $options);
				}
				 
				// Compiled test takers
				self::out("\nCompiling test takers", array('color' => 'light_blue'));
				 
				$testTakerClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
				$userClass		= new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User');
				$taoSubjectRoleClass =  new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#TaoSubjectRole');
				
				self::out(" - Hardifying ".$testTakerClass->getLabel(), array('color' => 'light_green'));
				$switcher->hardify($testTakerClass, array_merge($options, array('topClass' => $userClass)));
				
				//force referencing:
				$resourceReferencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
				if(!$resourceReferencer->isClassReferenced($taoSubjectRoleClass)){
					
					self::out(" No instance of class {} Force referencing the class ".$taoSubjectRoleClass->getLabel(), array('color' => 'light_green'));
					
					$resourceReferencer->referenceClass(
							$taoSubjectRoleClass, 
							array(
								'table' => '_'.core_kernel_persistence_hardapi_Utils::getShortName($testTakerClass),
								'topClass'=> $userClass
								)
						);
				}
				

				// Compiled groups
				self::out("\nCompiling groups", array('color' => 'light_blue'));
				 
				$groupClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOGroup.rdf#Group');

				self::out(" - Hardifying ".$groupClass->getLabel(), array('color' => 'light_green'));

				$switcher->hardify($groupClass, $options);

				// Compiled delivery history
				self::out("\nCompiling delivery history", array('color' => 'light_blue'));
				 
				$deliveryHistoryClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAODelivery.rdf#History');

				self::out(" - Hardifying ".$deliveryHistoryClass->getLabel(), array('color' => 'light_green'));

				$switcher->hardify($deliveryHistoryClass, array_merge($options, array('createForeigns' => false)));

				
				unset($switcher);
				 
				break;

				 
			case self::MODE_HARD2SMOOTH:
				 
				self::out("Decompiling triples to relational database", array('color' => 'light_blue'));
				 
				$options = array(
    				'recursive'				=> true,
    				'removeForeigns'		=> true				
				);
				 
				$switcher = new core_kernel_persistence_Switcher(array('http://www.tao.lu/middleware/wfEngine.rdf#ClassProcessVariables'));
				 
				// Compiled wfEngine data
				self::out("\nDecompiling wfEngine classes", array('color' => 'light_blue'));
				 
				//class used by the wfEngine
				$wfClasses = array(
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassSupportServices",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassCallOfservicesResources",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassServiceDefinitionResources",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassServicesResources",
					"http://www.tao.lu/middleware/wfEngine.rdf#ClassConnectors"
				);
				 
				foreach($wfClasses as $classUri){
					$class = new core_kernel_classes_Class($classUri);
					$this->out(" - Unhardifying ".$class->getLabel(), array('color' => 'light_green'));
					$switcher->unhardify($class, $options);
				}

				// Compiled test takers
				self::out("\nDecompiling test takers", array('color' => 'light_blue'));
				 
				$testTakerClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');

				self::out(" - Unhardifying ".$testTakerClass->getLabel(), array('color' => 'light_green'));

				$switcher->unhardify($testTakerClass, $options);
				 
				// Compiled groups
				self::out("\nDecompiling groups", array('color' => 'light_blue'));
				 
				$groupClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOGroup.rdf#Group');

				self::out(" - Unhardifying ".$groupClass->getLabel(), array('color' => 'light_green'));

				$switcher->unhardify($groupClass, $options);

				// Compiled delivery history
				self::out("\nDecompiling delivery history", array('color' => 'light_blue'));
				 
				$deliveryHistoryClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAODelivery.rdf#History');

				self::out(" - Unhardifying ".$deliveryHistoryClass->getLabel(), array('color' => 'light_green'));

				$switcher->unhardify($deliveryHistoryClass, array_merge($options));

				unset($switcher);
				 
				break;

			default:
				self::err('Unknow mode', true);
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

		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$referencer->clearCaches();
		 
		if(isset($this->parameters['indexes']) && $this->parameters['indexes'] == true){

			$dbWrapper = core_kernel_classes_DbWrapper::singleton();

			//Create indexes on discrimining columns
			self::out("\nCreate extra indexes, it can take a while...");

			//uris of the indexes to add to single columns
			$indexProperties = array(
	    		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActualParametersFormalParameter',
	    		'http://www.tao.lu/middleware/wfEngine.rdf#PropertySupportServicesUrl',
	    		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyConnectorsType',
		    	'http://www.tao.lu/middleware/wfEngine.rdf#PropertyConnectorsActivityReference',
		    	'http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesStatus',
		    	'http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesExecutionOf',
		    	'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsStatus',
		    	'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsExecutionOf',
		    	'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsCurrentUser',
	    		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsProcessExecution',
		    	'http://www.tao.lu/Ontologies/generis.rdf#login',
		    	'http://www.tao.lu/Ontologies/generis.rdf#password',
		    	'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_PROCESS_EXEC_ID',
	    		'http://www.tao.lu/Ontologies/TAOResult.rdf#AO_DELIVERY_ID',
	    		'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_TEST_ID',
	    		'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_ITEM_ID',
	    		'http://www.tao.lu/Ontologies/TAOResult.rdf#TAO_SUBJECT_ID'
    		);

    		foreach($indexProperties as $indexProperty){
    			$property = new core_kernel_classes_Property($indexProperty);
    			$propertyAlias = core_kernel_persistence_hardapi_Utils::getShortName($property);
    			foreach($referencer->propertyLocation($property) as $table){
    				if(!preg_match("/Props$/", $table) && preg_match("/^_[0-9]{2,}/", $table)){
    					$dbWrapper->exec("ALTER TABLE `{$table}` ADD INDEX `idx_{$propertyAlias}` (`{$propertyAlias}`( 255 ))");
    				}
    			}
    		}

    		self::out("\nRebuild table indexes, it can take a while...");

    		//Need to OPTIMIZE / FLUSH the tables in order to rebuild the indexes
    		$tables = $dbWrapper->getTables();

    		$size = count($tables);
    		$i = 0;
    		while($i < $size){
    			 
    			$percent = round(($i / $size) * 100);
    			if($percent < 10){
    				$percent = '0'.$percent;
    			}
    			self::out(" $percent %", array('color' => 'light_green', 'inline' => true, 'prefix' => "\r"));
    			 
    			$dbWrapper->exec("OPTIMIZE TABLE `{$tables[$i]}`");
    			$dbWrapper->exec("FLUSH TABLE `{$tables[$i]}`");
    			 
    			$i++;
    		}
		}
		 
		self::out("\nFinished !\n", array('color' => 'light_blue'));
		// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD8 end
	}

} /* end of class wfEngine_scripts_HardifyWfEngine */

?>