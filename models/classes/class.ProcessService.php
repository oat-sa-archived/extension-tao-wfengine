<?php

error_reporting(E_ALL);

/**
 * Service methods to manage the Groups business models using the RDF API.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoGroups
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.Service.php');


/**
 * Service methods to manage the Groups business models using the RDF API.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoGroups
 * @subpackage models_classes
 */
class wfEngine_models_classes_ProcessService
    extends wfEngine_models_classes_ProcessAuthoringService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The RDFS top level group class
     *
     * @access protected
     * @var Class
     */
    protected $processClass = null;

    /**
     * The ontologies to load
     *
     * @access protected
     * @var array
     */
    protected $processOntologies = array(NS_TAOQUAL);

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1-506607cb:1249f78eef0:-8000:0000000000001AEB begin
		
		parent::__construct();
		$this->processClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$this->loadOntologies($this->processOntologies);
		
        // section 127-0-1-1-506607cb:1249f78eef0:-8000:0000000000001AEB end
    }

    /**
     * get a group subclass by uri. 
     * If the uri is not set, it returns the group class (the top level class.
     * If the uri don't reference a group subclass, it returns null
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getProcessClass($uri = '')
    {
        $returnValue = null;
		
		if(empty($uri) && !is_null($this->processClass)){
			$returnValue = $this->processClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isProcessClass($clazz)){
				$returnValue = $clazz;
			}
		}

        return $returnValue;
    }

    /**
     * Short description of method getGroup
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string identifier usually the test label or the ressource URI
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getProcess($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

		
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
		

        return $returnValue;
    }

    /**
     * Short description of method createGroup
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string label
     * @param  ContainerCollection members
     * @param  ContainerCollection tests
     * @return core_kernel_classes_Resource
     */
    public function createProcess($label,  core_kernel_classes_ContainerCollection $members,  core_kernel_classes_ContainerCollection $tests)
    {
        $returnValue = null;


        return $returnValue;
    }

    /**
     * Short description of method isGroupClass
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function isProcessClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--5cd530d7:1249feedb80:-8000:0000000000001AEA begin
		
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
		
        // section 127-0-1-1--5cd530d7:1249feedb80:-8000:0000000000001AEA end

        return (bool) $returnValue;
    }
 

} /* end of class taoGroups_models_classes_GroupsService */

?>