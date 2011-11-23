<?php

/**
 * ProcessBrowser Controller provide actions that navigate along a process
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

error_reporting(E_ALL);

class wfEngine_actions_ProcessBrowser extends wfEngine_actions_WfModule{
	
	protected $processExecution = null;
	protected $activityExecution = null;
	protected $processExecutionService = null;
	protected $activityExecutionService = null;
	protected $activityExecutionNonce = false;
	protected $autoRedirecting = false;
	
	public function __construct(){
		
		parent::__construct();
		
		$this->processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$this->activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		
		//validate ALL posted values:
		$processExecutionUri = urldecode($this->getRequestParameter('processUri'));
		if(!empty($processExecutionUri) && common_Utils::isUri($processExecutionUri)){
			$processExecution = new core_kernel_classes_Resource($processExecutionUri);
			//check that the process execution is not finished or closed here:
			if($this->processExecutionService->isFinished($processExecution)){
				
				//cannot browse a finished process execution:
				$this->redirectToMain();
				
			}else{
				
				$this->processExecution = $processExecution;
				
				$activityExecutionUri = urldecode($this->getRequestParameter('activityUri'));
				
				if(!empty($activityExecutionUri) && common_Utils::isUri($activityExecutionUri)){
					
					$activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
					$currentActivityExecutions = $this->processExecutionService->getCurrentActivityExecutions($this->processExecution);
					
					//check if it is a current activity exec:
					if(array_key_exists($activityExecutionUri, $currentActivityExecutions)){
						
						$this->activityExecution = $activityExecution;

						//if ok, check the nonce:
						$nc = $this->getRequestParameter('nc');
						if($this->activityExecutionService->checkNonce($this->activityExecution, $nc)){
							$this->activityExecutionNonce = true;
						}else{
							$this->activityExecutionNonce = false;
						}
						
					}else{
						//the provided activity execution is no longer the current one (link may be outdated).
						//the user is redirected to the current activity execution if allowed, 
						//or redirected to "main" if there are more than one allowed current activity execution or none
						$this->autoRedirecting = true;
					}
				}
			}
		}
		
	}
	
	protected function autoredirectToIndex(){
		
		if(is_null($this->processExecution)){
			$this->redirectToMain();
			return;
		}
		//user data for browser view
		$userViewData = UsersHelper::buildCurrentUserForView(); 
		$this->setData('userViewData', $userViewData);
		$this->setData('processExecutionUri', urlencode($this->processExecution->uriResource));
		
		$this->setView('auto_redirecting.tpl');
		
		return;
	}
	
	protected function redirectToIndex(){
		
		if(ENABLE_HTTP_REDIRECT_PROCESS_BROWSER){
			$parameters = array();
			if(!empty($this->activityExecution)){
				$parameters['activityExecutionUri'] = urlencode($this->activityExecution->uriResource);
			}
			$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser', null, $parameters));
		}else{
			$this->index();
		}
		
	}
	
	protected function redirectToMain(){
		Session::removeAttribute("processUri");
		$this->redirect(tao_helpers_Uri::url('index', 'Main'));
	}
	
	public function index(){
		
		if(is_null($this->processExecution)){
			$this->redirectToMain();
			return;
		}
		
		if($this->autoRedirecting){
			$this->autoredirectToIndex();
			return;
		}
				
		/*
		 * known use of Session::setAttribute("processUri") in:
		 * - taoDelivery_actions_ItemDelivery::runner()
		 * - tao_actions_Api::createAuthEnvironment()
		 * TODO: clean usage
		 */
		Session::setAttribute("processUri", $this->processExecution->uriResource);
		
		//user data for browser view
		$userViewData = UsersHelper::buildCurrentUserForView(); 
		$this->setData('userViewData', $userViewData);
		$browserViewData = array(); // general data for browser view.
		
		//init services:
		$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$interactiveServiceService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_InteractiveServiceService');
		
		//get current user:
		$currentUser = $userService->getCurrentUser();
		if(is_null($currentUser)){
			throw new wfEngine_models_classes_ProcessExecutionException("No current user found!");
		}
		
		//get activity execution from currently available process definitions:
		$currentlyAvailableActivityExecutions = $this->processExecutionService->getAvailableCurrentActivityExecutions($this->processExecution, $currentUser, true);
		
		$activityExecution = null;
		if(count($currentlyAvailableActivityExecutions) == 0){
			//no available current activity exec found: no permission or issue in process execution:
			$this->pause();
			return;
		}else{
			if(!is_null($this->activityExecution) && $this->activityExecution instanceof core_kernel_classes_Resource){
				foreach($currentlyAvailableActivityExecutions as $availableActivityExec){
					if($availableActivityExec->uriResource == $this->activityExecution->uriResource){
						$activityExecution = $this->processExecutionService->initCurrentActivityExecution($this->processExecution, $this->activityExecution, $currentUser);
						break;
					}
				}
				if(is_null($activityExecution)){
					//invalid choice of activity execution:
					$this->activityExecution = null;
//					$invalidActivity = new core_kernel_classes_Resource($activityUri);
//					throw new wfEngine_models_classes_ProcessExecutionException("invalid choice of activity definition in process browser {$invalidActivity->getLabel()} ({$invalidActivity->uriResource}). \n<br/> The link may be outdated.");
					$this->autoredirectToIndex();
					return;
				}
			}else{
				if(count($currentlyAvailableActivityExecutions) == 1){
					$activityExecution = $this->processExecutionService->initCurrentActivityExecution($this->processExecution, reset($currentlyAvailableActivityExecutions), $currentUser);
					if(is_null($activityExecution)){
						throw new wfEngine_models_classes_ProcessExecutionException('cannot initiate the activity execution of the unique next activity definition');
					}
				}else{
					//count > 1:
					//parallel branch, ask the user to select activity to execute:
					$this->pause();
					return;
				}
			}
		}
		
		if(!is_null($activityExecution)){
			
			$this->activityExecution = $activityExecution;
			
			$browserViewData['activityExecutionUri']= $activityExecution->uriResource;
			$browserViewData['activityExecutionNonce']= $this->activityExecutionService->getNonce($activityExecution);
			
			//get interactive services (call of services):
			$activityDefinition = $this->activityExecutionService->getExecutionOf($activityExecution);
			$interactiveServices = $activityService->getInteractiveServices($activityDefinition);
			$services = array();
			foreach($interactiveServices as $interactiveService){
				$services[] = array(
					'callUrl'	=> $interactiveServiceService->getCallUrl($interactiveService, $activityExecution),
					'style'		=> $interactiveServiceService->getStyle($interactiveService),
					'resource'	=> $interactiveService,
				);
			}
			$this->setData('services', $services);
			
			//set activity control:
			$controls = $activityService->getControls($activityDefinition);
			$browserViewData['controls'] = array(
				'backward' 	=> in_array(INSTANCE_CONTROL_BACKWARD, $controls),
				'forward'	=> in_array(INSTANCE_CONTROL_FORWARD, $controls)
			);
		
			// If paused, resume it:
			if ($this->processExecutionService->isFinished($this->processExecution)){
				$this->processExecutionService->resume($this->processExecution);
			}
			
			//get process definition:
			$processDefinition = $this->processExecutionService->getExecutionOf($this->processExecution);
			
			// Browser view main data.
			$browserViewData['processLabel'] 			= $processDefinition->getLabel();
			$browserViewData['processExecutionLabel']	= $this->processExecution->getLabel();
			$browserViewData['activityLabel'] 			= $activityDefinition->getLabel();
			$browserViewData['processUri']				= $this->processExecution->uriResource;
			$browserViewData['active_Resource']			="'".$activityDefinition->uriResource."'" ;
			$browserViewData['isInteractiveService'] 	= true;
			$this->setData('browserViewData', $browserViewData);
					
			$this->setData('activity', $activityDefinition);
		
		
			/* <DEBUG> :populate the debug widget */
			if(DEBUG_MODE){
				
				$this->setData('debugWidget', DEBUG_MODE);
				
				$servicesResources = array();
				foreach($services as $service){
					$servicesResources[] = array(
						'resource' => $service['resource'],
						'callUrl'	=> $service['callUrl'],
						'style'	=> $service['style'],
						'input'		=> $interactiveServiceService->getInputValues($interactiveService, $activityExecution),
						'output'	=> $interactiveServiceService->getOutputValues($interactiveService, $activityExecution)
					);
				}
				$variableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
				$this->setData('debugData', array(
						'Activity' => $activityDefinition,
						'ActivityExecution' => $activityExecution,
						'CurrentActivities' => $currentlyAvailableActivityExecutions,
						'Services' => $servicesResources,
						'VariableStack' => $variableService->getAll()
				));
			}
			/* </DEBUG> */

			$this->setView('process_browser.tpl');
		}
	}

	public function back(){
		
		if(is_null($this->processExecution) || is_null($this->activityExecution) || !$this->activityExecutionNonce){
			$this->redirectToIndex();
			return;
		}
		
		$previousActivityExecutions = $this->processExecutionService->performBackwardTransition($this->processExecution, $this->activityExecution);
		
		//reinitiate nonce:
		$this->activityExecutionService->createNonce($this->activityExecution);
		
		if($this->processExecutionService->isFinished($this->processExecution)){
			$this->redirectToMain();
		}
		else if($this->processExecutionService->isPaused($this->processExecution)){
			$this->pause();
		}
		else{
			//look if the next activity execs are from the same definition:
			if(count($previousActivityExecutions) == 1){
				
				$this->activityExecution = reset($previousActivityExecutions);
				
			}else if(count($previousActivityExecutions) > 1){
				
				//check if it is the executions of a single actiivty or not:
				$activityDefinition = null;
				foreach($previousActivityExecutions as $previousActivityExecution){
					if(is_null($activityDefinition)){
						$activityDefinition = $this->activityExecutionService->getExecutionOf($previousActivityExecution);
					}else{
						if($activityDefinition->uriResource != $this->activityExecutionService->getExecutionOf($previousActivityExecution)->uriResource){
							break;
						}
					}
				}
				$this->activityExecution = reset($previousActivityExecutions);
				
			}
			$this->redirectToIndex();
		}
	}

	public function next(){
		
		if(is_null($this->processExecution) || is_null($this->activityExecution) || !$this->activityExecutionNonce){
			$this->redirectToIndex();
			return;
		}
		
		$nextActivityExecutions = $this->processExecutionService->performTransition($this->processExecution, $this->activityExecution);
		
		//reinitiate nonce:
		$this->activityExecutionService->createNonce($this->activityExecution);
		
		if($this->processExecutionService->isFinished($this->processExecution)){
			$this->redirectToMain();
		}
		elseif($this->processExecutionService->isPaused($this->processExecution)){
			$this->pause();
		}
		else{
			//look if the next activity execs are from the same definition:
			if(count($nextActivityExecutions) == 1){
				
				$this->activityExecution = reset($nextActivityExecutions);
				
			}else if(count($nextActivityExecutions) > 1){
				
				//check if it is the executions of a single actiivty or not:
				$activityDefinition = null;
				foreach($nextActivityExecutions as $nextActivityExecution){
					if(is_null($activityDefinition)){
						$activityDefinition = $this->activityExecutionService->getExecutionOf($nextActivityExecution);
					}else{
						if($activityDefinition->uriResource != $this->activityExecutionService->getExecutionOf($nextActivityExecution)->uriResource){
							break;
						}
					}
				}
				$this->activityExecution = reset($nextActivityExecutions);
				
			}
			$this->redirectToIndex();
		}
	}

	public function pause(){
		
		if(!is_null($this->processExecution)){
			if(!$this->processExecutionService->isPaused($this->processExecution)){
				$this->processExecutionService->pause($this->processExecution);
				//set the current activity execution to pause too...
			}
		}
		
		$this->redirectToMain();
	}
	
	public function loading(){
		$this->setView('activityLoading.tpl');
	}

}
?>