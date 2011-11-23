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
class wfEngine_actions_WfApiActivityExecution extends wfEngine_actions_WfApi {
    
	public function __construct()
	{
		parent::__construct();
		if(!$this->activityExecution->hasType(new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION))){
			$this->activityExecution = null;
			$this->setErrorMessage(__('The resource is not an activity execution'));
		}
		
		$this->activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
	}
	
	public function assign()
	{
		if(!is_null($this->activityExecution)){
			$userUri = urldecode($this->getRequestParameter('userUri'));
			if (!empty($userUri) && common_Utils::isUri($userUri)) {
				$user = new core_kernel_classes_Resource($userUri);
				$this->setSuccess($this->activityExecutionService->setActivityExecutionUser($this->activityExecution, $user, true));
			}else{
				$this->setErrorMessage('no user given');
			}
		}
	}
	
	public function next()
	{
		if(is_null($this->processExecution)){
			$this->processExecution = $this->activityExecutionService->getRelatedProcessExecution($this->activityExecution);
		}
		$currentActivityExecutions = $this->processExecutionService->performTransition($this->processExecution, $this->activityExecution);
		if($currentActivityExecutions!==false){
			$this->setSuccess(true);
			$this->setData('currentActivityExecutions', $currentActivityExecutions);
		}
	}
    
	public function previous()
	{
		if(is_null($this->processExecution)){
			$this->processExecution = $this->activityExecutionService->getRelatedProcessExecution($this->activityExecution);
		}
		$currentActivityExecutions = $this->processExecutionService->performBackwardTransition($this->processExecution, $this->activityExecution);
		if($currentActivityExecutions!==false){
			$this->setSuccess(true);
			$this->setData('currentActivityExecutions', $currentActivityExecutions);
		}
	}
	
}
