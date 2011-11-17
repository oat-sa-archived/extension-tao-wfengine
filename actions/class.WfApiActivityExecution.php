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
    
	
	public function __construct(){
		
		parent::__construct();
		
	}
	
	public function assign(){
		
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
	
}