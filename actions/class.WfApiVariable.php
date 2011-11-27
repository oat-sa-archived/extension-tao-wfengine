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
class wfEngine_actions_WfApiVariable extends wfEngine_actions_WfApi {
    
	protected $variableService = null;
	protected $code = '';
	protected $value = '';
	protected $values;
	
	public function __construct()
	{
		parent::__construct();
		$this->values = array();
		$this->variableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		
		if(!$this->activityExecution->hasType(new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION))){
			$this->activityExecution = null;
			$this->setErrorMessage(__('The resource is not an activity execution'));
		}
		
		$code = urldecode($this->getRequestParameter('code'));
		if(!empty($code)){
			if(is_null($this->variableService->getProcessVariable($code))){
				$this->setErrorMessage(__('The variable with the code '.$code.' does not exists'));
			}else{
				$this->code = $code;
			}
		}else{
			$this->setErrorMessage(__('No variable code given'));
		}
		
		$values = $this->getRequestParameter('value');
		if(is_array($values)){
			foreach($values as $value){
				$this->values[] = urldecode($value);
			}
		}else{
			$this->values[] = urldecode($values);
		}
	}
	
	public function push()
	{
		if(!is_null($this->activityExecution) && !empty($this->code) && !empty($this->values)){
			foreach($this->values as $value){
				$this->setSuccess($this->variableService->push($this->code, $value, $this->activityExecution));
			}
		}
	}
	
	public function edit()
	{
		if(!is_null($this->activityExecution) && !empty($this->code) && !empty($this->values)){
			$this->setSuccess($this->variableService->edit($this->code, $this->values, $this->activityExecution));
		}
	}
	
	public function get()
	{
		if(!is_null($this->activityExecution) && !empty($this->code)){
			$value = $this->variableService->get($this->code, $this->activityExecution);
			
			if(!empty($value)){
				$this->setSuccess(true);
				$this->setData('values', $value);
			}
		}
	}
	
	public function remove()
	{
		if(!is_null($this->activityExecution) && !empty($this->code)){
			$this->setSuccess($this->variableService->remove($this->code, $this->activityExecution));
		}
	}
}
