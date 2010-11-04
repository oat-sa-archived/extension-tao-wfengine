<?php
require_once ('tao/actions/Api.class.php');

/**
 * Service interface to save and retrieve recovery contexts
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @subpackage actions
 *
 */
class RecoveryContext extends Api {
	
	/**
	 * Retrieve the current context
	 */
	public function retrieve(){
		$context = array();
		
		if(	$this->hasRequestParameter('token') && 	Session::hasAttribute('activityExecutionUri')){
			
			$token = $this->getRequestParameter('token');
			$activityExecutionUri = Session::getAttribute('activityExecutionUri');
			
			if($this->authenticate($token) && !empty($activityExecutionUri)){
				$activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
				$recoveryService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_RecoveryService');
				
				$context = $recoveryService->getContext($activityExecution);
			}
		}
		echo json_encode($context);
	}
	
	/**
	 * Save a context in the current activity execution
	 */
	public function save(){
		$saved = false;
		if(	$this->hasRequestParameter('token') && 
			$this->hasRequestParameter('context') &&
			Session::hasAttribute('activityExecutionUri')){
			
			$token = $this->getRequestParameter('token');
			$activityExecutionUri = Session::getAttribute('activityExecutionUri');
			
			if($this->authenticate($token) && !empty($activityExecutionUri)){
				$activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
				$recoveryService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_RecoveryService');
				
				$context = $this->getRequestParameter('context');
				if(is_array($context)){
					if(count($context) > 0){
						$saved = $recoveryService->saveContext($activityExecution, $context);						
					}
				}
				else if (is_null($context)){
					//if the data sent are null [set context to null], we remove it  
					$saved = $recoveryService->removeContext($activityExecution);
				}
			}
		}
		echo json_encode(array('saved' => $saved));
	}
	
}
?>