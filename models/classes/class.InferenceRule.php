<?php
class InferenceRule extends PiaacRule
{
	public $then = null;
	public $else = null;
	public $hasElse = false;
	
	public function __construct($resource)
	{
		error_reporting(E_ALL);
		parent::__construct($resource);
		
		// $this->then is an Assignment that is a subclass of Term.
		// $this->else is an InferenceRule or nothing.
		
		$thenProperty = new core_kernel_classes_Property(PROPERTY_INFERENCERULES_THEN);
		$elseProperty = new core_kernel_classes_Property(PROPERTY_INFERENCERULES_ELSE);
		
		// Assignment to perform if the expression is evaluated to true.
		$thenPropertyValue = $this->getPropertyValues($thenProperty);
		$this->then = new PiaacAssignment($thenPropertyValue[0]);
		
		$elsePropertyValue = $this->getPropertyValues($elseProperty);	

		// Here we tag that the InferenceRule has an else statement.
		// We do not load it now but at execution time to avoid
		// loading useless else if statements that will be never
		// executed.
		if (count($elsePropertyValue))
			$this->hasElse = true;
		
		// else, there is no else and so ... it means that the inferencerule returns false,
		// nothing has to happen.
	}
	
	public function execute(array $symbols)
	{
		if ($this->getExpression()->evaluate($symbols))
		{
			// Great, we evaluate the assignment 	
			$this->then->evaluate($symbols);
		}
		else if ($this->hasElse)
		{
			// Just in time else Inference Rule loading.

			// Retrieve the type of elsePropertyValue.
			$elseProperty = new core_kernel_classes_Property(PROPERTY_INFERENCERULES_ELSE);
			$elsePropertyValue = $this->getPropertyValues($elseProperty);

			$elseInstance = new core_kernel_classes_Resource($elsePropertyValue[0]);
			$elseInstanceTypeProperty = new core_kernel_classes_Property(RDF_TYPE);
			$elseInstanceTypeValue = $elseInstance->getUniquePropertyValue($elseInstanceTypeProperty);
			
			if ($elseInstanceTypeValue->uriResource == CLASS_INFERENCERULES)
			{
				// Cool that's a inference rule.
				$this->else = new InferenceRule($elsePropertyValue[0]);
			}
			else if ($elseInstanceTypeValue->uriResource == CLASS_ASSIGNMENT)
			{
				// Chill out (fr-qc) ! It's an assigment.
				$this->else = new PiaacAssignment($elsePropertyValue[0]);
			}
			else
			{
				throw new WfInferenceException('Unknow datatype for InferenceRules->else. Must be InferenceRules or Assignment');
			}
			
			// Else statement loaded...
			if (get_class($this->else) == 'PiaacAssignment')
			{
				// Final... Evaluate the assignment.
				$this->else->evaluate($symbols);
			}
			else if (get_class($this->else) == 'InferenceRule')
			{
				// recursive execution of else components.
				$this->else->execute($symbols);
			}
			else
				throw new WfInferenceException('Unknow datatype for InferenceRules->else object.The type of the object must be InferenceRule or core_kernel_classes_Assignment');
		}
	}
}
?>