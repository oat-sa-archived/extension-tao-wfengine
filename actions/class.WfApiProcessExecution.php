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
class wfEngine_actions_WfApiProcessExecution extends wfEngine_actions_WfApi {
    
	
	public function __construct()
	{
		
		parent::__construct();
		
		if(!$this->processExecution->hasType(new core_kernel_classes_Class(CLASS_PROCESSINSTANCES))){
			$this->processExecution = null;
			$this->setErrorMessage(__('The resource is not a process execution'));
		}
	}
	
	public function delete()
	{
		if(!is_null($this->processExecution)){
			$this->setSuccess($this->processExecutionService->deleteProcessExecution($this->processExecution, true));
		}
		
	}
	
	public function pause()
	{
		if(!is_null($this->processExecution)){
			$this->setSuccess($this->processExecutionService->pause($this->processExecution));
		}
	}
	
	public function resume()
	{
		if(!is_null($this->processExecution)){
			$this->setSuccess($this->processExecutionService->resume($this->processExecution));
		}
	}
	
	public function cancel()
	{
		if(!is_null($this->processExecution)){
			$this->setSuccess($this->processExecutionService->finish($this->processExecution));
		}
	}
	
	public function next()
	{
		if(!is_null($this->processExecution)){
			$activityExecution = $this->getCurrentActivityExecution();
			if(!is_null($activityExecution)){
				$this->setSuccess($this->processExecutionService->performTransition($this->processExecution, $activityExecution));
			}
		}
	}
    
	public function previous()
	{
		if(!is_null($this->processExecution)){
			$activityExecution = $this->getCurrentActivityExecution();
			if(!is_null($activityExecution)){
				$this->setSuccess($this->processExecutionService->performBackwardTransition($this->processExecution, $activityExecution));
			}
		}
	}
	
}