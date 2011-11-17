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
	
	public function __construct(){
		
		parent::__construct();
		$this->variableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		
		$code = urldecode($this->getRequestParameter('code'));
		if(!empty($code)){
			$this->code = $code;
		}
		
		$value = urldecode($this->getRequestParameter('value'));
		if(is_string($value)){
			$this->value = $value;
		}else if(is_array($value)){
			$this->value = $value;
		}
		
	}
	
	public function push(){
		
		if(!is_null($this->activityExecution) && !empty($this->code) && !empty($this->value)){
			$this->setSuccess($this->variableService->push($this->code, $this->value, $this->activityExecution));
		}
		
	}
	
	public function edit(){
		
		if(!is_null($this->activityExecution) && !empty($this->code) && !empty($this->value)){
			$this->setSuccess($this->variableService->edit($this->code, $this->value, $this->activityExecution));
		}
		
	}
	
	public function get(){
		
		if(!is_null($this->activityExecution) && !empty($this->code)){
			$this->setSuccess($this->variableService->get($this->code, $this->activityExecution));
		}
		
	}
	
	public function remove(){
		
		if(!is_null($this->activityExecution) && !empty($this->code)){
			$this->setSuccess($this->variableService->remove($this->code, $this->activityExecution));
		}
		
	}
}