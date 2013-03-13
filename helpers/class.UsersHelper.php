<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);\n *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

class wfEngine_helpers_UsersHelper
{

	/**
	 * Double authentication: loggin into tao and the wfengine and init the API connections
	 * @param string $in_login
	 * @param string $in_password
	 * @return boolean
	 */
	public static function authenticate($in_login, $in_password){

		$userService = wfEngine_models_classes_UserService::singleton();
		$session = PHPSession::singleton();
		
		//loggin into tao 
		if($userService->loginUser($in_login, $in_password, false)){
		
			//get the user in the session
			$currentUser = $userService->getCurrentUser($session->getAttribute(tao_models_classes_UserService::LOGIN_KEY));
			
			//init the languages
			core_kernel_classes_Session::singleton()->setDataLanguage($userService->getUserLanguage($currentUser['login']));
			
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
		$userService = core_kernel_users_Service::singleton();
		$wfUserService = wfEngine_models_classes_UserService::singleton();
		$currentUser = $wfUserService->getCurrentUser();
		
		// username.
		$data['username'] 	= (string)$currentUser->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN));
	
		// user roles.
		$data['roles']		= array();
		$roles = $userService->getUserRoles($currentUser);
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