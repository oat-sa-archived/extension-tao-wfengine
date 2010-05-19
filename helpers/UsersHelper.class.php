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

		$userService = tao_models_classes_ServiceFactory::get('tao_models_classes_UserService');
		
		//loggin into tao 
		if($userService->loginUser($in_login, $in_password, false)){
		
			//get the user in the session
			$currentUser = $userService->getCurrentUser(Session::getAttribute(tao_models_classes_UserService::LOGIN_KEY));
				
			//connect the API
			core_control_FrontController::connect($currentUser['login'], $currentUser['password'], DATABASE_NAME);
			
			//init the languages
			core_kernel_classes_Session::singleton()->defaultLg = $userService->getDefaultLanguage();
			core_kernel_classes_Session::singleton()->setLg($userService->getUserLanguage($currentUser['login']));
			
			//log in the wf engines
			$_SESSION["WfEngine"] 		= WfEngine::singleton($currentUser['login'], $currentUser['password']);
			$user = WfEngine::singleton()->getUser();
			if($user == null) {
				return false;
			}
			
			$_SESSION["userObject"] 	= $user;
				
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
		$WfEngine 			= WfEngine::singleton();
		$user 				= $WfEngine->getUser();

		// username.
		
		$data['username'] 	= $user->userName;
	
		// user roles.
		$data['roles']		= array();
		foreach ($user->roles as $role){
			$data['roles'][] = array('uri' 	 => $role->uri,
									 'label' => $role->label);
		}
		
		return $data;
	}

	public static function mayAccessActivity(Activity $activity)
	{
		$WfEngine 	= WfEngine::singleton();
		$user		= $WfEngine->getUser();

		$acceptedRole = $activity->acceptedRole;

		$mayAccess = false;

		foreach ($user->roles as $role)
		{
			if ($acceptedRole->uri == $role->uri)
			{
				$mayAccess = true;
				break;
			}
		}

		return $mayAccess;
	}

	public static function mayAccessProcess(Process $process)
	{
		$mayAccess 	= false;
		$activities = $process->getAllActivities();

		foreach ($activities as $activity)
		{
			if (self::mayAccessActivity($activity))
			{
				$mayAccess = true;
				break;
			}
		}

		return $mayAccess;
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