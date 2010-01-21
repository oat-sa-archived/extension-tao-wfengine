<?php

error_reporting(E_ALL);

/**
 * WorkFlowEngine - class.WfRole.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatic generated with ArgoUML 0.24 on 21.08.2008, 09:12:55
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include WfUser
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.WfUser.php');

/**
 * include wfResource
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.wfResource.php');

/* user defined includes */
// section 10-13-1-85-16731180:11be4127421:-8000:00000000000009B6-includes begin
// section 10-13-1-85-16731180:11be4127421:-8000:00000000000009B6-includes end

/* user defined constants */
// section 10-13-1-85-16731180:11be4127421:-8000:00000000000009B6-constants begin
// section 10-13-1-85-16731180:11be4127421:-8000:00000000000009B6-constants end

/**
 * Short description of class WfRole
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class WfRole
    extends wfResource
{
    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param string
     * @return void
     */
    public function __construct($uri)
    {
        // section 10-13-1-85-16731180:11be4127421:-8000:00000000000009D8 begin
        parent::__construct($uri);
        // section 10-13-1-85-16731180:11be4127421:-8000:00000000000009D8 end
    }

} /* end of class WfRole */

?>