<?php

error_reporting(E_ALL);

/**
 * Factory for creating Process Disagrams
 *
 * @author Joel Bout
 * @package wfEngine
 * @subpackage helpers
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-5c9f7130:133f3eb6549:-8000:0000000000006000-includes begin
// section 127-0-1-1-5c9f7130:133f3eb6549:-8000:0000000000006000-includes end

/* user defined constants */
// section 127-0-1-1-5c9f7130:133f3eb6549:-8000:0000000000006000-constants begin
// section 127-0-1-1-5c9f7130:133f3eb6549:-8000:0000000000006000-constants end

/**
 * Factory for creating Process Disagrams
 *
 * @access public
 * @author Joel Bout
 * @package wfEngine
 * @subpackage helpers
 */
class wfEngine_helpers_ProcessDiagramFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Builds a simple Diagram of the Process
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource process
     * @return string
     */
    public function buildDiagramData( core_kernel_classes_Resource $process)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5c9f7130:133f3eb6549:-8000:0000000000006001 begin
        $diagramData = json_encode(array(
        		"arrowData" => array(),
        		"positionData" => array()
        ));
        $returnValue = $diagramData;
        // section 127-0-1-1-5c9f7130:133f3eb6549:-8000:0000000000006001 end

        return (string) $returnValue;
    }

} /* end of class wfEngine_helpers_ProcessDiagramFactory */

?>