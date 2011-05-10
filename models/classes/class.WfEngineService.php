<?php

class wfEngine_models_classes_WfEngineService
	extends tao_models_classes_Service {

    /**
     * Short description of method getProcessDefinitions
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getProcessDefinitions()
    {
        $returnValue = array();

        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000819 begin
		
		$class = new core_kernel_classes_Class(CLASS_PROCESS);
		$processes = $class->getInstances();
		foreach ($processes as $key=>$val){
			$process = new wfEngine_models_classes_Process($key);
			$returnValue[] = $process;

		}


        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000819 end

        return (array) $returnValue;
    }



    /**
     * Short description of method getProcessExecutions
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getProcessExecutions()
    {

        $returnValue = array();

        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008E7 begin
        
    	$processDefClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processExecClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		       
		$processes = $processDefClass->getInstances();
		foreach ($processes as $uri => $process){
			$processExecutions = $processExecClass->searchInstances(array(PROPERTY_PROCESSINSTANCES_EXECUTIONOF => $uri), array('like' => false));
        	foreach($processExecutions as $execution){
        		$processInstance = new wfEngine_models_classes_ProcessExecution($execution->uriResource);
        		$returnValue[] = $processInstance;
        	}
		}

        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008E7 end

        return (array) $returnValue;
    }


} /* end of class WfEngine */

?>