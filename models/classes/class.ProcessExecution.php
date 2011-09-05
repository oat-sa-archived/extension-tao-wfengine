<?php

error_reporting(-1);

/**
 * WorkFlowEngine - class.ProcessExecution.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatic generated with ArgoUML 0.24 on 08.10.2008, 10:46:08
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
	die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000007E9-includes begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000007E9-includes end

/* user defined constants */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000007E9-constants begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000007E9-constants end

/**
 * Short description of class wfEngine_models_classes_ProcessExecution
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class wfEngine_models_classes_ProcessExecution
extends wfEngine_models_classes_WfResource
{
	// --- ATTRIBUTES ---

	/**
	 * Short description of attribute status
	 *
	 * @access public
	 * @var string
	 */
	public $status = '';

	/**
	 * Short description of attribute currentActivity
	 *
	 * @access public
	 * @var array
	 */
	public $currentActivity = array();//should be renamed to $currentActivities

	/**
	 * Short description of attribute process
	 *
	 * @access public
	 * @var Process
	 */
	public $process = null;
	
	protected $activityService = null;
	protected $connectorService = null;

	/**
	 * Short description of attribute variables
	 *
	 * @access public
	 * @var array
	 */
	public $variables = array();

	/**
	 * Short description of attribute path
	 *
	 * @access public
	 * @var wfEngine_models_classes_ProcessPath
	 */
	public $path = null;

	// --- OPERATIONS ---

	/**
	 * Short description of method getVariables
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return array
	 */
	public function getVariables(){
		$returnValue = array();

		// section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008EF begin

		$processVarsProp = new core_kernel_classes_Property(PROPERTY_PROCESS_VARIABLES);
		$processVars = $this->process->resource->getPropertyValues($processVarsProp);

		
		foreach ($processVars as $uriVar)
		{
			
			$var = new core_kernel_classes_Property($uriVar);
			$values = $this->resource->getPropertyValues($var);
			
			$label = $var->getLabel();
			$codeProp = new core_kernel_classes_Property(PROPERTY_PROCESSVARIABLES_CODE);
			$code = $var->getUniquePropertyValue($codeProp);
			
			$actualValue = '';			
			if(count($values)>1){
				$actualValue = serialize($values);
			}else{
				if ((sizeOf($values) > 0) && (trim(strip_tags($values[0])) != ""))
				{
					$actualValue = trim($values[0]);
				}
			}
			
			if(!empty($actualValue)){
				$returnValue[] 	= new Variable($uriVar, $code->literal, $actualValue);
			}
		}

		// section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008EF end
		
		return (array) $returnValue;
	}

	/**
	 * Short description of method performTransition
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function performTransition($activityExecutionUri){
	
		// section 10-13-1--31--4660acca:119ecd38e96:-8000:0000000000000866 begin
		$this->logger->debug('Start Perform Transition ',__FILE__,__LINE__);

		//we should call process->feedFlow method, keep it in session, then reuse attributes instead of querying generis. at each call This imply that currentactivity is a pointer in generis but also a pointer to the object in memory so that we can retrive nec-xt conenctors of the currentactivity, etc...
		//code will be quicker and cleaner

		// Retrieval of process variables values and the current activity.

		//the activity definition is set into cache .. about 0.06 -> 0.01
		//$value = common_Cache::getCache($this->currentActivity[0]->uri);

		Session::setAttribute("activityExecutionUri", $activityExecutionUri);
		$processVars 				= $this->getVariables();//TBR
		$arrayOfProcessVars 		= wfEngine_helpers_ProcessUtil::processVarsToArray($processVars);//used only for conditional connector rule evaluation
		
		//init the services
		$activityExecutionService 	= tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$userService 				= tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
		$tokenService 				= tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
		$notificationService 		= tao_models_classes_ServiceFactory::get('wfEngine_models_classes_NotificationService');
		
		//get the current user
		$currentUser = $userService->getCurrentUser();
		
		
		//new:
		$activityExecutionResource = new core_kernel_classes_Resource($activityExecutionUri);
		$activityDefinition = $activityExecutionResource->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY));
		$activityBeforeTransition 	= new wfEngine_models_classes_Activity($activityDefinition->uriResource);
		
		$activityBeforeTransition->feedFlow(1);

		//set the activity execution of the current user as finished:
		if(!is_null($activityExecutionResource)){
			$activityExecutionResource->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_IS_FINISHED), GENERIS_TRUE);
		}else{
			throw new Exception("cannot find the activity execution of the current activity {$activityBeforeTransition->uri} in perform transition");
		}
		
		
		$nextConnectorUri = $this->getNextConnectorsUri($activityDefinition->uriResource);
		
		$token = $tokenService->getCurrent($activityExecutionResource);
		$arrayOfProcessVars[VAR_PROCESS_INSTANCE] = $token->uriResource;
		$newActivities = $this->getNewActivities($arrayOfProcessVars, $nextConnectorUri);
		
		if($newActivities === false){
			//means that the process must be paused:
			$this->pause();
			return;
		}
		
		// The actual transition starts here:
		
		$connector = null;
		if(!empty($nextConnectorUri)){
			$connector = new core_kernel_classes_Resource($nextConnectorUri);
		
			$nextActivities = array();
			foreach($newActivities as $newActivity){
				$nextActivities[] = $newActivity->resource;
			}
			
			//transition done here the tokens are "moved" to the next step: even when it is the last, i.e. newActivity empty
			$tokenService->move($connector, $nextActivities, $currentUser, $this->resource);
			
			//trigger the notifications
			$notificationService->trigger($connector, $this->resource);
			
		}
		
		//transition done: now get the following activities:
		
		
		//get the current activities, whether the user has the right or not:
		$this->currentActivity = array();
		foreach($tokenService->getCurrentActivities($this->resource) as $currentActivity){
			
			$newActivity = new wfEngine_models_classes_Activity($currentActivity->uriResource);
			
			$this->path->invalidate($activityBeforeTransition, ($this->path->contains($newActivity) ? $newActivity : null));
			// We insert in the ontology the last activity in the path stack.
			$this->path->insertActivity($newActivity);
			
			$this->currentActivity[] = $newActivity;
		}
		
		//if the connector is not a parallel one, let the user continue in his current branch and prevent the pause:
		$uniqueNextActivity = null;
		if(!is_null($connector)){
			$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
			
			if($connectorType->uriResource != INSTANCE_TYPEOFCONNECTORS_PARALLEL){
				
				if(count($newActivities)==1){
					//TODO: could do a double check here: if($newActivities[0] is one of the activty found in the current tokens):
					
					if($activityExecutionService->checkAcl($newActivities[0]->resource, $currentUser, $this->resource)){
						
						$uniqueNextActivity = $newActivities[0];//the Activity Object
					}
				}
			}
		}
		
		
		$setPause = true;
		$authorizedActivityDefinitions = array();
		if (!count($newActivities) || $activityBeforeTransition->isLast()){
			//there is no following activity so the process ends here:
			$this->finish();
			return;
		}elseif(!is_null($uniqueNextActivity)){
			
			//we are certain what the next activity would be for the user so return it:
			$authorizedActivityDefinitions[] = $uniqueNextActivity;
			$this->currentActivity = array();
			$this->currentActivity[] = $uniqueNextActivity;
			$setPause = false;
		}else{
			
			foreach ($this->currentActivity as $activityAfterTransition){
				//check if the current user is allowed to execute the activity
				if($activityExecutionService->checkAcl($activityAfterTransition->resource, $currentUser, $this->resource)){
					$authorizedActivityDefinitions[] = $activityAfterTransition;
					$setPause = false;
				}
				else{
					continue;
				}
			}
			
		}
		
		//finish actions on the authorized acitivty definitions
		foreach($authorizedActivityDefinitions as $activityAfterTransition){
			// The process is not finished.
			// It means we have to run the onBeforeInference rule of the new current activity.
			
			$activityAfterTransition->feedFlow(1);


			// Last but not least ... is the next activity a machine activity ?
			// if yes, we perform the transition.
			/*
			 * @todo to be tested
			 */
			if ($activityAfterTransition->isHidden){
				//required to create an activity execution here with:
				
				$currentUser = $userService->getCurrentUser();
				if(is_null($currentUser)){
					throw new Exception("No current user found!");
				}
				//security check if the user is allowed to access this activity
				// if(!$activityExecutionService->checkAcl($activity->resource, $currentUser, $this->resource)){
					// Session::removeAttribute("processUri");
					// $this->redirect(_url('index', 'Main'));
				// }//already performed above...
				
				$activityExecutionResource = $activityExecutionService->initExecution($activityAfterTransition->resource, $currentUser, $this->resource);
				if(!is_null($activityExecutionResource)){
					$this->performTransition($activityExecutionResource->uriResource);
				}else{
					throw new wfEngine_models_classes_WfException('the activit execution cannot be create for the hidden activity');
				}
				
				
				//service not executed? use curl request?
			}
		}
		
		if($setPause){
			$this->pause();
		}else{
			$this->resume();
		}
		
		// section 10-13-1--31--4660acca:119ecd38e96:-8000:0000000000000866 end
	}
	
	/**
	 * @param $arrayOfProcessVars
	 * @param $nextConnectorUri
	 * @return array of Activity or Boolean (false) 
	 */
	private function getNewActivities($arrayOfProcessVars, $nextConnectorUri)
	{
		$newActivities = array();
		if(empty($nextConnectorUri)){
			return $newActivities;//certainly the last activity
		}
		
		$connector = new wfEngine_models_classes_Connector($nextConnectorUri);
		
		$connType = $connector->getType();
		if(!($connType instanceof core_kernel_classes_Resource)){
			throw new common_Exception('Connector type must be a Resource');
		}
		$this->logger->debug('Next Connector Type : ' . $connType->getLabel(),__FILE__,__LINE__);
		
		
		switch ($connType->uriResource) {
			case INSTANCE_TYPEOFCONNECTORS_CONDITIONAL : {
				$newActivities = $this->getSplitConnectorNewActivity($arrayOfProcessVars,$nextConnectorUri);
				break;
			}
			case INSTANCE_TYPEOFCONNECTORS_PARALLEL : {

				$nextActivitesCollection = $connector->getNextActivities();
				
				foreach ($nextActivitesCollection->getIterator() as $activityResource){
					$newActivities[] = 	new wfEngine_models_classes_Activity($activityResource->uriResource);
				}
				
				break;
			}
			case INSTANCE_TYPEOFCONNECTORS_JOIN : {
			
				$completed = false;
				
				//count the number of each different activity definition that has to be done parallely:
				$activityResourceArray = array();
				$prevActivitesCollection = $connector->getPreviousActivities();
				foreach ($prevActivitesCollection->getIterator() as $activityResource){
					if($this->activityService->isActivity($activityResource)){
						if(!isset($activityResourceArray[$activityResource->uriResource])){
							$activityResourceArray[$activityResource->uriResource] = 1;
						}else{
							$activityResourceArray[$activityResource->uriResource] += 1;
						}
					}
				}
				
				$debug = array();
				$tokenClass = new core_kernel_classes_Class(CLASS_TOKEN);
				$propActivityExecIsFinished = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_IS_FINISHED);
				$propActivityExecProcessExec = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION);
				$activityExecutionClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);				
				foreach($activityResourceArray as $activityDefinition=>$count){
					//get all activity execution for the current activity definition and for the current process execution indepedently from the user (which is not known at the authoring time)
					
					//get the collection of the activity executions performed for the given actiivty definition:
					
					$activityExecutions = $activityExecutionClass->searchInstances(array(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY => $activityDefinition), array('like'=>false));
					
					$activityExecutionArray = array();
					$debug[$activityDefinition] = array();
					foreach($activityExecutions as $activityExecutionResource){
						$processExecutionResource = $activityExecutionResource->getOnePropertyValue($propActivityExecProcessExec);
						
						$debug[$activityDefinition][$activityExecutionResource->getLabel()] = $processExecutionResource->getLabel().':'.$processExecutionResource->uriResource;
						// $debug[$activityDefinition]['$this->resource->uri'] = $this->resource->uri;
							
						if(!is_null($processExecutionResource)){
							if($processExecutionResource->uriResource == $this->resource->uriResource){
								//check if the activity execution is associated to a token: 
								//take the activity exec into account only if it is the case:
								$tokens = $tokenClass->searchInstances(array(PROPERTY_TOKEN_ACTIVITYEXECUTION => $activityExecutionResource->uriResource), array('like' => false));
								if(count($tokens)){
									//found one: check if it is finished:
									$isFinished = $activityExecutionResource->getOnePropertyValue($propActivityExecIsFinished);
									if(!$isFinished instanceof core_kernel_classes_Resource || $isFinished->uriResource == GENERIS_FALSE){
										$completed = false;
										break(2); //leave the $completed value as false, no neet to continue
									}else{
										//a finished activity execution for the process execution
										$activityExecutionArray[] = $activityExecutionResource;
									}
								}
							}
						}
					}
					
					$debug[$activityDefinition]['activityExecutionArray'] = $activityExecutionArray;
					
					if(count($activityExecutionArray) == $count){
						//ok for this activity definiton, continue to the next loop
						$completed = true;
					}else{
						$completed = false;
						break;
					}
				}
				
				if($completed){
					$newActivities = array();
					//get THE (unique) next activity
					$nextActivitesCollection = $connector->getNextActivities();
					foreach ($nextActivitesCollection->getIterator() as $activityResource){
						$newActivities[] = new wfEngine_models_classes_Activity($activityResource->uriResource);//normally, should be only ONE, so could actually break after the first loop
					}
				}else{
					//pause, do not allow transition so return boolean false
					return false;
				}
				
				break;
			}
			default : {
				//considered as a sequential connector
				foreach ($connector->getNextActivities()->getIterator() as $val){
					$this->logger->debug('Next Activity  Name: ' . $val->getLabel(),__FILE__,__LINE__);
					$this->logger->debug('Next Activity  Uri: ' . $val->uriResource,__FILE__,__LINE__);
					
					if($this->activityService->isActivity($val)){
						$activity = new wfEngine_models_classes_Activity($val->uriResource);
						$newActivities[]= $activity;
					}else if($this->connectorService->isConnector($val)){
						$newActivities = $this->getNewActivities($arrayOfProcessVars, $val->uriResource);
					}
					
					if(!empty($newActivities)){
						break;//since it is a sequential one, stop at the first valid loop:
					}
				}
				break;
			}
		}
		
		return $newActivities;
	}

	/**
	 * @param $arrayOfProcessVars
	 * @param $connUri
	 * @return unknown_type
	 */
	private function getSplitConnectorNewActivity($arrayOfProcessVars,$connUri) {

		$newActivities = array();
		// We get the TransitionRule relevant to the connector.
		$connector = new wfEngine_models_classes_Connector($connUri);
	
		$transitionRule 	= $connector->transitionRule;
		
		//remove the 1st arg "getSplitConnectorNewActivity", and call get ProvessVars directly here
		$evaluationResult 	= $transitionRule->getExpression()->evaluate($arrayOfProcessVars);


		if ($evaluationResult)	{

			// next activities = THEN
			
			if ($transitionRule->thenActivity instanceof wfEngine_models_classes_Activity)
			{
				$newActivities[] = $transitionRule->thenActivity;

			}
			else
			{
				$connectorUri = $transitionRule->thenActivity->uri;
				$newActivities = $this->getNewActivities($arrayOfProcessVars, $connectorUri);
			}
		}
		else
		{
			// next activities = ELSE
			if ($transitionRule->elseActivity instanceof wfEngine_models_classes_Activity)
			{
				$newActivities[] = $transitionRule->elseActivity;
			}
			else
			{
				$connectorUri = $transitionRule->elseActivity->uri;
				$newActivities = $this->getNewActivities($arrayOfProcessVars, $connectorUri);
			}

		}
		return $newActivities;
	}


	/**
	 * builds $this->currentactivities an array of activityExecution
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @param string
	 * @param boolean
	 * @return void
	 */
	public function __construct($uri, $feed = true)
	{
		// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008BD begin
		parent::__construct($uri);
		$this->activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$this->connectorService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ConnectorService');	
		$this->resource = new core_kernel_classes_Resource($uri,__METHOD__);
		//getexecutionOf field
		$executionOfProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_EXECUTIONOF);
		$values = $this->resource->getPropertyValues($executionOfProp);
		
		foreach ($values as $a => $b)
		{
			$process 		= new wfEngine_models_classes_Process($b);
			$this->process 	= $process;
		}

		//added for optimization
		if ($feed)
		{
			$this->feed();

		}
		// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008BD end
	}

	/**
	 * Short description of method resume
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function resume()
	{
		// section 10-13-1--31-7f1456d9:11a242e5517:-8000:0000000000000F26 begin

		// Status handling.

		$statusProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_STATUS);
		$this->resource->editPropertyValues($statusProp,INSTANCE_PROCESSSTATUS_RESUMED);
		$this->status = "Resumed";

		// -- Exit code handling: what is it for?
		// $exitCodeProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCE_EXITCODE);
		// $this->resource->removePropertyValues($exitCodeProp);



		// section 10-13-1--31-7f1456d9:11a242e5517:-8000:0000000000000F26 end
	}


	/**
	 * Short description of method pause
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function pause()
	{
		// section 10-13-1-85-746e873e:11bb0a6f076:-8000:00000000000009A5 begin

		// -- Status handling.
		$statusProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_STATUS);
		$this->resource->editPropertyValues($statusProp,INSTANCE_PROCESSSTATUS_PAUSED);
		$this->status = 'Paused';

		// section 10-13-1-85-746e873e:11bb0a6f076:-8000:00000000000009A5 end
	}



	/**
	 * @param $uri
	 * @return unknown_type
	 */
	private function getNextConnectorsUri($activityUri){
		
		$returnValue = '';
		
		$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$connectors = $connectorClass->searchInstances(array(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES => $activityUri), array('like' => false, 'recursive' => 0));
			
		$countConnectors = count($connectors);
		if($countConnectors > 1){
			//there might be a join connector among them or an issue
			$connectorsUri = array();
			foreach ($connectors as $connector){
				$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
				//drop the connector join for now 
				//(a join connector is considered only when it is only one found, i.e. the "else" case below)
				if($connectorType->uriResource != INSTANCE_TYPEOFCONNECTORS_JOIN){
					$connectorsUri[] = $connector->uriResource;
				}else{
					//warning: join connector:
					$connectorsUri = array($connector->uriResource);
					break;
				}
			}
			
			if(count($connectorsUri) == 1){
				//ok, the unique next connector has been found
				$returnValue = $connectorsUri[0];
			}
		}else if($countConnectors == 1){
			$returnValue = array_shift($connectors)->uriResource;
		}else{
			//it is the final activity
		}
		
		return $returnValue;
	}

	/**

	*
	* Short description of method getNextConnectors
	*
	* @access private
	* @author firstname and lastname of author, <author@example.org>
	* @return array
	 * moved to activity service and never used
	*/
	private function getNextConnectors()
	{
		$returnValue = array();

		// section 10-13-1-85--3c82cee5:11bb0c5945c:-8000:00000000000009AB begin
		$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$nextConnectors = $connectorClass->searchInstances(array(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES => $this->currentActivity[0]->uri), array('like' => false));

		$connectors = array();
		foreach ($nextConnectors as $connector){
			$newConn = new wfEngine_models_classes_Connector($connector->uriResource);
			$newConn->feedFlow(1);
			$connectors[] = $newConn;
		}

		$returnValue = $connectors;
		// section 10-13-1-85--3c82cee5:11bb0c5945c:-8000:00000000000009AB end

		return (array) $returnValue;
	}

	/**
	 * Short description of method isFinished
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return boolean
	 */
	public function isFinished()
	{
		$returnValue = (bool) false;

		// section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A09 begin
		$returnValue = ($this->status == 'Finished');
		// section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A09 end

		return (bool) $returnValue;
	}
	
	/**
	 * Check if the status is in pause
	 *
	 * @access public
	 * @author Bertrand Chevrier
	 * @return boolean
	 */
	public function isPaused()
	{
		$returnValue = (bool) false;

		// section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A09 begin
		$returnValue = ($this->status == 'Paused');
		// section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A09 end

		return (bool) $returnValue;
	}

	/**
	 * Short description of method performBackwardTransition
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function performBackwardTransition(wfEngine_models_classes_Activity $from)
	{
		// section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A4D begin

		$activity = $this->path->getActivityBefore($from);
		// Only go backward if there is an activity before the "from Activity".
		// If you persist in doing so, your process current token will be set
		// in the digital nirvana...
		if (null != $activity)
		{
			$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
			$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
			$user = $userService->getCurrentUser();
			
			$tokenService->setCurrentActivities($this->resource, array(new core_kernel_classes_Resource($activity)), $user);
			
			$this->currentActivity = array();
			$beforeActivity = new wfEngine_models_classes_Activity($activity);
			$tokenService->moveBack($from->resource, $beforeActivity->resource,$user, $this->resource);
			$this->currentActivity[] = $beforeActivity;
			

			if ($beforeActivity->isHidden && !$beforeActivity->isFirst())
			{
				$this->performBackwardTransition($beforeActivity);
			}
		}
	}


	/**
	 * Short description of method finish
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	private function finish()
	{
		// section 10-13-1-85-19c5934a:11cae6d4e92:-8000:0000000000000A28 begin
		// -- Status handling
		$statusProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_STATUS);
		$this->resource->editPropertyValues($statusProp, INSTANCE_PROCESSSTATUS_FINISHED);

		$this->status = 'Finished';
		
		//remove the current tokens
		$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
		$tokenService->setCurrents($this->resource, array());
		

		// section 10-13-1-85-19c5934a:11cae6d4e92:-8000:0000000000000A28 end
	}

	/**
	 * set attributes to the object
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function feed()
	{
		// section 10-13-1--31--7b61b039:11cdba08b1e:-8000:0000000000000A30 begin
		
		$tokenService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_TokenService');
		foreach($tokenService->getCurrentActivities($this->resource) as $activity)
		{
			$this->currentActivity[] 	= new wfEngine_models_classes_Activity($activity->uriResource);
		}
		
		$statusProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_STATUS);
		$status = $this->resource->getPropertyValues($statusProp);

		//add status information
		if (sizeOf($status)>0)
		{
			switch ($status[0])
			{
				case INSTANCE_PROCESSSTATUS_RESUMED : 	{ $this->status = "Resumed"; break; }
				case INSTANCE_PROCESSSTATUS_STARTED : 	{ $this->status = "Started"; break; }
				case INSTANCE_PROCESSSTATUS_FINISHED : 	{ $this->status = "Finished"; break; }
				case INSTANCE_PROCESSSTATUS_PAUSED :	{ $this->status = "Paused" ;break; }
			}
		}

		// Build the path of the process execution.
		$this->path = new wfEngine_models_classes_ProcessPath($this);

		// section 10-13-1--31--7b61b039:11cdba08b1e:-8000:0000000000000A30 end
	}


	public function isBackable()
	{
		$backable = false;
		$previousActivity = $this->path->getActivityBefore($this->currentActivity[0]);
		if ($previousActivity)
		{
			$previousActivity = new wfEngine_models_classes_Activity($previousActivity);
		}
		else
		{
			return false;
		}
			
		while($previousActivity)
		{
			$scannedActivity = $previousActivity;

			if (!$scannedActivity->isHidden)
			{
				$backable = true;
				break;
			}

			$previousActivity = $this->path->getActivityBefore($scannedActivity);
			if ($previousActivity)
			{
				$previousActivity = new wfEngine_models_classes_Activity($previousActivity);
			}
		}
			
		return $backable;
	}

} /* end of class wfEngine_models_classes_ProcessExecution */

?>