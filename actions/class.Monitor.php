<?php
/**
 *  Montitor Controler provide actions to manage processes
 * 
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @package wfEngine
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class wfEngine_actions_Monitor extends tao_actions_TaoModule {
	
	protected $variableService = null;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->processMonitoringGridOptions = array(
			'columns' => array(
				'http://www.w3.org/2000/01/rdf-schema#label' 													=> array('weight'=>3)
				, 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesExecutionOf' 				=> array('weight'=>2)
				, 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesCurrentActivityExecutions' => array(
					'weight'=>6
					, 'widget'=>'CurrentActivities'
					, 'columns' => array(
						'variables' => array(
							'widget'=>'ActivityVariables'
							, 'columns' => array(
								'value' => array('weight'=>3, 'widget'=>'ActivityVariable')
							)
						)
					)
				)
			)
		);
		
		$this->variableService = wfEngine_models_classes_VariableService::singleton();
	}
	
	/**
	 * 
	 */
	public function getRootClass()
	{
		return null;
	}
	
	/**
	 * The monitoring front page
	 * -> Display current process status
	 * -> Display current activities status
	 * -> Display activities history
	 */
	public function index()
	{
		//Class to filter on
		$clazz = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		
		//Properties to filter on
		$properties = array();
		$properties[] = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_EXECUTIONOF);
		$properties[] = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_STATUS);
		
		//Monitoring grid
		$processMonitoringGrid = new wfEngine_helpers_Monitoring_ProcessMonitoringGrid(array(), $this->processMonitoringGridOptions);
		$grid = $processMonitoringGrid->getGrid();
		$model = $grid->getColumnsModel();
		
		//Process history grid
		$processHistoryGrid = new wfEngine_helpers_Monitoring_ExecutionHistoryGrid(new core_kernel_classes_Resource(' '), array(
			'columns' => array(
				'variables' => array(
					'widget'=>'ActivityVariables'
					, 'columns' => array(
						'value' => array('weight'=>3, 'widget'=>'ActivityVariable')
					)
				)
			)
		));
		$historyProcessModel = $processHistoryGrid->getGrid()->getColumnsModel();
		
		//Filtering data
		$this->setData('clazz', $clazz);
		$this->setData('properties', $properties);
		
		//Monitoring data
		$this->setData('model', json_encode($model));
		$this->setData('historyProcessModel', json_encode($historyProcessModel));
		$this->setData('data', $processMonitoringGrid->toArray());
		
		//WF Variables
		$this->setData('wfVariables', json_encode($this->variableService->getAllVariables()));
		
		$this->setView('monitor/index.tpl');
	}
	
	/**
	 * Get JSON monitoring data
	 */
	public function monitorProcess()
	{
		$returnValue = array();
		$filter = null;
		
		//get the filter
		if($this->hasRequestParameter('filter')){
			$filter = $this->getRequestParameter('filter');
			$filter = $filter == 'null' || empty($filter) ? null : $filter;
            if(is_array($filter)){
                foreach($filter as $propertyUri=>$propertyValues){
                    foreach($propertyValues as $i=>$propertyValue){
                        $propertyDecoded = tao_helpers_Uri::decode($propertyValue);
                        if(common_Utils::isUri($propertyDecoded)){
                            $filter[$propertyUri][$i] = $propertyDecoded;
                        }
                    }
                }
            }
		}
		//get the processes uris
		$processesUri = $this->hasRequestParameter('processesUri') ? $this->getRequestParameter('processesUri') : null;
		
		$processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		if(!is_null($filter)){
			$processExecutions = $processInstancesClass->searchInstances($filter, array ('recursive'=>true));
		}
		else if(!is_null($processesUri)){
			foreach($processesUri as $processUri){
				$processExecutions[$processUri] = new core_kernel_classes_resource($processUri);
			}
		}
		else{
			$processExecutions = $processInstancesClass->getInstances();
		}
		
		$processMonitoringGrid = new wfEngine_helpers_Monitoring_ProcessMonitoringGrid(array_keys($processExecutions), $this->processMonitoringGridOptions);
		$data = $processMonitoringGrid->toArray();
		
		echo json_encode($data);
	}

	/**
	 * Get JSON activity history
	 */
	public function processHistory()
	{
		if($this->hasRequestParameter('uri')){
			$uri = $this->getRequestParameter('uri');
		}
		
		$processMonitoringGrid = new wfEngine_helpers_Monitoring_ExecutionHistoryGrid(new core_kernel_classes_Resource($uri));
		$data = $processMonitoringGrid->toArray();
		
		echo json_encode($data);
	}
}
