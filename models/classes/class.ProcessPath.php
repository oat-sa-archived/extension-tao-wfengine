<?php

error_reporting(E_ALL);

/**
 * WorkFlowEngine - class.ProcessPath.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatic generated with ArgoUML 0.24 on 13.10.2008, 09:06:31
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
	die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-85--29df164:11c79d4c931:-8000:0000000000000A1F-includes begin
// section 10-13-1-85--29df164:11c79d4c931:-8000:0000000000000A1F-includes end

/* user defined constants */
// section 10-13-1-85--29df164:11c79d4c931:-8000:0000000000000A1F-constants begin
// section 10-13-1-85--29df164:11c79d4c931:-8000:0000000000000A1F-constants end

/**
 * Short description of class wfEngine_models_classes_ProcessPath
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class wfEngine_models_classes_ProcessPath
{
	// --- ATTRIBUTES ---

	/**
	 * Short description of attribute activityStack
	 *
	 * @access public
	 * @var array
	 */
	public $activityStack = array();

	/**
	 * Short description of attribute processExecution
	 *
	 * @access public
	 * @var wfEngine_models_classes_ProcessExecution
	 */
	public $processExecution = null;

	public $fullActivityStack = array();

	// --- OPERATIONS ---

	/**
	 * Short description of method insertActivity
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @param Activity
	 * @return void
	 */
	public function insertActivity( wfEngine_models_classes_Activity $activity)
	{
		// section 10-13-1-85--29df164:11c79d4c931:-8000:0000000000000A28 begin

		// Full path handling.
		$this->fullActivityStack[] = $activity->uri;
		$processExecutionResource = new core_kernel_classes_Resource($this->processExecution->uri);
		$processFullPathProperty = new core_kernel_classes_Property(PROPERTY_PINSTANCES_FULLPROCESSPATH);
		 
		$processExecutionResource->removePropertyValues($processFullPathProperty);
		$fullPathString = '';
		 
		foreach ($this->fullActivityStack as $pI)
		{
			if (strlen($fullPathString))
			$fullPathString .= '|';
			 
			$fullPathString .= $pI;
		}
		 
		$processExecutionResource->setPropertyValue($processFullPathProperty, $fullPathString);
		 
		// Valid path handling.
		if (!array_search($activity->uri, $this->activityStack))
		{
			array_push($this->activityStack, $activity->uri);

			 
			// we rebuild the path string.
			$pathString = '';
			 
			foreach ($this->activityStack as $activity)
			{
				if (strlen($pathString))
				$pathString .= '|';
				 
				$pathString .= $activity;
			}

			// We simply save the path string for the current process execution.
			$pinstanceProp = new core_kernel_classes_Property(PROPERTY_PINSTANCES_PROCESSPATH);
			$this->processExecution->resource->editPropertyValues($pinstanceProp,$pathString);

		}
		// section 10-13-1-85--29df164:11c79d4c931:-8000:0000000000000A28 end
	}

	/**
	 * Short description of method getLastActivity
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return Activity
	 */
	public function getLastActivity()
	{
		$returnValue = null;

		// section 10-13-1-85--29df164:11c79d4c931:-8000:0000000000000A2D begin
		$stackSize = $this->getPathSize();

		if ($stackSize)
		$returnValue = $this->activityStack[$stackSize - 1];
		else
		$returnValue = null;
		// section 10-13-1-85--29df164:11c79d4c931:-8000:0000000000000A2D end

		return $returnValue;
	}

	/**
	 * Short description of method removeLastActivity
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function removeLastActivity()
	{
		// section 10-13-1-85--29df164:11c79d4c931:-8000:0000000000000A2F begin

		$activityToRemove = array_pop($this->activityStack);

		// We rebuild the path string, without the last entry.
		$pathString = '';

		for ($i = 0; $i < count($this->activityStack); $i++)
		{
			if (strlen($pathString))
			$pathString .= '|';

			$pathString .= $this->activityStack[$i];
		}
		$pinstanceProp = new core_kernel_classes_Property(PROPERTY_PINSTANCES_PROCESSPATH);
		$this->processExecution->resource->editPropertyValues($pinstanceProp,$pathString);

		// section 10-13-1-85--29df164:11c79d4c931:-8000:0000000000000A2F end
	}

	/**
	 * Short description of method getPathSize
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return int
	 */
	public function getPathSize()
	{
		$returnValue = (int) 0;

		// section 10-13-1-85--29df164:11c79d4c931:-8000:0000000000000A38 begin
		$returnValue = count($this->activityStack);
		// section 10-13-1-85--29df164:11c79d4c931:-8000:0000000000000A38 end

		return (int) $returnValue;
	}

	/**
	 * Short description of method __construct
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @param wfEngine_models_classes_ProcessExecution
	 * @return void
	 */
	public function __construct( wfEngine_models_classes_ProcessExecution $processExecution)
	{
		// section 10-13-1-85--29df164:11c79d4c931:-8000:0000000000000A47 begin

		$this->processExecution = $processExecution;
		 
		$processPathProp = new core_kernel_classes_Property(PROPERTY_PINSTANCES_PROCESSPATH);
		$path = $processExecution->resource->getPropertyValues($processPathProp);
		//replace processExecution by token:
			
		$explodeToken = '|';

		if (count($path) && strlen($path[0]))
		{
				
			$pathItems = explode($explodeToken, $path[0]);

			foreach ($pathItems as $pI)
			array_push($this->activityStack, $pI);
		}
		$pinstanceFullProcessPath = new core_kernel_classes_Property(PROPERTY_PINSTANCES_FULLPROCESSPATH);
		$path = $processExecution->resource->getPropertyValues($pinstanceFullProcessPath);
		 
			
			
		$explodeToken = '|';

		if (count($path) && strlen($path[0]))
		{
				
			$pathItems = explode($explodeToken, $path[0]);

			foreach ($pathItems as $pI)
			array_push($this->fullActivityStack, $pI);
		}
		 
		// section 10-13-1-85--29df164:11c79d4c931:-8000:0000000000000A47 end
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
		// section 10-13-1-85-19c5934a:11cae6d4e92:-8000:0000000000000A26 begin
		// FIXME Optimize the remove method for the Path class to get imbrezzive bervormanse.
		while ($this->getPathSize() > 0)
		$this->removeLastActivity();
		// section 10-13-1-85-19c5934a:11cae6d4e92:-8000:0000000000000A26 end
	}

	/**
	 * Short description of method contains
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @param Activity
	 * @return boolean
	 */
	public function contains(wfEngine_models_classes_Activity $activity)
	{
		$returnValue = (bool) false;

		// section 10-13-1-85-7282d1cf:11cf4fd21dd:-8000:0000000000000A3B begin
		foreach ($this->activityStack as $pathItem)
		{
			if ($activity->uri == $pathItem)
			{
				$returnValue = true;
				break;
			}
		}
		 
		// section 10-13-1-85-7282d1cf:11cf4fd21dd:-8000:0000000000000A3B end

		return (bool) $returnValue;
	}

	/**
	 * Short description of method invalidate
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @param Activity
	 * @return void
	 */
	public function invalidate(wfEngine_models_classes_Activity $to, wfEngine_models_classes_Activity $currentActivity = null)
	{
		// section 10-13-1-85-7282d1cf:11cf4fd21dd:-8000:0000000000000A40 begin
		// $to is the activity before the current one.

		$i 					= ($this->getPathSize() -1);
		$indexesToRemove 	= array();


		if ($currentActivity &&
		count($this->activityStack) > 2 &&
		($maxBound = array_search($currentActivity->uri, $this->activityStack)) &&
		($minBound = array_search($to->uri, $this->activityStack)) &&
		isset($this->activityStack[$maxBound - 1]) &&
		$this->activityStack[$maxBound - 1] != $to->uri)
		{
			$i = $minBound + 1;
			while ($i < $maxBound)
			{
				$indexesToRemove[] = $i;

				$i++;
			}
		}
		else
		{
			while ($i >= 0)
			{
				if (!($this->activityStack[$i] != $to->uri))
				break;
				 
				$indexesToRemove[] = $i;
				 
				$i--;
			}
		}

		// We delete the found elements and synchronize it with the
		// ontology.
		foreach ($indexesToRemove as $index)
		{
			 
			unset($this->activityStack[$index]);
		}

		$this->activityStack = array_values($this->activityStack);


		$serializedPath = '';

		foreach ($this->activityStack  as $pathItem){
			$serializedPath .= $pathItem . '|';
		}
		if (strlen($serializedPath)){
			$serializedPath = substr($serializedPath, 0, strlen($serializedPath) - 1);
		}

		$pInstanceProp = new core_kernel_classes_Property(PROPERTY_PINSTANCES_PROCESSPATH);
		$this->processExecution->resource->editPropertyValues($pInstanceProp,$serializedPath);

		// section 10-13-1-85-7282d1cf:11cf4fd21dd:-8000:0000000000000A40 end
	}

	public function getActivityBefore(wfEngine_models_classes_Activity $activity)
	{
		for ($i = 0; $i < count($this->activityStack); $i++)
		{
			if ($activity->uri == $this->activityStack[$i])
			{
				// Activity before exists ?
				return ((isset($this->activityStack[$i - 1])) ? $this->activityStack[$i - 1] : null);
			}
		}
		 
		return null;
	}

	public function getPathFrom(wfEngine_models_classes_Activity $activity)
	{
		$pathPortion = array();
		$store = false;
		 
		foreach ($this->activityStack as $pathItem)
		{
			if ($pathItem == $activity->uri)
			$store = true;
			 
			if ($store)
			$pathPortion[] = $pathItem;
		}
		 
		return $pathPortion;
	}

	public function __toString()
	{
		$toString = '';
		 
		foreach ($this->activityStack as $pathItem)
		{
			$activityResource = new core_kernel_classes_Resource($pathItem);
			$toString .= ' - ' . $activityResource->getLabel();
		}
		 
		return $toString;
	}

} /* end of class wfEngine_models_classes_ProcessPath */

?>