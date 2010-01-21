<?php

error_reporting(E_ALL);

/**
 * WorkFlowEngine - class.User.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatic generated with ArgoUML 0.24 on 15.05.2008, 16:30:15
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000883-includes begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000883-includes end

/* user defined constants */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000883-constants begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000883-constants end

/**
 * Short description of class User
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class User
{
    // --- ATTRIBUTES ---

    /**
     * Short description of attribute userName
     *
     * @access public
     * @var string
     */
    public $userName = '';

    /**
     * Short description of attribute roles
     *
     * @access public
     * @var array
     */
    public $roles = array();

    /**
     * Short description of attribute isAdmin
     *
     * @access public
     * @var boolean
     */
    public $isAdmin = true;

    /**
     * Short description of attribute connected
     *
     * @access public
     * @var boolean
     */
    public $connected = false;

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
    public function __construct($userUri, $userName)
    {
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008AE begin
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008AE end
    }

} /* end of class User */

?>