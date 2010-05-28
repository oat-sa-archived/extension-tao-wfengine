<?php

error_reporting(E_ALL);

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

/**
 * include Activity
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.Activity.php');

/**
 * include ActivityExecution
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.ActivityExecution.php');

/**
 * include ProcessPath
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.ProcessPath.php');

/**
 * include Process
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.Process.php');

/**
 * include Variable
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.Variable.php');

/**
 * include WfResource
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.WfResource.php');

/* user defined includes */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000007E9-includes begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000007E9-includes end

/* user defined constants */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000007E9-constants begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000007E9-constants end

/**
 * Short description of class ProcessExecution
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class ProcessExecution
extends WfResource
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
	public $currentActivity = array();

	/**
	 * Short description of attribute process
	 *
	 * @access public
	 * @var Process
	 */
	public $process = null;

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
	 * @var ProcessPath
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
	public function getVariables()
	{
		$returnValue = array();

		// section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008EF begin

		$processVarsProp = new core_kernel_classes_Property(PROCESS_VARIABLES);
		$processVars = $this->process->resource->getPropertyValues($processVarsProp);

		foreach ($processVars as $uriVar)
		{
			$var = new core_kernel_classes_Property($uriVar);
			$values = $this->resource->getPropertyValues($var);
			if ((sizeOf($values) > 0) && (trim(strip_tags($values[0])) != ""))
			{
				$label = $var->getLabel();
				$codeProp = new core_kernel_classes_Property(PROPERTY_CODE);
				$code = $var->getUniquePropertyValue($codeProp);
				$returnValue[] 	= new Variable($uriVar, $code->literal, trim($values[0]));
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
	public function performTransition($ignoreConsistency = false)
	{

		// section 10-13-1--31--4660acca:119ecd38e96:-8000:0000000000000866 begin
		$this->logger->debug('Start Perform Transition ',__FILE__,__LINE__);

		//we should call process->feedFlow method, keep it in session, then reuse attributes instead of querying generis. at each call This imply that currentactivity is a pointer in generis but also a pointer to the object in memory so that we can retrive nec-xt conenctors of the currentactivity, etc...
		//code will be quicker and cleaner

		// Retrieval of process variables values and the current activity.

		//the activity definition is set into cache .. about 0.06 -> 0.01
		//$value = common_Cache::getCache($this->currentActivity[0]->uri);

		
		$processVars 				= $this->getVariables();
		$arrayOfProcessVars 		= Utils::processVarsToArray($processVars);
		$curentTokenProp = new core_kernel_classes_Property(CURRENT_TOKEN);
		
		
			
		
			$activityBeforeTransition 	= new Activity($this->currentActivity[0]->uri);
	
			$activityBeforeTransition->feedFlow(1);
	
			//common_Cache::setCache($activityBeforeTransition,$this->currentActivity[0]->uri);
	

	
	
			// ONAFTER INFERENCE RULE
			// If we are here, no consistency error was thrown. Thus, we can infer something if needed.
			foreach ($activityBeforeTransition->inferenceRule as $rule)
			{
				$rule->execute($arrayOfProcessVars);
			}
	
			
			// -- ONAFTER CONSISTENCY CHECKING
			// First of all, we check if the consistency rule is respected.
			if (!$ignoreConsistency && $activityBeforeTransition->consistencyRule)
			{
				$consistencyRule 		= $activityBeforeTransition->consistencyRule;
				$consistencyCheckResult = $consistencyRule->getExpression()->evaluate($arrayOfProcessVars);
				$activityToGoBack		= null;
	
				if ($consistencyCheckResult)
				{
					// Were do we jump back ?
					if ($activityBeforeTransition->isHidden)
					{
						$activityToGoBack = Utils::getLastViewableActivityFromPath($this->path->activityStack,
						$activityBeforeTransition->uri);
					}
					else
					{
						$activityToGoBack = $activityBeforeTransition;
					}
	
					//the consistency notification is updated with the actual values of variables
					$activeLiteral = new core_kernel_classes_ActiveLiteral($consistencyRule->notification);
					$consistencyRule->notification = $activeLiteral->getDisplayedText($arrayOfProcessVars);
	
					// If the consistency result is negative, we throw a ConsistencyException.
					$consistencyException = new ConsistencyException('The consistency test was negative',
																	$activityBeforeTransition,
																	$consistencyRule->involvedActivities,
																	$consistencyRule->notification,
																	$consistencyRule->suppressable);
	
					// The current token must be the activity we are jumping back 		
					$this->resource->editPropertyValues($curentTokenProp,$activityToGoBack->uri);
	
	
					throw $consistencyException;
				}
			
		}

		$connectorsUri = $this->getNextConnectorsUri($this->currentActivity[0]->uri);
		
		$arrayOfProcessVars[VAR_PROCESS_INSTANCE] = $this->resource->uriResource;
		$newActivities = $this->getNewActivities($arrayOfProcessVars, $connectorsUri);


		$this->resource->removePropertyValues($curentTokenProp);

		$this->currentActivity = array();

		foreach ($newActivities as $newActivity)
		{

			$this->resource->setPropertyValue($curentTokenProp,$newActivity->uri);
			$this->logger->debug('Activiy ' . $newActivity->uri . ' added to current token' ,__FILE__,__LINE__);
		
			$this->path->invalidate($activityBeforeTransition,($this->path->contains($newActivity) ? $newActivity : null));

			// We insert in the ontology the last activity in the path stack.
			$this->path->insertActivity($newActivity);
			$this->currentActivity[] = new Activity($newActivity->uri);

		}

		// If the activity before the transition was the last activity of the process,
		// we have to finish gracefully the process.

		if (!count($newActivities) || $activityBeforeTransition->isLast())
		{
			$this->finish();
		}
		else
		{

			$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
			$userService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_UserService');
			$currentUser = $userService->getCurrentUser();
			
			$setPause = true;
			foreach ($this->currentActivity as $activityAfterTransition) {
				
				//$activityExecutionService->initExecution($activityAfterTransition->resource, $currentUser);
				
				//check if the current user is allowed to execute the activity
				if($activityExecutionService->checkAcl($activityAfterTransition->resource, $currentUser)){
					$setPause = false;
				}
				else{
					continue;
				}
				
				// The process is not finished.
				// It means we have to run the onBeforeInference rule of the new current activity.
				
				$activityAfterTransition->feedFlow(1);
	
				
				// ONBEFORE INFERENCE RULE
				// If we are here, no consistency error was thrown. Thus, we can infer something if needed.
				foreach ($activityAfterTransition->onBeforeInferenceRule as $rule)
				{
					$rule->execute($arrayOfProcessVars);
				}
			
	
				// Last but not least ... is the next activity a machine activity ?
				// if yes, we perform the transition.
				if ($activityAfterTransition->isHidden)
				{
					$this->performTransition($ignoreConsistency);
				}

			}
			if($setPause){
				$this->pause();
			}
		}

		// section 10-13-1--31--4660acca:119ecd38e96:-8000:0000000000000866 end
	}

	/**
	 * @param $arrayOfProcessVars
	 * @param $nextConnectors
	 * @return Activity
	 */
	private function getNewActivities($arrayOfProcessVars, $nextConnectors)
	{
		$newActivities = array();

		foreach ($nextConnectors as $connUri){

			$connector = new Connector($connUri);
			
			$connType = $connector->getType();
			if(!($connType instanceof core_kernel_classes_Resource)){
				throw new common_Exception('Connector type should be a Resource');
			}
			$this->logger->debug('Next Connector Type : ' . $connType->getLabel(),__FILE__,__LINE__);
			
			
			switch ($connType->uriResource) {
				case CONNECTOR_SPLIT : {
					$newActivities = $this->getSplitConnectorNewActivity($arrayOfProcessVars,$connUri);
					break;
				}
				case CONNECTOR_LIST_UP:
				case CONNECTOR_LIST : {

					$connector = new Connector($connUri);
					$newActivities = $this->getListConnectorNewActivity($arrayOfProcessVars,$connector);

					break;
				}
				case INSTANCE_TYPEOFCONNECTORS_PARALLEL : {
						//TODO
						echo 'work in progress';
						$connector = new Connector($connUri);

						$nextActivitesCollection = $connector->getNextActivities();
						foreach ($nextActivitesCollection->getIterator() as $activityResource){
							$newActivities[] = 	$activityResource->uriResource;
						}

						
						break;
				}
				case INSTANCE_TYPEOFCONNECTORS_JOIN : {
					//TODO
						echo 'work in progress';
						$connector = new Connector($connUri);
						$prevActivitesCollection = $connector->getPreviousActivities();
						
						foreach ($prevActivitesCollection->getIterator() as $activityResource){
							
							$apiModel  	= core_kernel_impl_ApiModelOO::singleton();
        	
							$subjects 	= $apiModel->getSubject(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY, $activity->uriResource);
							
							$executionResource =	$subjects->get(0);
							
							$isFinishedProp = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_IS_FINISHED);
							$isFinished = $executionResource->getOnePropertyValue($isFinishedProp);
							
							if(!$isFinished instanceof core_kernel_classes_Resource || $isFinished->uriResource == GENERIS_TRUE){
								$newActivities = false;
								break;
							}
							
						}
						$newActivities = $connector->getNextActivities();
						
						break;
				}
				default : {
					
					foreach ($connector->getNextActivities()->getIterator() as $val)
					{
						$this->logger->debug('Next Activity  Name: ' . $val->getLabel(),__FILE__,__LINE__);
						$this->logger->debug('Next Activity  Uri: ' . $val->uriResource,__FILE__,__LINE__);
						$activity = new Activity($val->uriResource);
						$activity->getActors();
						$newActivities[]= $activity;

					}
					break;
				}
			}

		}

		return $newActivities;
	}





	/**
	 * @param $activitiesExecution
	 * @param $selector
	 * @return ListSelector
	 */
	private function getActivityListSelector(ActivitiesListExecution $activitiesExecution,$selector){
		switch ($selector->uriResource) {
			default: {
				throw new common_Exception('only SEQ SElector currently implemented');
				break;
			}
			case RESOURCE_ACTIVITIES_SELECTOR_SEQ : {

				$listSelector = new SequentialSelector($activitiesExecution);
				break;
			}
			case RESOURCE_ACTIVITIES_SELECTOR_RAND : {
				$finishedActivityListProp = new core_kernel_classes_Property(PROPERTY_FINISHED_ACTIVITIES);
				$collection = $this->resource->getPropertyValuesCollection($finishedActivityListProp);
				$finishedActivityListArray = array();
				if(!$collection->isEmpty()){
					$finishedActivityListArray = unserialize($collection->get(0));
				}
				$listSelector = new RandomSelector($activitiesExecution,-1,$finishedActivityListArray);
				break;
			}
			case RESOURCE_ACTIVITIES_SELECTOR_RAND_1 : {

				$listSelector = new RandomSelector($activitiesExecution,1);
				break;
			}
			case RESOURCE_ACTIVITIES_SELECTOR_DICO : {

				$response = array();

				foreach ($activitiesExecution->getRdfList()->getArray() as $resUri){
					$res = new core_kernel_classes_Resource($resUri);
					$responsePropertyUri = propertyExists($res->getlabel());

					if($responsePropertyUri != false) {
						$responseProperty = new core_kernel_classes_Property($responsePropertyUri,__METHOD__);
						$interviewee = new core_kernel_classes_Resource(getIntervieweeUriByProcessExecutionUri($this->uri));
						$responseCollection = $interviewee->getPropertyValuesCollection($responseProperty);
						if(!$responseCollection->isEmpty()) {
							$answer = $responseCollection->get(0);
							$codeProp = new core_kernel_classes_Property(PROPERTY_CODE);
							$response[$res->uriResource] = $answer->getUniquePropertyValue($codeProp)->literal;
						}
						else{
							$response[$res->uriResource] = null;
						}
					}
				}

				//				var_dump($response);

				$listSelector = new DichotomicSelector($activitiesExecution,$response,$this->currentActivity[0]);


				//				$responsePropertyUri = propertyExists($this->currentActivity[0]->label);
				//				if($responsePropertyUri != false){
				//
				//					$responseProperty = new core_kernel_classes_Property($responsePropertyUri,__METHOD__);
				//					$interviewee = new core_kernel_classes_Resource(getIntervieweeUriByProcessExecutionUri($this->uri));
				//					$responseCollection = $interviewee->getPropertyValuesCollection($responseProperty);
				//					var_dump($responseCollection);
				//					if($responseCollection->isEmpty()) {
				//						// is emty at first time in dichotomy
				//						$listSelector = new DichotomicSelector($activitiesExecution,null,$this->currentActivity[0]);
				//					}
				//					else {
				//
				//						$listSelector = new DichotomicSelector($activitiesExecution,$responseCollection->get(0),$this->currentActivity[0]);
				//
				//					}
				//
				//				}
				//				else {
				//
				//					$listSelector = new DichotomicSelector($activitiesExecution,null,$this->currentActivity[0]);
				//				}


				break;
			}

		}
		return $listSelector;
	}


	/**
	 * @param $connector
	 * @param $down
	 * @return ActivitiesList
	 */
	private function getActivityList(Connector $connector, $down = true) {

		switch ($connector->getType()->uriResource) {
			case CONNECTOR_LIST_UP: {
				$activityListInst = $down ? $connector->getNextActivities()->get(0) : $connector->getPreviousActivities()->get(0);
				break;
			}

			case CONNECTOR_LIST : {
				$activityListInst =  $down ? $connector->getPreviousActivities()->get(0): $connector->getNextActivities()->get(0) ;
				break;
			}
		}

		return new ActivitiesList($activityListInst);
	}


	/**
	 * @param $arrayOfProcessVars
	 * @param $connector
	 * @return unknown_type
	 */
	private function getListConnectorNewActivity($arrayOfProcessVars,Connector $connector){
		//		xdebug_start_trace('E:\work\log\log.xt');
		$newActivities = array();

		$activitiesList = $this->getActivityList($connector,true);
		$logger = new common_Logger('WfEngine Process Execution', Logger::debug_level);
		$logger->info('****Proceeding on list :' . $activitiesList->label,__FILE__,__LINE__);


		$selector = $activitiesList->getSelector();
		$rdfList = $activitiesList->getRdfList();

		$activitiesExecHistoryProp = new core_kernel_classes_Property(CURRENT_TOKEN_EXECUTION);
		$activitiesExecHistoryValues = $this->resource->getPropertyValuesCollection($activitiesExecHistoryProp);



		//First time we discover a list, so we create the connector history list that will stay in memory.
		if($activitiesExecHistoryValues->isEmpty()){
			$logger->info('****Building Connector History :' ,__FILE__,__LINE__);
			//$activitiesExecHistory =  core_kernel_classes_RdfListFactory::create('Connector histoy', 'Connector histoy');
			$activitiesExecHistory = array();
			$remainningList = core_kernel_classes_RdfListFactory::create('Mem List of '. $rdfList->getLabel());
			$activitiesExecution = $activitiesList->createExecution($remainningList);
			$activitiesExecHistory[] = $activitiesExecution->resource->uriResource;
			$this->resource->setPropertyValue($activitiesExecHistoryProp,serialize($activitiesExecHistory));

		}
		else {
			$oldActivitiesExecHistory = unserialize($activitiesExecHistoryValues->get(0));
			$lastExecution = new ActivitiesListExecution(new core_kernel_classes_Resource($oldActivitiesExecHistory[0]));

			$parent = $lastExecution->getParent();
			if( $lastExecution->isUp()
			&& $parent != null
			&& $parent->getParent() != null) {


				$lastExecution->setUp(false);
				$activitiesList = $parent->getParent();
				$selector = $activitiesList->getSelector();
				$rdfList = $activitiesList->getRdfList();
				$logger->info('Back to parent: ' . $activitiesList->resource->uriResource,__FILE__,__LINE__);
				$logger->info('Back to parent: ' . $activitiesList->resource->getLabel(),__FILE__,__LINE__);
			}


			foreach ($oldActivitiesExecHistory as $plop){
				$found = null;
				$execution = new ActivitiesListExecution(new core_kernel_classes_Resource($plop));
				if($execution->getParent()->resource->uriResource == $activitiesList->resource->uriResource) {
					$found = $execution;
					break;
				}
			}

			if($found == null && !$lastExecution->isFinished()) {
				$remainningList = core_kernel_classes_RdfListFactory::create('Mem List of '. $rdfList->getLabel());
				$activitiesExecution = $activitiesList->createExecution($remainningList);
				$logger->debug('Create Mem list of : ' . $rdfList->getLabel(),__FILE__,__LINE__);
				$logger->debug('Create Mem list of : ' . $remainningList->uriResource,__FILE__,__LINE__);

				array_unshift($oldActivitiesExecHistory,$activitiesExecution->resource->uriResource);
				$this->resource->editPropertyValues($activitiesExecHistoryProp,serialize($oldActivitiesExecHistory));
				$logger->debug('Add new list to hitory ' ,__FILE__,__LINE__);
			}
			else {

				$activitiesExecution = new ActivitiesListExecution($found->resource);

				$activitiesExecution->setUp($false);
				$activitiesExecution->restored = true;

				$activitiesExecHistory = array_diff($oldActivitiesExecHistory , array($found->resource->uriResource));
				array_unshift($activitiesExecHistory,$found->resource->uriResource);

				$this->resource->editPropertyValues($activitiesExecHistoryProp,serialize($activitiesExecHistory));
				$logger->debug('set previous list at the top of history' ,__FILE__,__LINE__);

			}

		}
		$logger->debug('List Selector type uri : ' . $selector->uriResource,__FILE__,__LINE__);
		$logger->debug('List Selector type name : ' . $selector->getLabel(),__FILE__,__LINE__);

		//we get the proper selector
		//		echo __FILE__.__LINE__;
		//		var_dump($activitiesExecution,$activitiesExecution->getRdfList()->getArray(),$activitiesExecution->isUp());
		$listSelector = $this->getActivityListSelector($activitiesExecution,$selector);



		//check if the list has some item left we retrieve them

		if($listSelector->hasNext()){
			$nextInst = $listSelector->next();
			//			var_dump($activitiesList,$activitiesList->getParent(),$activitiesExecution->isUp(),$activitiesExecution->isUp());

			$newActivities[] = new Activity($nextInst->uriResource);

			$logger->debug('List Selector has next Element uri : ' . $nextInst->uriResource,__FILE__,__LINE__);
			$logger->debug('List Selector has next Element name : ' . $nextInst->getLabel(),__FILE__,__LINE__);


		}
		//we are at the end of the list
		else {
			$activitiesExecution->setFinished(true);
			$activitiesExecHistory = unserialize($this->resource->getUniquePropertyValue($activitiesExecHistoryProp));
			$finishedActivityListProp = new core_kernel_classes_Property(PROPERTY_FINISHED_ACTIVITIES);
			$collection = $this->resource->getPropertyValuesCollection($finishedActivityListProp);
			$finishedActivityListArray = array();
			if(!$collection->isEmpty()){
				$finishedActivityListArray = unserialize($collection->get(0));
			}
			$finishedActivityListArray[] = $activitiesList->resource->uriResource;

			$this->resource->editPropertyValues($finishedActivityListProp,serialize($finishedActivityListArray));
			$logger->debug('List Selector do not have more element'  . $activitiesList->resource->getLabel(),__FILE__,__LINE__);
			$logger->debug('List Selector do not have more element'  . $activitiesList->resource->uriResource,__FILE__,__LINE__);
			$logger->debug('List is finised, remove it from history' ,__FILE__,__LINE__);

			if(!empty($activitiesExecHistory)){

				// former tail become new history
				array_shift($activitiesExecHistory);
				$this->resource->editPropertyValues($activitiesExecHistoryProp,serialize($activitiesExecHistory));

				//rec call to the next activity, we indicate the activity execution that we go up

				$nextConnectorInst = $this->getActivityList($connector,true)->resource;

				$logger->debug('next connector uri : ' . $nextConnectorInst->uriResource ,__FILE__,__LINE__);
				$logger->debug('next connector uri : ' . $nextConnectorInst->getLabel() ,__FILE__,__LINE__);

				$nextConnector = $this->getNextConnectorsUri($this->getActivityList($connector,true)->resource->uriResource);
				$plop = new Connector($nextConnector[0]);
				if(!$plop->getNextActivities()->isEmpty()) {
					$fatherActivity = $plop->getNextActivities()->get(0);
					$fatherConnector = $this->getNextConnectorsUri($fatherActivity->uriResource);

					$newActivities = $this->getNewActivities($arrayOfProcessVars , $fatherConnector);
					$logger->debug('activity over remove activityExecution'  ,__FILE__,__LINE__);
					$activitiesExecution->setUp(true);
				}
				else {

					$logger->debug('activity over remove activityExecution'  ,__FILE__,__LINE__);
					$activitiesExecution->setUp(true);
					return array();
				}

			}
			else {

				$logger->debug('END' ,__FILE__,__LINE__);


				//				// handle when the activity history is empty i.e. at the end of the test
				//				if($connector->getNextActivities()->isEmpty()){
				//					$this->finish();
				//					$activitiesExecution->setUp(true);
				//					return null;
				//				}
				//
				//				else{
				//					//  one element left in activity history
				////					echo __FILE__.__LINE__;
				//					var_dump($connector->getNextActivities()->get(0));
				//					$nextConnector = $this->getNextConnectorsUri($connector->getNextActivities()->get(0)->uriResource);
				//					$activitiesExecution->setUp(true);
				//
				//					$newActivities = $this->getNewActivities($arrayOfProcessVars , $nextConnector);
				//
				//				}


			}

		}
		//		echo __FILE__.__LINE__; var_dump($newActivities);
		//		xdebug_stop_trace();
		//		$logger->debug('next Activity uri : '. $newActivities[0]->resource->uriResource,__FILE__,__LINE__);
		//		$logger->debug('next Activity name : '. $newActivities[0]->resource->getLabel(),__FILE__,__LINE__);
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
		$connector = new Connector($connUri);
	
		$transitionRule 	= $connector->transitionRule;

		$evaluationResult 	= $transitionRule->getExpression()->evaluate($arrayOfProcessVars);


		if ($evaluationResult)	{

			// Prochaines activit�s = THEN

			if ($transitionRule->thenActivity instanceof Activity)
			{

				$newActivities[] = $transitionRule->thenActivity;

			}
			else
			{
				$connectors = array($transitionRule->thenActivity->uri);
				$newActivities = $this->getNewActivities($arrayOfProcessVars, $connectors);
			}
		}
		else
		{
			// Prochaines activit�s = ELSE
			if ($transitionRule->elseActivity instanceof Activity)
			{
				$newActivities[] = $transitionRule->elseActivity;
			}
			else
			{
				$connectors = array($transitionRule->elseActivity->uri);
				$newActivities = $this->getNewActivities($arrayOfProcessVars, $connectors);
			}

		}

		return $newActivities;
	}

	/**
	 * Short description of method warnNextRole
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @param Activity
	 * @return void
	 */
	private function warnNextRole( Activity $activity)
	{
		// section 10-13-1--31--4660acca:119ecd38e96:-8000:0000000000000869 begin
		// section 10-13-1--31--4660acca:119ecd38e96:-8000:0000000000000869 end
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
		$this->resource = new core_kernel_classes_Resource($uri,__METHOD__);
		//getexecutionOf field
		$executionOfProp = new core_kernel_classes_Property(EXECUTION_OF);
		$values = $this->resource->getPropertyValues($executionOfProp);
		
		foreach ($values as $a => $b)
		{
			$process 		= new Process($b);
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

		$statusProp = new core_kernel_classes_Property(STATUS);
		$this->resource->editPropertyValues($statusProp,RESOURCE_PROCESSSTATUS_RESUMED);
		$this->status = "Resumed";

		// -- Exit code handling.
		$exitCodeProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCE_EXITCODE);
		$this->resource->removePropertyValues($exitCodeProp);



		// section 10-13-1--31-7f1456d9:11a242e5517:-8000:0000000000000F26 end
	}

	/**
	 * Short description of method back
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function back()
	{
		// section 10-13-1-85-746e873e:11bb0a6f076:-8000:00000000000009A3 begin
		// section 10-13-1-85-746e873e:11bb0a6f076:-8000:00000000000009A3 end
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
		$statusProp = new core_kernel_classes_Property(STATUS);
		$this->resource->editPropertyValues($statusProp,RESOURCE_PROCESSSTATUS_PAUSED);
		$this->status = 'Paused';


		// section 10-13-1-85-746e873e:11bb0a6f076:-8000:00000000000009A5 end
	}

	/**
	 * Short description of method restart
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function restart()
	{
		// section 10-13-1-85-746e873e:11bb0a6f076:-8000:00000000000009A7 begin
		// section 10-13-1-85-746e873e:11bb0a6f076:-8000:00000000000009A7 end
	}




	/**
	 * @param $uri
	 * @return unknown_type
	 */
	private function getNextConnectorsUri($uri){

		$connectorsCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PREC_ACTIVITIES,$uri);

		$connectorsUri = array();
		foreach ($connectorsCollection->getIterator() as $statement){
			$this->logger->debug('get Next Connectors Uri : ' . $statement->uriResource,__FILE__,__LINE__);
			$this->logger->debug('get Next Connectors Name : ' . $statement->getLabel(),__FILE__,__LINE__);
			$connectorsUri[] = $statement->uriResource;
		}
		return $connectorsUri;
	}

	/**

	*
	* Short description of method getNextConnectors
	*
	* @access private
	* @author firstname and lastname of author, <author@example.org>
	* @return array
	*/
	private function getNextConnectors()
	{
		$returnValue = array();

		// section 10-13-1-85--3c82cee5:11bb0c5945c:-8000:00000000000009AB begin
		$nextConnectorsCollection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PREC_ACTIVITIES,$this->currentActivity[0]->uri);

		$connectors = array();

		foreach ($nextConnectorsCollection->getIterator() as $statement)
		{
			$newConn = new Connector($statement->uriResource);
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
	public function performBackwardTransition(Activity $from)
	{
		// section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A4D begin

		$activity = $this->path->getActivityBefore($from);

		// Only go backward if there is an activity before the "from Activity".
		// If you persist in doing so, your process current token will be set
		// in the digital nirvana...
		if (null != $activity)
		{
			$currentTokenProp = new core_kernel_classes_Property(CURRENT_TOKEN);

			$this->resource->editPropertyValues($currentTokenProp,$activity);

			$this->currentActivity = array();
			$beforeActivity = new Activity($activity);
			$this->currentActivity[] = $beforeActivity;

			if ($beforeActivity->isHidden && !$beforeActivity->isFirst())
			{
				$this->performBackwardTransition($beforeActivity);
			}
		}
	}

	/**
	 * Short description of method getPreviousConnectors
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return array
	 */
	public function getPreviousConnectors()
	{
		$returnValue = array();

		// section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A4F begin

		// section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A4F end

		return (array) $returnValue;
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
		$statusProp = new core_kernel_classes_Property(STATUS);
		$this->resource->editPropertyValues($statusProp,STATUS_FINISHED);

		$this->status = 'Finished';

		// -- Exit code handling.
		// I chain removeProp... and editProp... because of an editProp...
		// malfunction.
		$currentTokenProp =	new core_kernel_classes_Property(CURRENT_TOKEN);
		$this->resource->removePropertyValues($currentTokenProp);

//		$exitCodeProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCE_EXITCODE);
//		$this->resource->setPropertyValue($exitCodeProp, RESOURCE_EXITCODE_ALL_COVERED);
//
//		// -- Action code handling.
//		$actionCodeProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCE_ACTIONCODE);
//		$this->resource->removePropertyValues($actionCodeProp);
//		$this->resource->setPropertyValue($actionCodeProp,'');


		// section 10-13-1-85-19c5934a:11cae6d4e92:-8000:0000000000000A28 end
	}

	/**
	 * Short description of method remove
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function remove()
	{
		// section 10-13-1-85-19c5934a:11cae6d4e92:-8000:0000000000000A2A begin

		// -- Will flush the Path and its PathItems in the KM if needed.
		// After that we simply kill the current process :D !
		$this->path->remove();
		parent::remove();

		// We log the "CASE DESTROYED" event in the log file.
		if (defined('PIAAC_ENABLED'))
		{
			$event = new PiaacEvent('BQ_ENGINE', 'Removing process',
									'process_removed', getIntervieweeUriByProcessExecutionUri($this->uri));
			PiaacEventLogger::getInstance()->trigEvent();
		}
		// section 10-13-1-85-19c5934a:11cae6d4e92:-8000:0000000000000A2A end
	}




	/**
	 * @param $activity
	 * @param $testing
	 * @return unknown_type
	 */
	public function jumpBack(Activity $activity, $testing="")
	{
		$beforeActivityLabel = $this->currentActivity[0]->label;
		$beforeActivity = $this->currentActivity[0];
		// Current token is now the activity to jump back.
		$tokenProp = new core_kernel_classes_Property(CURRENT_TOKEN);
		$this->resource->editPropertyValues($tokenProp,$activity->uri);


		/*
		 //should be a real boolean, don't know how php framework handle that
		 //to do after release 5.1.7 change this
		 if ($testing=="true") {$this->path->insertActivity($activity);}

		 $this->currentActivity = array();
		 $this->currentActivity[] = new Activity($activity->uri);
		 */

		$this->path->invalidate($beforeActivity,
		($this->path->contains($activity) ? $activity : null));

		// We insert in the ontology the last activity in the path stack.
		$this->path->insertActivity($activity);
		$this->currentActivity[] = new Activity($activity->uri);



		// We log the "MOVE_JUMP" in the log file.
		if (defined('PIAAC_ENABLED'))
		{
			$event = new PiaacBusinessEvent('BQ_ENGINE', 'MOVE_JUMP',
											'The interviewer jumped to a previous question', 
			getIntervieweeUriByProcessExecutionUri($this->uri),
			$beforeActivityLabel,
			$this->currentActivity[0]->label);

			PiaacEventLogger::getInstance()->trigEvent($event);
		}
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

		$currentTokenProp = new core_kernel_classes_Property(CURRENT_TOKEN);
		$values = $this->resource->getPropertyValues($currentTokenProp);

		foreach ($values as  $b)
		{
			$activity				= new Activity($b);
			$activityExecution		= new ActivityExecution($this,$activity);
			$activityExecution->uri = $b;
			$activityExecution->label = $activity->label;

			$this->currentActivity[] = $activity;
		}

		$statusProp = new core_kernel_classes_Property(STATUS);
		$values = $this->resource->getPropertyValues($statusProp);

		//add status information
		if (sizeOf($values)>0)
		{
			switch ($values[0])
			{
				case RESOURCE_PROCESSSTATUS_RESUMED : 	{ $this->status = "Resumed"; break; }
				case RESOURCE_PROCESSSTATUS_STARTED : 	{ $this->status = "Started"; break; }
				case RESOURCE_PROCESSSTATUS_FINISHED : 	{ $this->status = "Finished"; break; }
				case RESOURCE_PROCESSSTATUS_PAUSED :	{ $this->status = "Paused" ;break; }
			}
		}

		// Build the path of the process execution.
		$this->path = new ProcessPath($this);

		// section 10-13-1--31--7b61b039:11cdba08b1e:-8000:0000000000000A30 end
	}


	public function performTransitionToLast() // throws ConsistencyException
	{
		$logger = Utils::getGenericLogger();

		// We get the part of the valid path between the current activity and
		// the last valid activity.
		$fromActivity = $this->currentActivity[0];
		$partialPath = $this->path->getPathFrom($fromActivity);


		for ($i = 0; $i < (count($partialPath) - 1); $i++)
		{
			$pathItemUri = $partialPath[$i];

			$currentActivity = $this->currentActivity[0];
			$currentActivityUri = $currentActivity->uri;


			if ($pathItemUri != $currentActivityUri)
			{
				$pathItemActivity = new Activity($pathItemUri);

				if (!$pathItemActivity->isHidden)
				{
					// We changed our route regarding the path before jumping.
					// or we are at the very last activity we may go to.
					break;
				}
				else
				{
					// Hidden activity problem... then I have to increase the lookup
					// of pathItems until we find a not hidden activity in the path stack.
					while ($i < count($partialPath) - 1)
					{
						$pathItemUri = $partialPath[$i];
						$pathItemActivity = new Activity($pathItemUri);

						if ($pathItemActivity->isHidden)
						$i++;
						else
						break;
					}
				}


			}
			// We try to go further !
			$this->performTransition();
		}
	}

	public function isBackable()
	{
		$backable = false;
		$previousActivity = $this->path->getActivityBefore($this->currentActivity[0]);
		if ($previousActivity)
		{
			$previousActivity = new Activity($previousActivity);
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
				$previousActivity = new Activity($previousActivity);
			}
		}
			
		return $backable;
	}

} /* end of class ProcessExecution */

?>