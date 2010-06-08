<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 08.06.2010, 11:05:58 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
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
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('tao/models/classes/class.Service.php');

/* user defined includes */
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-includes begin
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-includes end

/* user defined constants */
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-constants begin
// section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C05-constants end

/**
 * Short description of class wfEngine_models_classes_VariableService
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_VariableService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method save
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array variable
     * @return mixed
     */
    public function save($variable)
    {
        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C07 begin
        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C07 end
    }

    /**
     * Short description of method remove
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  mixed params
     * @return boolean
     */
    public function remove( $params)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0B begin
        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0B end

        return (bool) $returnValue;
    }

    /**
     * Short description of method get
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string key
     * @return mixed
     */
    public function get($key)
    {
        $returnValue = null;

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0E begin
        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C0E end

        return $returnValue;
    }

    /**
     * Short description of method getAll
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getAll()
    {
        $returnValue = array();

        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C11 begin
        // section -87--2--3--76--7eb229c2:12916be1ece:-8000:0000000000003C11 end

        return (array) $returnValue;
    }

} /* end of class wfEngine_models_classes_VariableService */

?>