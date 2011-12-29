<?php
class wfEngine_actions_Authentication extends Module
{
    
    /**
     * Users Service
     * @var type wfEngine_models_classes_UserService
     */
    protected $userService;
    
    /**
     * Action constructor
     */
    public function __construct()
    {
         $this->userService = wfEngine_models_classes_UserService::singleton();
    }
    
	/**
	 * WfEngine Login controler
	 */
	public function index()
	{

		if($this->hasRequestParameter('errorMessage')){
			$this->setData('errorMessage',$this->getRequestParameter('errorMessage'));
		}		
		
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
				if($this->userService->loginUser($values['login'], md5($values['password']))){
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

    /**
     * Login a user to the workflow engine through an ajax request
     */
    public function login()
    {
        $success = false;
        $message = __('Unable to log in the user');
        if($this->hasRequestParameter('login') && $this->hasRequestParameter('password')){
            if ($this->userService->loginUser($this->getRequestParameter('login'), md5($this->getRequestParameter('password')))){
                $success = true;
                $message = __('User logged in successfully');
            }
        }
        new common_AjaxResponse(array(
            'success'   => $success
            , 'message' => $message
        ));
    }
    
    /**
     * Logout a user
     */
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
