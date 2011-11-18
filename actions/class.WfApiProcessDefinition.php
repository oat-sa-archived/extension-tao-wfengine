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
class wfEngine_actions_WfApiProcessDefinition extends wfEngine_actions_WfApi {
    
	protected $processDefinitionService = null;
	protected $processDefinition = null;
	
	public function __construct(){
		
		parent::__construct();
		
		$this->processDefinitionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessDefinitionService');
		
		$processDefinitionUri = urldecode($this->getRequestParameter('processDefinitionUri'));
		if(!empty($processDefinitionUri) && common_Utils::isUri($processDefinitionUri)){
			$process = new core_kernel_classes_Resource($processDefinitionUri);
			if($process->hasType(new core_kernel_classes_Class(CLASS_PROCESS))){
				$this->processDefinition = $process;
			}else{
				$this->setErrorMessage(__('The resource is not a process definition'));
			}
		}else{
			$this->setErrorMessage(__('No process definition uri given'));
		}
		
	}
	
	public function getName(){
		
		if(!is_null($this->processDefinition)){
			$label = $this->processDefinition->getLabel();
			$this->setSuccess(true);
			$this->setData('name', $label);
		}
		
	}
	
	public function initExecution(){
		
		if(!is_null($this->processDefinition)){

			$postName = urldecode($this->getRequestParameter('name'));
			$name = empty($postName)?__('execution of').' '.$this->processDefinition->getLabel():$postName;

			$postComment = urldecode($this->getRequestParameter('comment'));
			$comment = empty($postComment)?__('created by').' '.__CLASS__.' on '.date('c'):$postComment;
			
			$variables = array();
			$postVariables = urldecode($this->getRequestParameter('variables'));
			if (is_array($postVariables) && !empty($postVariables)) {
				$variables = $postVariables;
			}
			
			$processExecution = $this->processExecutionService->createProcessExecution($this->processDefinition, $name, $comment, $variables);
			if(!is_null($processExecution)){
				$this->setSuccess(true);
				$this->setData('processExecutionUri', $processExecution->uriResource);
			}else{
				$this->setErrorMessage(__('Cannot create process execution'));
			}
			
		}
		
	}
	
}