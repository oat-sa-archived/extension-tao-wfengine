<?php

class UsersHelper
{

	/**
	 * Double authentication: loggin into tao and the wfengine and init the API connections
	 * @param string $in_login
	 * @param string $in_password
	 * @return boolean
	 */
	public static function authenticate($in_login, $in_password){

		$userService = wfEngine_models_classes_UserService::singleton();
		
		//loggin into tao 
		if($userService->loginUser($in_login, $in_password, false)){
		
			//get the user in the session
			$currentUser = $userService->getCurrentUser(Session::getAttribute(tao_models_classes_UserService::LOGIN_KEY));
				
			//connect the API
			core_control_FrontController::connect($currentUser['login'], $currentUser['password'], DATABASE_NAME);
			
			//init the languages
			core_kernel_classes_Session::singleton()->setLg($userService->getUserLanguage($currentUser['login']));
			
			// Taoqual authentication and language markers.
			$_SESSION['taoqual.authenticated'] 		= true;
			$_SESSION['taoqual.lang']				= 'EN';
			$_SESSION['taoqual.serviceContentLang'] = 'EN';
			$_SESSION['taoqual.userId']				= $in_login;
			
			return true;
		}
		return false;
	}

	public static function buildCurrentUserForView()
	{
		$userService = wfEngine_models_classes_UserService::singleton();
		$roleService = wfEngine_models_classes_RoleService::singleton();
		$currentUser = $userService->getCurrentUser();
		
		// username.
		$data['username'] 	= (string)$currentUser->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN));
	
		// user roles.
		$data['roles']		= array();
		$roles = $roleService->getUserRoles($currentUser);
		foreach($roles as $role){	
			$data['roles'][] = array(
				'uri' 	 => $role->uriResource,
				'label' => $role->getLabel()
			);
		}
		
		return $data;
	}

	/**
	 * check into the session if a user has been authenticated
	 * @return boolean 
	 */
	public static function checkAuthentication(){
		return (isset($_SESSION['taoqual.authenticated']) && core_kernel_users_Service::singleton()->isASessionOpened());
	}


	public static function informServiceMode()
	{
		header('HTTP/1.1 403 Forbidden');
		echo '<h1>Unauthorized</h1>';
		echo '<p>Service mode is enabled.</p>';
	}
}
?>