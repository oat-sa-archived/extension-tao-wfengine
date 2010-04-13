<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - wfEngine/actions/form/class.Login.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 13.04.2010, 11:05:13 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023BF-includes begin
// section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023BF-includes end

/* user defined constants */
// section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023BF-constants begin
// section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023BF-constants end

/**
 * Short description of class wfEngine_actions_form_Login
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage actions_form
 */
class wfEngine_actions_form_Login
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        // section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023C0 begin
        
    	$this->form = tao_helpers_form_FormFactory::getForm('login');
		
		$connectElt = tao_helpers_form_FormFactory::getElement('connect', 'Submit');
		$connectElt->setValue(__('Connect'));
		$this->form->setActions(array($connectElt), 'bottom');
    	
        // section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023C0 end
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        // section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023C2 begin
        
    	if($this->data['route']){
    		$routeElt = tao_helpers_form_FormFactory::getElement('route', 'Hidden');
    		$routeElt->setValue('true');
    		$this->form->addElement($routeElt);
    		
    		$fromElt = tao_helpers_form_FormFactory::getElement('from', 'Hidden');
    		$fromElt->setValue($this->data['from']);
    		$this->form->addElement($fromElt);
    		
    		$fromQueryElt = tao_helpers_form_FormFactory::getElement('fromQuery', 'Hidden');
    		$fromQueryElt->setValue($this->data['fromQuery']);
    		$this->form->addElement($fromQueryElt);
    	}
    	
    	$loginElt = tao_helpers_form_FormFactory::getElement('login', 'Textbox');
		$loginElt->setDescription(__('Login'));
		$loginElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		$this->form->addElement($loginElt);
		
		$passElt = tao_helpers_form_FormFactory::getElement('password', 'Hiddenbox');
		$passElt->setDescription(__('Password'));
		$passElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		$this->form->addElement($passElt);
    	
        // section 127-0-1-1-68f1e705:127f61c8a56:-8000:00000000000023C2 end
    }

} /* end of class wfEngine_actions_form_Login */

?>