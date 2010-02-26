<?php

class TransitionRule extends core_kernel_classes_Rule
{
	public $thenActivity = null;
	public $elseActivity = null;
	
	//todo memory inspector for then and else activities
	function __construct($ressource)
	{
		parent::__construct($ressource);
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
			$thenType = $thenPropertyValue->getUniquePropertyValue(new core_kernel_classes_Property(RDF_TYPE));
			
			if ($hasElse)
				$elseType = $elsePropertyValue->getUniquePropertyValue(new core_kernel_classes_Property(RDF_TYPE));
			
			if ($thenType->uriResource == CLASS_CONNECTORS)
			{
					$this->thenActivity = new Connector($thenPropertyValue->uriResource);
					//$this->thenActivity->feedFlow(1);
			}
			else
				$this->thenActivity = new Activity($thenPropertyValue->uriResource);
			
			if ($hasElse)
			{
				if ($elseType->uriResource == CLASS_CONNECTORS)
				{
		
						$this->elseActivity = new Connector($elsePropertyValue->uriResource);
						//$this->elseActivity->feedFlow(1);
				}
				else
				{
					$this->elseActivity = new Activity($elsePropertyValue->uriResource);
				}
			}
		}
		catch (common_Exception $e)
		{
			echo $e;
			var_dump($this);
			die("\nI died in Transition Rule");
		}
	}
}
?>