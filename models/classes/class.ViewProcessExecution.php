<?php

error_reporting(E_ALL);

/**
 * WorkFlowEngine - class.ViewProcessExecution.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatic generated with ArgoUML 0.24 on 11.08.2008, 09:28:22
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include ProcessExecution
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.ProcessExecution.php');

/* user defined includes */
// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008CE-includes begin
// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008CE-includes end

/* user defined constants */
// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008CE-constants begin
// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008CE-constants end

/**
 * Short description of class ViewProcessExecution
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class ViewProcessExecution
    extends ProcessExecution
{
    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method drawSvg
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function drawSvg()
    {
        $returnValue = (string) '';

        // section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008D1 begin
		$this->process->hlActivities = $this->currentActivity;
		$this->process->execution=$this;
		$returnValue = $this->process->drawSvg();
        // section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008D1 end

        return (string) $returnValue;
    }

} /* end of class ViewProcessExecution */

?>