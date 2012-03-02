<?php
class wfEngine_actions_WfModule extends Module
{
	
	public function __construct()
	{
		if($this->_isAllowed()){
			//Authentication and API initialization
			$userService = wfEngine_models_classes_UserService::singleton();
			$userService->connectCurrentUser();
		}
		else{
			$this->notAllowedRedirection();
		}
		
	}
	
	/**
	 * Check if the current user is allowed to acces the request
	 * Override this method to allow/deny a request
	 * @return boolean
	 */
	protected function _isAllowed()
	{
		return (isset($_SESSION['taoqual.authenticated']) && core_kernel_users_Service::singleton()->isASessionOpened());	//if a user is logged in
	}
    
    /**
     * Behaviour to adopt if the user is not allowed to access the current action.
     */
    protected function notAllowedRedirection()
    {
        if($this->hasRequestParameter('processUri') && $this->hasRequestParameter('activityUri')){
            $this->redirect(_url('index', 'Authentication', 'wfEngine', array(
                    'errorMessage' => urlencode(__('Please login to access the selected activity.')),
                    'processUri' => urlencode($this->getRequestParameter('processUri')),
                    'activityUri' => urlencode($this->getRequestParameter('activityUri'))
                    )
                ));
        }else{
            $this->redirect(_url('index', 'Authentication', 'wfEngine', array(
                    'errorMessage' => urlencode(__('Access denied. Please renew your authentication.'))
                )));
        }
    }
}
?>