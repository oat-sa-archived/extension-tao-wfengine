<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/models/classes/class.ProcessDiagram.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.12.2011, 17:56:11 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003448-includes begin
// section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003448-includes end

/* user defined constants */
// section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003448-constants begin
// section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003448-constants end

/**
 * Short description of class wfEngine_models_classes_ProcessDiagram
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ProcessDiagram
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute arrow
     *
     * @access private
     * @var array
     */
    private $arrow = array();

    /**
     * Short description of attribute activity
     *
     * @access private
     * @var array
     */
    private $activity = array();

    /**
     * Short description of attribute connector
     *
     * @access private
     * @var array
     */
    private $connector = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003449 begin
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003449 end
    }

    /**
     * Short description of method addArrow
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource from
     * @param  Resource to
     * @param  string fromPort
     * @return mixed
     */
    public function addArrow( core_kernel_classes_Resource $from,  core_kernel_classes_Resource $to, $fromPort = "bottom")
    {
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003454 begin
        $fromid = substr($from->uriResource, strpos($from->uriResource, "#")+1);
        $toid = substr($to->uriResource, strpos($to->uriResource, "#")+1);
        $this->arrow[] = array(
        		'from'	=> $fromid,
        		'to'	=> $toid,
        		'port'	=> $fromPort
        );
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003454 end
    }

    /**
     * Short description of method addActivity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @param  int xOffset
     * @param  int yOffset
     * @return mixed
     */
    public function addActivity( core_kernel_classes_Resource $activity, $xOffset, $yOffset)
    {
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003456 begin
        $id = substr($activity->uriResource, strpos($activity->uriResource, "#")+1);
        $this->activity[$id] = array(
        		'x' => $xOffset,
        		'y' => $yOffset
        		);
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003456 end
    }

    /**
     * Short description of method addConnector
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource connector
     * @param  int xOffset
     * @param  int yOffset
     * @return mixed
     */
    public function addConnector( core_kernel_classes_Resource $connector, $xOffset, $yOffset)
    {
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003458 begin
    	$id = substr($connector->uriResource, strpos($connector->uriResource, "#")+1);
    	$this->connector[$id] = array(
    			'x' => $xOffset,
    			'y' => $yOffset
    	);
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003458 end
    }

    /**
     * Short description of method toJSON
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function toJSON()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003464 begin
        $arrowData = array();
        $positionData = array();
        
        foreach ($this->arrow as $arrow) {
        	$type = null;
        	if (isset($this->activity[$arrow['from']])) {
        		$type = 'activity';
        	} elseif (isset($this->connector[$arrow['from']])) {
        		$type = 'connector';
        	} else {
        		throw new common_Exception('arrow starting from an inexistent activity or connector '.$arrow['from']);
        	}
        	$arrowData[] = array(
        			"id" => "connector_".$arrow['from']."_pos_".$arrow['port'],
        			"targetObject"=> $arrow['to'],
        			"type" => "top"
        	);
        }
        
        foreach ($this->activity as $id => $pos) {
        	$positionData[] = array(
        			'id' => $id,
        			'left' => $pos['x'],
        			'top' => $pos['y']
        	);
        }
        foreach ($this->connector as $id => $pos) {
        	$positionData[] = array(
        			'id' => $id,
        			'left' => $pos['x'],
        			'top' => $pos['y']
        	);
        }

        $returnValue = json_encode(
        		array(
        				"arrowData" => $arrowData,
        				"positionData" => $positionData
        		)
        );
        // section 127-0-1-1-23e337d4:1340f06a5e1:-8000:0000000000003464 end

        return (string) $returnValue;
    }

} /* end of class wfEngine_models_classes_ProcessDiagram */

?>