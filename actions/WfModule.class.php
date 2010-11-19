<?php
error_reporting(E_ALL);
class WfModule extends Module
{
	
	public function __construct(){
		
		$GLOBALS['lang'] = $GLOBALS['default_lang'];
		
		if($this->_isAllowed()){
			//Authentication and API initialization
			$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
			$userService->connectCurrentUser();
		}
		else{
			$this->redirect(_url('index', 'Authentication', 'wfEngine', array('errorMessage' => urlencode(__('Access denied. Please renew your authentication!')))));
		}
		
		//initialize I18N
		(Session::hasAttribute('ui_lang')) ? $uiLang = Session::getAttribute('ui_lang') : $uiLang = $GLOBALS['default_lang'];
		tao_helpers_I18n::init($uiLang);
	}
	
	/**
	 * Check if the current user is allowed to acces the request
	 * Override this method to allow/deny a request
	 * @return boolean
	 */
	protected function _isAllowed(){
		return (isset($_SESSION['taoqual.authenticated']) && core_kernel_users_Service::singleton()->isASessionOpened());	//if a user is logged in
	}
}
?>