<?php
error_reporting(E_ALL);



class TransitionRule extends core_kernel_rules_Rule
{
	public $thenActivity = null;
	public $elseActivity = null;
	public $logger;
	
	//todo memory inspector for then and else activities
	function __construct($ressource)
	{
		parent::__construct($ressource);
		$this->logger = new common_Logger('TransitionRules', Logger::debug_level);
		$this->logger->debug('Next TransitionRules  Name: ' . $this->getLabel(),__FILE__,__LINE__);
		$this->logger->debug('Next TransitionRules  Uri: ' . $this->uriResource,__FILE__,__LINE__);
		$hasElse = false;

		try
		{
			$thenProperty = new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN);
			$elseProperty = new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE);

			$thenPropertyValue = $this->getUniquePropertyValue($thenProperty);
			$elsePropertyValue = $this->getPropertyValues($elseProperty);

			if (count($elsePropertyValue) && $elsePropertyValue[0] != '')
			{
				$elsePropertyValue = new core_kernel_classes_Resource($elsePropertyValue[0]);
				$hasElse = true;
			}
			else
			{
				$hasElse = false;
			}

			// Is that an activity or a transition rule ?
			$thenTypes = $thenPropertyValue->getType();
			$isConnector = false;
			foreach($thenTypes as $thenType){
				if($thenType->uriResource == CLASS_CONNECTORS){
					$this->thenActivity = new wfEngine_models_classes_Connector($thenPropertyValue->uriResource);
					$isConnector = true;
					break;
				}
			}
			if(!$isConnector){
				$this->thenActivity = new wfEngine_models_classes_Activity($thenPropertyValue->uriResource);
			}
			
			if ($hasElse)
			{
				$elseTypes = $elsePropertyValue->getType();
				$isConnector = false;
				foreach($elseTypes as $elseType){
					if ($elseType->uriResource == CLASS_CONNECTORS){
						$this->elseActivity = new wfEngine_models_classes_Connector($elsePropertyValue->uriResource);
						$isConnector = true;
						break;
					}
				}
				if(!$isConnector){
					$this->elseActivity = new wfEngine_models_classes_Activity($elsePropertyValue->uriResource);
				}
			}
		}
		catch (common_Exception $e)
		{
			var_dump($this);
			echo $e;
			die("\nI died in Transition Rule");
		}
	}
}
?>