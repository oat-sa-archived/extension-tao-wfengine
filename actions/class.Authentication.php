<?php
class wfEngine_actions_Authentication extends Module
{
	/**
	 * WfEngine Login controler
	 */
	public function index()
	{

		if($this->hasRequestParameter('errorMessage')){
			$this->setData('errorMessage',$this->getRequestParameter('errorMessage'));
		}
		
		$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		
		
		$processUri = urldecode($this->getRequestParameter('processUri'));
		$processExecution = common_Utils::isUri($processUri)?new core_kernel_classes_Resource($processUri):null;
		
		$activityUri = urldecode($this->getRequestParameter('activityUri'));
		$activityExecution = common_Utils::isUri($activityUri)?new core_kernel_classes_Resource($activityUri):null;
		
		//create the login for to the activity execution of a process execution:
		$myLoginFormContainer = new wfEngine_actions_form_Login(array(
			'processUri' => !is_null($processExecution)?$processExecution->uriResource:'',
			'activityUri' => !is_null($activityExecution)?$activityExecution->uriResource:''
		));
		$myForm = $myLoginFormContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$values = $myForm->getValues();
				if($userService->loginUser($values['login'], md5($values['password']))){
					if(!empty($values['processUri']) && !empty($values['activityUri'])){
						$this->redirect(_url('index', 'ProcessBrowser', 'wfEngine', array(
								'processUri' => urlencode($values['processUri']),
								'activityUri' => urlencode($values['activityUri'])
							)
						));
					}else{
						$this->redirect(_url('index', 'Main'));
					}
				}
				else{
					$this->setData('errorMessage', __('No account match the given login / password'));
				}
			}
		}
		
		$this->setData('form', $myForm->render());
		$this->setView('login.tpl');
	}


	public function logout()
	{
		// Humpa Lumpa motion twin session destroy .
		if (isset($_COOKIE[session_name()])) {
    		setcookie(session_name(), session_id(), 1, '/');
		}

		// Finally, destroy the session.
		session_unset();

		$this->redirect(_url('index', 'Authentication'));
	}
}
?>
