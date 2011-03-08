<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine\models\classes\class.ProcessService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 08.03.2011, 17:57:10 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <s.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Somsack Sipasseuth, <s.sipasseuth@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 10-13-1-39-1f91722d:12e9641f6ad:-8000:0000000000002C56-includes begin
// section 10-13-1-39-1f91722d:12e9641f6ad:-8000:0000000000002C56-includes end

/* user defined constants */
// section 10-13-1-39-1f91722d:12e9641f6ad:-8000:0000000000002C56-constants begin
// section 10-13-1-39-1f91722d:12e9641f6ad:-8000:0000000000002C56-constants end

/**
 * Short description of class wfEngine_models_classes_ProcessService
 *
 * @access public
 * @author Somsack Sipasseuth, <s.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ProcessService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute processClass
     *
     * @access protected
     * @var Resource
     */
    protected $processClass = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <s.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 10-13-1-39-1f91722d:12e9641f6ad:-8000:0000000000002C5A begin
		parent::__construct();
		$this->processClass = new core_kernel_classes_Class(CLASS_PROCESS);
        // section 10-13-1-39-1f91722d:12e9641f6ad:-8000:0000000000002C5A end
    }

    /**
     * Short description of method getProcess
     *
     * @access public
     * @author Somsack Sipasseuth, <s.sipasseuth@tudor.lu>
     * @param  string identifier
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getProcess($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 10-13-1-39-1f91722d:12e9641f6ad:-8000:0000000000002C5C begin
		if(is_null($clazz) && $mode == 'uri'){
			try{
				$resource = new core_kernel_classes_Resource($identifier);
				$type = $resource->getUniquePropertyValue(new core_kernel_classes_Property( RDF_TYPE ));
				$clazz = new core_kernel_classes_Class($type->uriResource);
			}
			catch(Exception $e){}
		}
		if(is_null($clazz)){
			$clazz = $this->processClass;
		}
		if($this->isProcessClass($clazz)){
			$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
		}
        // section 10-13-1-39-1f91722d:12e9641f6ad:-8000:0000000000002C5C end

        return $returnValue;
    }

    /**
     * Short description of method getProcessClass
     *
     * @access public
     * @author Somsack Sipasseuth, <s.sipasseuth@tudor.lu>
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getProcessClass($uri = '')
    {
        $returnValue = null;

        // section 10-13-1-39-1f91722d:12e9641f6ad:-8000:0000000000002C6B begin
		if(empty($uri) && !is_null($this->processClass)){
			$returnValue = $this->processClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isProcessClass($clazz)){
				$returnValue = $clazz;
			}
		}
        // section 10-13-1-39-1f91722d:12e9641f6ad:-8000:0000000000002C6B end

        return $returnValue;
    }

    /**
     * Short description of method isProcessClass
     *
     * @access public
     * @author Somsack Sipasseuth, <s.sipasseuth@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function isProcessClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 10-13-1-39-1f91722d:12e9641f6ad:-8000:0000000000002C70 begin
		if($clazz->uriResource == $this->processClass->uriResource){
			$returnValue = true;	
		}
		else{
			foreach($this->processClass->getSubClasses() as $subclass){
				if($clazz->uriResource == $subclass->uriResource){
					$returnValue = true;
					break;	
				}
			}
		}
        // section 10-13-1-39-1f91722d:12e9641f6ad:-8000:0000000000002C70 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method cloneProcess
     *
     * @access public
     * @author Somsack Sipasseuth, <s.sipasseuth@tudor.lu>
     * @param  Resource instance
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function cloneProcess( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz)
    {
        $returnValue = null;

        // section 10-13-1-39-1f91722d:12e9641f6ad:-8000:0000000000002C73 begin
		if(!is_null($instance) && !is_null($clazz)){
			$processCloner = new wfEngine_models_classes_ProcessCloner();
			$returnValue = $processCloner->cloneProcess($instance);
		}				
        // section 10-13-1-39-1f91722d:12e9641f6ad:-8000:0000000000002C73 end

        return $returnValue;
    }

} /* end of class wfEngine_models_classes_ProcessService */

?>