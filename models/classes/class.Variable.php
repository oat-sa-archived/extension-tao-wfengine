<?php

error_reporting(E_ALL);

/**
 * WorkFlowEngine - class.Variable.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatic generated with ArgoUML 0.24 on 22.08.2008, 13:35:34
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include WfResource
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.WfResource.php');

/* user defined includes */
// section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A14-includes begin
// section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A14-includes end

/* user defined constants */
// section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A14-constants begin
// section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A14-constants end

/**
 * Short description of class Variable
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Variable
    extends WfResource
{
    // --- ATTRIBUTES ---

    /**
     * Short description of attribute name
     *
     * @access public
     * @var string
     */
    public $code = '';

    /**
     * Short description of attribute value
     *
     * @access public
     * @var mixed
     */
    public $value = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param string
     * @param string
     * @return void
     */
    public function __construct($uri, $code, $value)
    {
        // section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A43 begin
        parent::__construct($uri);
        
        $this->code = $code;
        $this->value = $value;
        // section 10-13-1-85-16731180:11be4127421:-8000:0000000000000A43 end
    }

} /* end of class Variable */

?>