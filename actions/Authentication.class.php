<?php
class Authentication extends Module
{
	public function index()
	{
		// If the client arrives here, it's maybe because he tried to reach an action
		// in which you need to be authenticated. In this case, the query string should
		// contain parameter named 'from' that contains the urlencoded value of the
		// 'module/action' that was invoked when the authentication process failed.
		// If 'from' exists in the query string, then the 'fromQuery' parameter must
		// be in the query string. 'fromQuery' contains the url encoded value of the
		// query string used to invoke the action where in the user was not successfuly
		// authenticated.
		//
		// 'from' and 'fromQuery' will be injected in the view as hidden 'input' elements.
		// In this was, the login action will receive these parameters and will be able
		// to route the client to the view where in he tried to be authenticated before
		// being redirected to the authentication process.
		$indexViewData = array();
		$indexViewData['route'] = false;

		if (isset($_GET['from']) && isset($_GET['fromQuery']))
		{
			$indexViewData['route']			= true;
			$indexViewData['from'] 			= $_GET['from'];
			$indexViewData['fromQuery'] 	= $_GET['fromQuery'];
		}
		$this->setData('indexViewData',$indexViewData);
		$this->setView('login.tpl');
	}

	public function login($in_login, $in_password)
	{
		// We connect to generis.
		if (UsersHelper::authenticate($in_login,$in_password))
		{
			// Login should be successful ...
			// If Piaac is enabled, we have to invalidate the Piaac cache
			// to be sure it is "fresh enough".
			if (defined('PIAAC_ENABLED')) PiaacDataHolder::invalidateCache();

			// If we are here, the login process succeeded. So we redirect the user
			// to the UserFrontend main web form or to the location specified
			// by the 'from' and 'fromQuery' parameters.
			if (isset($_POST['route']) && $_POST['route'] == 'true')
				GenerisFC::redirection($_POST['from'] . $_POST['fromQuery']);
			else{
				
				if (defined('PIAAC_ENABLED') && SERVICE_MODE && ($processBoundUri = getProcessExecutionUriBoundToUser($in_login)))
				{
					GenerisFC::redirection('processBrowser/index?processUri=' . urlencode($processBoundUri));
				}
				else
				{
					GenerisFC::redirection('main/index');
				}
			}

		}
		else
		{
			// The user has to provide valid indentification information.
			GenerisFC::redirection('authentication/index');
		}
	}

	public function logout()
	{
		// Humpa Lumpa motion twin session destroy .
		if (isset($_COOKIE[session_name()])) {
    		setcookie(session_name(), session_id(), 1, '/');
		}

		// Finally, destroy the session.
		session_destroy();

		GenerisFC::redirection('authentication/index');
	}
}
?>
