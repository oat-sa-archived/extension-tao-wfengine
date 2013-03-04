<?php
/**
 * Service interface to save and retrieve recovery contexts
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @subpackage actions
 *
 */
class wfEngine_actions_RecoveryContext extends tao_actions_Api {
	
	/**
	 * Retrieve the current context
	 */
	public function retrieve(){
		$context = array();
		$session = PHPSession::singleton();
		
		if(	$this->hasRequestParameter('token') && 	$session->hasAttribute('activityExecutionUri')){
			
			$token = $this->getRequestParameter('token');
			$activityExecutionUri = $session->getAttribute('activityExecutionUri');
			
			if($this->authenticate($token) && !empty($activityExecutionUri)){
				$activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
				$recoveryService = wfEngine_models_classes_RecoveryService::singleton();
				
				$context = $recoveryService->getContext($activityExecution, 'any');//get the first, no null context!
			}
		}
		echo json_encode($context);
	}
	
	/**
	 * Save a context in the current activity execution
	 */
	public function save(){
		$session = PHPSession::singleton();
		
		$saved = false;
		if(	$this->hasRequestParameter('token') && 
			$this->hasRequestParameter('context') &&
			$session->hasAttribute('activityExecutionUri')){
			
			$token = $this->getRequestParameter('token');
			$activityExecutionUri = $session->getAttribute('activityExecutionUri');
			
			if($this->authenticate($token) && !empty($activityExecutionUri)){
				$activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
				$recoveryService = wfEngine_models_classes_RecoveryService::singleton();
				
				$context = $this->getRequestParameter('context');
				if(is_array($context)){
					if(count($context) > 0){
						$saved = $recoveryService->saveContext($activityExecution, $context);						
					}
				}
				else if (is_null($context) || $context == 'null'){
					//if the data sent are null [set context to null], we remove it  
					$saved = $recoveryService->removeContext($activityExecution);
				}
			}
		}
		echo json_encode(array('saved' => $saved));
	}
	
}
?>