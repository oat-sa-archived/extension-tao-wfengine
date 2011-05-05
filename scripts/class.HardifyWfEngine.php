<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/scripts/class.HardifyWfEngine.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.05.2011, 12:13:09 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage scripts
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_scripts_Runner
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/scripts/class.Runner.php');

/* user defined includes */
// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD1-includes begin
// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD1-includes end

/* user defined constants */
// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD1-constants begin
// section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD1-constants end

/**
 * Short description of class wfEngine_scripts_HardifyWfEngine
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage scripts
 */
class wfEngine_scripts_HardifyWfEngine
    extends tao_scripts_Runner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute MODE_SMOOTH2HARD
     *
     * @access public
     * @var int
     */
    const MODE_SMOOTH2HARD = 1;

    /**
     * Short description of attribute MODE_HARD2SMOOTH
     *
     * @access public
     * @var int
     */
    const MODE_HARD2SMOOTH = 2;

    /**
     * Short description of attribute mode
     *
     * @access protected
     * @var int
     */
    protected $mode = 0;

    // --- OPERATIONS ---

    /**
     * Short description of method preRun
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function preRun()
    {
        // section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD6 begin
        
    	if(isset($this->parameters['compile']) && $this->parameters['compile'] == true){
    		$this->mode = self::MODE_SMOOTH2HARD;
    	}
    	if(isset($this->parameters['decompile']) && $this->parameters['decompile'] == true){
    		$this->mode = self::MODE_HARD2SMOOTH;
    	}
    	
        // section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD6 end
    }

    /**
     * Short description of method run
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function run()
    {
        // section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD4 begin
        
    	
    	
    	switch($this->mode){ 
    		case self::MODE_SMOOTH2HARD:
    			$this->out("Compiling workflow triples to relational database", array('color' => 'light_blue'));
    			break;
    			
    		
    		case self::MODE_HARD2SMOOTH:
    			$this->err('this mode is not yet implemented', true);
    			break;
    		
    		default:
    			$this->err('Unknow mode', true);
    	}
    	
        // section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD4 end
    }

    /**
     * Short description of method postRun
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function postRun()
    {
        // section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD8 begin
        // section 127-0-1-1-22592813:12fbf8723a0:-8000:0000000000002FD8 end
    }

} /* end of class wfEngine_scripts_HardifyWfEngine */

?>