<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * This file is part of Generis Object Oriented API.
 *
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}


/**
 * The wfEngine_models_classes_ProcessTreeService class allows to create the array representation of a jsTree for a given process
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 * @subpackage models_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class wfEngine_models_classes_ProcessTreeService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The attribute 
     *
     * @access protected
     * @var Class
     */
	protected $currentProcess = null; 
	 
    protected $currentActivity = null;

	protected $currentConnector = null;
	
	protected $addedConnectors = array();
	
    // --- OPERATIONS ---

	/**
     * The method __construct intiates the DeliveryService class and loads the required ontologies from the other extensions 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return mixed
     */	
    public function __construct($currentProcess = null){
	
		$this->currentProcess = $currentProcess;
    
	}
	
	/**
     * The method creates the array representation of jstree, for a process definition 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param core_kernel_classes_Resource process
     * @return array
     */	
	public function activityTree(core_kernel_classes_Resource $process = null){
		
		$this->currentActivity = null;
		// $this->addedConnectors = array();//reinitialized for each activity loop
		$data = array();
		
		if(empty($process) && !empty($this->currentProcess)){
			$process = $this->currentProcess;
		}
		if(empty($process)){
			throw new Exception("no process instance to populate the activity tree");
			return $data;
		}
		
		//initiate the return data value:
		$data = array(
			'data' => __("Process Tree:").' '.$process->getLabel(),
			'attributes' => array(
				'id' => 'node-process-root',
				'class' => 'node-process-root',
				'rel' => tao_helpers_Uri::encode($process->uriResource)
			),
			'children' => array()
		);
		
		//instanciate the processAuthoring service
		$processAuthoringService = new wfEngine_models_classes_ProcessAuthoringService();
	
		$activities = array();
		$activities = $processAuthoringService->getActivitiesByProcess($process);
		// throw new Exception(var_dump($activities));
		foreach($activities as $activity){
			
			$this->currentActivity = $activity;
			$this->addedConnectors = array();//required to prevent cyclic connexion between connectors of a given activity
			$initial = false;
			$last = false;
			
			$activityData = array();
			$activityData = $this->activityNode(
				$activity,
				'next',
				false
			);//default value will do
						
			//get connectors
			$connectors = $processAuthoringService->getConnectorsByActivity($activity);
			// throw new Exception("data=".var_dump($connectors));	
			/*
			if(!empty($connectors['prev'])){
			
				$this->currentConnector = null;
				
				//activity connected to a previous one:
				foreach($connectors['prev'] as $connector){
				
					$this->currentConnector = $connector;
					
					$connectorData = array();
						
					//type of connector:
					$connectorType = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
					
					
					//if it is a split type
					if( strtolower($connectorType->getLabel()) == "split"){
						//get the rule
						$connectorRule = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
						$connectorData[] = $this->conditionNode($connectorRule);
												
						//get the "PREC"
						$prev = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES));
						$connectorData[] = $this->activityNode($prev, 'prec', true);
												
					}elseif(strtolower($connectorType->getLabel()) == "sequence"){
						$prev = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PREVIOUSACTIVITIES));
						if(!wfEngine_helpers_ProcessUtil::isActivity($prev)){
							throw new Exception("the previous activity of a sequence connector {$connector->uriResource} must be an activity {$prev->uriResource}");
						}
						$connectorData[] = $this->activityNode($prev, 'next', true);
					}
					
					//add to activity data
					$activityData['children'][] = array(
						'data' => $connectorType->getLabel().":".$connector->getLabel(),
						'attributes' => array(
							'rel' => tao_helpers_Uri::encode($connector->uriResource),
							'class' => 'node-connector-prev'
						),
						'children' => $connectorData
					);
					
					// foreach($connectorData as $data){
						// $activityData["children"][] = $data;
					// }
					
				}
			}else{
				//check if it is the initial activity, otherwise, return an undefined type;
			
				//add the default "empty" connector node: SHOULD be displayed: initial service here!!! and there should be only ONE!
				$activityData['children'][] = array(
					'data' => __('undefined'),
					'attributes' => array(
						'rel' => 'undefined',
						'class' => 'node-connector-prev'
					)
				);
			}*/
			
			//following nodes:
			if(!empty($connectors['next'])){
				//connector following the current activity: there should be only one
				foreach($connectors['next'] as $connector){
					$this->currentConnector = $connector;
					$activityData['children'][] = $this->connectorNode($connector, '', true);
				}
			}else{
				// throw new Exception("no connector associated to the activity: {$activity->uriResource}");
				//Simply not add a connector here: this should be considered as the last activity:
				$last = true;				
			}
			
			//check if it is the first activity node:
			
			$isIntial = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL));//http://www.tao.lu/middleware/taoqual.rdf#i119018447833116
			if(!is_null($isIntial) && $isIntial instanceof core_kernel_classes_Resource){
				if($isIntial->uriResource == GENERIS_TRUE){
					$initial =true;
				}
			}
			
			if($initial){
				$activityData = $this->addNodeClass($activityData, "node-activity-initial");
				if($last){
					$activityData = $this->addNodeClass($activityData, 'node-activity-last');	
					$activityData = $this->addNodeClass($activityData, "node-activity-unique");
				}
			}elseif($last){
				$activityData = $this->addNodeClass($activityData, 'node-activity-last');	
			}
			
			
			//get interactive services
			$services = null;
			$services = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
			foreach($services->getIterator() as $service){
				if($service instanceof core_kernel_classes_Resource){
					$activityData['children'][] = array(
						'data' => $service->getLabel(),
						'attributes' => array(
							'id' => tao_helpers_Uri::encode($service->uriResource),
							'class' => 'node-interactive-service'
						)
					);
				}
			}
			
			//add children here
			if($initial){
				array_unshift($data["children"],$activityData);
			}else{
				$data["children"][] = $activityData;
			}
			
		}
		
		return $data;
	}
		
	/**
     * The method creates the array representation a connector to fill the jsTree 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param core_kernel_classes_Resource connector
	 * @param string nodeClass
	 * @param boolean recursive
     * @return array
     */
	public function connectorNode(core_kernel_classes_Resource $connector, $nodeClass='', $recursive=false, $portInfo=array()){//put the current activity as a protected property of the class Process aythoring Tree
		
		$returnValue = array();
		$connectorData = array();
					
		//type of connector:
		//if not null, get the information on the next activities. Otherwise, return an "empty" connector node, indicating that the node has just been created, i.e. at the same time as an activity
		$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE), false);
		if(is_null($connectorType)){
			//create default connector node:
			$returnValue = self::addNodePrefix($this->defaultConnectorNode($connector),$nodeClass);
			return $returnValue;
		}
		
		//if it is a split type
		if( $connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_CONDITIONAL){
			//get the rule
			$connectorRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE), false);
			if(!is_null($connectorRule)){
				//continue getting connector data: 
				$connectorData[] = $this->conditionNode($connectorRule);
				
				//get the "THEN"
				$then = $connectorRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN), false);
				if(!is_null($then)){
					$portData = array(
						'id' => 0,
						'label' => 'then',
						'multiplicity' => 1
					);
					if(wfEngine_helpers_ProcessUtil::isConnector($then)){
						$connectorActivityReference = $then->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->uriResource;
						if( ($connectorActivityReference == $this->currentActivity->uriResource) && !in_array($then->uriResource, $this->addedConnectors) ){
							if($recursive){
								$connectorData[] = $this->connectorNode($then, 'then', true, $portData);
							}else{
								$connectorData[] = $this->activityNode($then, 'then', false, $portData);
							}
						}else{
							$connectorData[] = $this->activityNode($then, 'then', true, $portData);
						}
					}else{
						$connectorData[] = $this->activityNode($then, 'then', true, $portData);
					}
				}
				
				//same for the "ELSE"
				$else = $connectorRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE), false);
				if(!is_null($else)){
					$portData = array(
						'id' => 1,
						'label' => 'else',
						'multiplicity' => 1
					);
					if(wfEngine_helpers_ProcessUtil::isConnector($else)){
						$connectorActivityReference = $else->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->uriResource;
						if( ($connectorActivityReference == $this->currentActivity->uriResource) && !in_array($else->uriResource, $this->addedConnectors) ){
							if($recursive){
								$connectorData[] = $this->connectorNode($else, 'else', true, $portData);
							}else{
								$connectorData[] = $this->activityNode($else, 'else', false, $portData);
							}
						}else{
							$connectorData[] = $this->activityNode($else, 'else', true, $portData);
						}
					}else{
						$connectorData[] = $this->activityNode($else, 'else', true, $portData);
					}
				}
			}
		}elseif($connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_SEQUENCE){
			$next = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), false);
			
			if(!is_null($next)){
				$connectorData[] = $this->activityNode($next, 'next', true);//the default portData array will do
			}
		}elseif($connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_PARALLEL){
			$nextActivitiesCollection = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES));
			//count the number of parallel activity:
			$activityCount = array();
			foreach($nextActivitiesCollection->getIterator() as $nextActivity){
				if($nextActivity instanceof core_kernel_classes_Resource){
					if(isset($activityCount[$nextActivity->uriResource])){
						//increment:
						$activityCount[$nextActivity->uriResource] += 1;
					}else{
						$activityCount[$nextActivity->uriResource] = 1;
					}
				}
			}
			
			$portId = 0;
			foreach($activityCount as $activityUri=>$number){
				
				$activity = new core_kernel_classes_Resource($activityUri);
				$connectorData[] = $this->activityNode(
					$activity,
					'next',
					true,
					array(
						'id' => $portId,
						'multiplicity' => $number,
						'label' => $activity->getLabel()
					),
					"(count: $number)"
				);
				$portId ++;
			}
			
			//add one extra one, which is empty:
			
			
		}elseif($connectorType->uriResource == INSTANCE_TYPEOFCONNECTORS_JOIN){
			$next = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES), false);
			
			if(!is_null($next)){
				$connectorData[] = $this->activityNode($next, 'next', true);//the default portData array will do
			}
		}else{
			throw new Exception("unknown connector type: {$connectorType->getLabel()} for connector {$connector->uriResource}");
		}
		
		if(empty($portInfo)){
			$portInfo = array(
				'id' => 0,
				'label' => 'next',
				'multiplicity' => 1,
			);
		}else{
			if(!isset($portInfo['id'])){
				$portInfo['id'] = 0;
			}
			if(!isset($portInfo['id'])){
				$portInfo['label'] = 'next';
			}
			if(!isset($portInfo['id'])){
				$portInfo['multiplicity'] = 1;
			}
		}
		
		//add to data
		$returnValue = array(
			'data' => $connectorType->getLabel().":".$connector->getLabel(),
			'attributes' => array(
				'id' => tao_helpers_Uri::encode($connector->uriResource),
				'class' => 'node-connector'
			),
			'type' => trim(strtolower($connectorType->getLabel())),
			'port' => $nodeClass,
			'portData' => $portInfo
		);
		$returnValue = self::addNodePrefix($returnValue, $nodeClass);
		
		if(!empty($connectorData)){
			$returnValue['children'] = $connectorData;
		}
		
		$this->addedConnectors[] = $connector->uriResource;
		
		return $returnValue;
	}
	
	/**
     * Add a prefix to the node data value
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param array node
	 * @param string prefix
     * @return array
     */
	public function addNodePrefix($node, $prefix=''){
		$newNode = $node;
		$labelPrefix = '';
		switch(strtolower($prefix)){
			case 'prev':
				break;
			case 'next':
				break;
			case 'if':
				$labelPrefix = __('If').' ';
				break;	
			case 'then':
				$labelPrefix = __('Then').' ';
				break;
			case 'else':
				$labelPrefix = __('Else').' ';
				break;
		}
		if(!empty($labelPrefix)){
			$newNode['data'] = $labelPrefix.$node['data'];
		}
		return $newNode;
	}
	
	/**
     * The method creates the array representation the default connector node to fill the jsTree 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param core_kernel_classes_Resource connector
	 * @param boolean prev
     * @return array
     */
	public function defaultConnectorNode(core_kernel_classes_Resource $connector, $prev = false){
		
		$returnValue = array(
			'data' => __("type??").":".$connector->getLabel()
		);
		
		if(!$prev){
			$returnValue['attributes'] = array(
				'id' => tao_helpers_Uri::encode($connector->uriResource),
				'class' => 'node-connector'
			);
		}else{
			$returnValue['attributes'] = array(
				'class' => 'node-connector-prev'
			);
		}
		
		return $returnValue;
	}
	
	/**
     * The method creates the array representation of the condition of a rule node to fill the jsTree 
     * (could be inferenceRule or transitionRule)
	 *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param core_kernel_classes_Resource rule
     * @return array
     */	
	public function conditionNode(core_kernel_classes_Resource $rule){
		
		$nodeData = array();
		
		if(!is_null($rule)){
			$data='';
			$if = $rule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
			if(!is_null($if)){
				$data = $if->getLabel();
			}else{
				$data = __("(still undefined)");
			}
			$nodeData = array(
				'data' => $data,
				'attributes' => array(
					'id' => tao_helpers_Uri::encode($rule->uriResource),
					'class' => 'node-rule'
				)
			);
			
			$nodeData = self::addNodePrefix($nodeData, 'if');
		}
		
		return $nodeData;
	}

	/**
     * The method creates the array representation the default connector node to fill the jsTree 
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param core_kernel_classes_Resource activity
	 * @param string nodeClass
	 * @param boolean goto
     * @return array
     */
	public function activityNode(core_kernel_classes_Resource $activity, $nodeClass='', $goto=false, $portInfo=array(), $labelSuffix=''){
		$nodeData = array();
		$class = '';
		$linkAttribute = 'id';
		          
		$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$connectorService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ConnectorService');
		
		if($activityService->isActivity($activity)){
			$class = 'node-activity';
		}elseif($connectorService->isConnector($activity)){
			$class = 'node-connector';
		}else{
			return $nodeData;//unknown type
		}
		
		if($goto){
			$class .= "-goto";
			$linkAttribute = "rel";
		}
		
		if(empty($portInfo)){
			$portInfo = array(
				'id' => 0,
				'label' => 'next',
				'multiplicity' => 1,
			);
		}else{
			if(!isset($portInfo['id'])){
				$portInfo['id'] = 0;
			}
			if(!isset($portInfo['id'])){
				$portInfo['label'] = 'next';
			}
			if(!isset($portInfo['id'])){
				$portInfo['multiplicity'] = 1;
			}
		}
		
		$nodeData = array(
			'data' => $activity->getLabel().' '.$labelSuffix,
			'attributes' => array(
				$linkAttribute => tao_helpers_Uri::encode($activity->uriResource),
				'class' => $class
			),
			'port' => $nodeClass,
			'portData' => $portInfo
		);
		
		$nodeData = self::addNodePrefix($nodeData, $nodeClass);
		
		return $nodeData;
	}
	
	/**
     * The method adds a node class to a nodeData array
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param array nodeData
	 * @param string newClass
     * @return array
     */
	public function addNodeClass( $nodeData=array(), $newClass='' ){
		
		if(isset($nodeData['attributes']['class']) && !empty($newClass)){
			$nodeData['attributes']['class'] .= " ".$newClass;
			
			//set specific option
			if($newClass == 'node-activity-initial'){
				$nodeData['isInitial'] = true; 
			}
			if($newClass == 'node-activity-last'){
				$nodeData['isLast'] = true; 
			}
		}
		return $nodeData;
	}
} /* end of class  */

?>