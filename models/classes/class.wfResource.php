<?php

error_reporting(E_ALL);

/**
 * WorkFlowEngine - class.wfResource.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatic generated with ArgoUML 0.24 on 11.08.2008, 09:28:21
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000831-includes begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000831-includes end

/* user defined constants */
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000831-constants begin
// section 10-13-1--31-740bb989:119ebfa9b28:-8000:0000000000000831-constants end

/**
 * Short description of class wfResource
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class wfResource
{
    // --- ATTRIBUTES ---

    /**
     * Short description of attribute uri
     *
     * @access public
     * @var string
     */
	
	public $resource;
    public $uri = '';

    /**
     * Short description of attribute label
     *
     * @access public
     * @var string
     */
    public $label = '';

    /**
     * Short description of attribute comment
     *
     * @access public
     * @var string
     */
    public $comment = '';

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
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008B2 begin
		$this->uri =$uri;
		$this->resource = new core_kernel_classes_Resource($uri,__METHOD__);
		$this->setLabel($this->resource->getLabel());
		$this->setComment($this->resource->comment);
			
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008B2 end
    }

    /**
     * Short description of method setLabel
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param string
     * @return void
     */
    public function setLabel($label)
    {
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008DF begin
		$this->label = str_replace(" ","&nbsp;",trim(strip_tags($label)));
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008DF end
    }

    /**
     * Short description of method setComment
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param string
     * @return void
     */
    public function setComment($comment)
    {
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008E1 begin
		$this->comment = trim(strip_tags($comment));
        // section 10-13-1--31-740bb989:119ebfa9b28:-8000:00000000000008E1 end
    }

    /**
     * Short description of method remove
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    public function remove()
    {
        // section 10-13-1--31-7f1456d9:11a242e5517:-8000:000000000000096F begin
		$this->resource->delete();
        // section 10-13-1--31-7f1456d9:11a242e5517:-8000:000000000000096F end
    }

} /* end of class wfResource */

?>