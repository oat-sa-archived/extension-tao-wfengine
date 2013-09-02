<?php
/**  
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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Exposes the workflow runner as a service
 * 
 * @author Joel Bout, <joel@taotesting.com>
 */
class wfEngine_actions_ProcessRunner extends wfEngine_actions_WfModule{
	
	public function __construct(){
	    tao_helpers_Context::load('STANDALONE_MODE');
		parent::__construct();
	}
	public function index(){
		
		set_time_limit(200);
			
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		
		$processDefinitionUri = $this->getRequestParameter('processDefinition');
		$processDefinition = new core_kernel_classes_Resource($processDefinitionUri);

		$processExecName = $processDefinition->getLabel();
		$processExecComment = 'Created in Processes server on ' . date(DATE_ISO8601);
		
		if (isset($_REQUEST['processVariables']) && !empty($_REQUEST['processVariables'])) {
		    $processVariables = json_decode($_REQUEST['processVariables'], true);
		    $processVariables = is_array($processVariables) ? $processVariables : array();
		} else {
		    // none provided
		    $processVariables = array();
		}

		$newProcessExecution = $processExecutionService->createProcessExecution($processDefinition, $processExecName, $processExecComment, $processVariables);
		
		//create nonce to initial activity executions:
		foreach($processExecutionService->getCurrentActivityExecutions($newProcessExecution) as $initialActivityExecution){
			$activityExecutionService->createNonce($initialActivityExecution);
		}

		$param = array('processUri' => urlencode($newProcessExecution->getUri()), 'standalone' => 'true');
		$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser', null, $param));
	}

}