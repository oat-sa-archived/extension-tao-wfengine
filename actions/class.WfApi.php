<?php
/**
 * WFEngine API
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @subpackage actions
 *
 */
class wfEngine_actions_WfApi extends tao_actions_Api {
    
	protected $processExecutionService = null;
	protected $activityExecutionService = null;
	protected $processExecution = null;
	protected $activityExecution = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$this->activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		
		//validate ALL posted values:
		$processExecutionUri = urldecode($this->getRequestParameter('processExecutionUri'));
		if(!empty($processExecutionUri) && common_Utils::isUri($processExecutionUri)){
			$this->processExecution = new core_kernel_classes_Resource($processExecutionUri);
		}
		
		$activityExecutionUri = urldecode($this->getRequestParameter('activityExecutionUri'));
		if(!empty($activityExecutionUri) && common_Utils::isUri($activityExecutionUri)){
			$this->activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
		}
		
		$this->setSuccess(false);
	}
	
	public function __destruct()
	{
		echo json_encode($this->output);	
	}
			
	protected function setSuccess($success)
	{
		$caller = array_shift(debug_backtrace());

		$this->output['success'] = (bool) $success;
		$this->output['caller'] = __CLASS__.'::'.$caller['function'];
	}
	
	protected function setErrorMessage($message, $code = 0)
	{
		if(!isset($this->output['error'])){
			$this->output['error'] = array();
		}
		$this->output['error'][] = $message;
	}
	
	public function getCurrentActivityExecution()
	{
		$returnValue = null;
		
		if(!is_null($this->processExecution)){
			
			$currentActivityExecutions = $this->processExecutionService->getCurrentActivityExecutions($this->processExecution);
			if(is_null($this->activityExecution)){
				if(count($currentActivityExecutions) == 1){
					$returnValue = reset($currentActivityExecutions);
				}else{
					$this->setErrorMessage('There are more than one current activity executions');
				}
			}else{
				if(!isset($currentActivityExecutions[$this->activityExecution->uriResource])){
					$returnValue = $this->activityExecution;
				}else{
					$this->setErrorMessage('The current activity execution is not among the current ones of the process execution');
				}
			}
		}
		
		return $returnValue;
	}
	
}