<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Test the service wfEngine_models_classes_ConnectorService
 *
 * @author Lionel Lecaque, <taosupport@tudor.lu>
 * @package wfEngine
 * @subpackage test
 */

class ConnectorServiceTestCase extends UnitTestCase {
    /**
	 * @var wfEngine_models_classes_ActivityService
     */
    protected $service;

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

        $aService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ConnectorService');
        $this->assertIsA($aService, 'tao_models_classes_Service');
        $this->assertIsA($aService, 'wfEngine_models_classes_ConnectorService');

        $this->service = $aService;
    }
    
}