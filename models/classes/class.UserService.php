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
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Manage the user in the workflow engine
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_UserService
    extends tao_models_classes_UserService
{

    /**
     * Overrides tao_models_classes_UserService to restrict authenticable users to those
     * having the Workflow role.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return array
     */
    public function getAllowedRoles()
    {
		return array(INSTANCE_ROLE_WORKFLOW => new core_kernel_classes_Resource(INSTANCE_ROLE_WORKFLOW));
    }
    
    /**
     * New users will be Workflow Users.
     * 
     * @return core_kernel_classes_Class The class to use to instantiate new users.
     */
    public function getUserClass()
    {
    	return new core_kernel_classes_Class(CLASS_WORKFLOWUSER);
    }

    /**
     * method to format the data
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Class clazz
     * @param  array options
     * @return array
     */
    public function toTree( core_kernel_classes_Class $clazz, $options)
    {
        $returnValue = array();

        $users = $this->getAllUsers(array('order' => 'login'));
		foreach($users as $user){
			$login = (string) $user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN));
			$returnValue[] = array(
					'data' 	=> tao_helpers_Display::textCutter($user->getLabel(), 16),
					'attributes' => array(
						'id' => tao_helpers_Uri::encode($user->uriResource),
						'class' => 'node-instance',
						'title' => __('login: ').$login
					)
				);

		}

        return (array) $returnValue;
    }

}

?>