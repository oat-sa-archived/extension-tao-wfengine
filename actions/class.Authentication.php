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
         $this->userService = taoDelivery_models_classes_UserService::singleton();
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
        //log the user
        if($this->hasRequestParameter('login') && $this->hasRequestParameter('password')){
            if ($this->userService->loginUser($this->getRequestParameter('login'), md5($this->getRequestParameter('password')))){
                $success = true;
                $message = __('User logged in successfully');
            }
        }
        
        $currentUser = $this->userService->getCurrentUser();
        var_dump($currentUser);
        return;
        //write the response
        new common_AjaxResponse(array(
            'success'   => $success
            , 'message' => $message
        ));
    }
    
    /**
     * Get information about the current user
     */
    public function info()
    {
        $data = array();
        $success = false;
        $currentUser = $this->userService->getCurrentUser();
        if(!is_null($currentUser)){
            
            $success = true;
            //properties to get
            $properties = array(
                'login' => PROPERTY_USER_LOGIN,
                'uilg' => PROPERTY_USER_UILG,
                'deflg' => PROPERTY_USER_DEFLG,
                'mail' => PROPERTY_USER_MAIL,
                'firstname' => PROPERTY_USER_FIRTNAME,
                'lastname' => PROPERTY_USER_LASTNAME,
            );
            foreach($properties as $label=>$propertyUri){
                $value = $currentUser->getOnePropertyValue(new core_kernel_classes_Property($propertyUri));
                if($value instanceof core_kernel_classes_Resource){
                    $data[$label] = $value->uriResource;
                }else if($value instanceof core_kernel_classes_Literal){
                    $data[$label] = (string)$value;
                }
            }
            //add roles
            $data['roles'] = array();
            foreach($currentUser->getAllPropertyValues(new core_kernel_classes_Property(RDFS_TYPE)) as $type){                
                $data['roles'][] = $type->uriResource;
            }
        }
        //write the response
        new common_AjaxResponse(array(
            'success'   => $success
            , 'data'    => $data
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
