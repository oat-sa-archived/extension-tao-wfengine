<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Test the service wfEngine_models_classes_ActivityService
 *
 * @author Lionel Lecaque, <taosupport@tudor.lu>
 * @package wfEngine
 * @subpackage test
 */

class ActivityServiceTestCase extends UnitTestCase {

    /**
     * output messages
     * @param string $message
     * @param boolean $ln
     * @return void
     */
    private function out($message, $ln = false){
        if(self::OUTPUT){
            if(PHP_SAPI == 'cli'){
                if($ln){
                    echo "\n";
                }
                echo "$message\n";
            }
            else{
                if($ln){
                    echo "<br />";
                }
                echo "$message<br />";
            }
        }
    }

    /**
     * Test the service implementation
     */
    public function testService(){

        $aService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
        $this->assertIsA($aService, 'tao_models_classes_Service');
        $this->assertIsA($aService, 'wfEngine_models_classes_ActivityService');

        $this->service = $aService;
    }

    
    
    public function testVirtualProcess(){
        $processAuthoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
        $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
        $processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');
        $this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
        
$authoringService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessAuthoringService');
        //define activities and connectors
        $activity1 = $authoringService->createActivity($processDefinition, 'activity1');
        $this->assertNotNull($activity1);
        
		
        
        $authoringService->setFirstActivity($processDefinition, $activity1);

        $this->fail('not inp yet - Work in progress');
         
    }

}