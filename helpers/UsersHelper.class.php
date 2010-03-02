<?php
class UsersHelper
{

	public static function authenticate($in_login, $in_password){

		
		// New API Connection.
		core_control_FrontController::connect($in_login,md5($in_password), DATABASE_NAME);


		$_SESSION["WfEngine"] 		= WfEngine::singleton($in_login, $in_password);
		$_SESSION["userObject"] 	= WfEngine::singleton()->getUser();
		core_kernel_classes_Session::singleton()->setLg("EN");
			
		// Taoqual authentication and language markers.
		$_SESSION['taoqual.authenticated'] 		= true;
		$_SESSION['taoqual.lang']				= 'EN';
		$_SESSION['taoqual.serviceContentLang'] = 'EN';
		$_SESSION['taoqual.userId']				= $in_login;



		return true;

	}

	public static function buildCurrentUserForView()
	{
		$WfEngine 			= WfEngine::singleton();
		$user 				= $WfEngine->getUser();
		var_dump($user);
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

	public static function checkAuthentication()
	{
		if (!isset($_SESSION['taoqual.authenticated']))
		{	
			self::authenticationRouting();
		}
		
	}

	public static function authenticationRouting()
	{
		$context = Context::getInstance();
		$fromModule = $context->getModuleName();
		$fromAction= $context->getActionName();
		//		$currentHttpRequest = new HttpRequest();
		//		$fromModule = $currentHttpRequest->getModule();
		//		$fromAction = $currentHttpRequest->getAction();

		// From contain the url encoded module and action.
		$fromLoc = urlencode($fromModule . '/' . $fromAction);

		// Params will contain the url encoded current query string.
		$query = urlencode('?' . $_SERVER['QUERY_STRING']);

		$flow = new FlowController();
		$flow->redirect("authentication/index?from=${fromLoc}&fromQuery=${query}");

		//		AdvancedFC::redirection("authentication/index?from=${fromLoc}&fromQuery=${query}");

	}

	public static function informServiceMode()
	{
		header('HTTP/1.1 403 Forbidden');
		echo '<h1>Unauthorized</h1>';
		echo '<p>Service mode is enabled.</p>';
	}
}
?>