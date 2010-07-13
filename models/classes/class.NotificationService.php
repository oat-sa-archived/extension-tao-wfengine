<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 13.07.2010, 13:32:03 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.Service.php');

/* user defined includes */
// section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002233-includes begin
// section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002233-includes end

/* user defined constants */
// section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002233-constants begin
// section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002233-constants end

/**
 * Short description of class wfEngine_models_classes_NotificationService
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_NotificationService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute notificationClass
     *
     * @access protected
     * @var Class
     */
    protected $notificationClass = null;

    /**
     * Short description of attribute notificationSentProp
     *
     * @access protected
     * @var Property
     */
    protected $notificationSentProp = null;

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
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002240 begin
        
    	$this->notificationClass = new core_kernel_classes_Class(CLASS_NOTIFICATION);
    	$this->notificationSentProp = new core_kernel_classes_Property(PROPERTY_NOTIFICATION_SENT);
    	
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002240 end
    }

    /**
     * Short description of method trigger
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource connector
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function trigger( core_kernel_classes_Resource $connector,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000223C begin
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000223C end

        return $returnValue;
    }

    /**
     * Short description of method getNotificationsToSend
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getNotificationsToSend()
    {
        $returnValue = array();

        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002242 begin
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002242 end

        return (array) $returnValue;
    }

    /**
     * Short description of method sendNotifications
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Adapter adapter
     * @return boolean
     */
    public function sendNotifications( tao_helpers_transfert_Adapter $adapter)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000228C begin
        
        if(!is_null($adapter)){
        	
        	//initialize properties used in the loop
        	$notificationToProp 				= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_TO);
        	$notificationConnectorProp 			= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_CONNECTOR);
        	$notificationDateProp 				= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_DATE);
        	$connectorNotificationMessageProp 	=  new core_kernel_classes_Property(PROPERTY_CONNECTOR_NOTIFICATION_MESSAGE);
        	$userMailProp 						= new core_kernel_classes_Property(PROPERTY_USER_MAIL);
        	$notificationProcessExecProp 		= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_PROCESS_EXECUTION);
        	$processExecutionOfProp 			= new core_kernel_classes_Property(EXECUTION_OF);
        	
        	
        	//create messages from the notifications resources
        	$notificationsToSend = $this->getNotificationsToSend();
        	foreach($notificationsToSend as $notificationResource){
        		
        		//get the message content from the connector
        		$content = '';
        		$connector = $notificationResource->getOnePropertyValue($notificationConnectorProp);
        		if(!is_null($connector)){
        			$content = (string)$connector->getOnePropertyValue($connectorNotificationMessageProp);
        		}
        		
        		//get the email of the user
        		$toEmail = '';
        		$to = (string)$notificationResource->getOnePropertyValue($notificationToProp);
        		if(!is_null($to)){
        			$toEmail = (string)$to->getOnePropertyValue($userMailProp);
        		}
        		
        		//get the name of the concerned process
        		$processName = '';
        		$processExec = $notificationResource->getOnePropertyValue($notificationProcessExecProp);
        		if($processExec instanceof core_kernel_classes_Resource){
        			$process = $process->getOnePropertyValue($processExecutionOfProp);
        			if($process instanceof core_kernel_classes_Resource){
        				$processName = $process->getLabel();
        			}
        		}
        		
        		//create the message instance
        		$messages = array();
        		if(!empty($toEmail) && !empty($content)){
        			$message = new tao_helpers_transfert_Message();
        			$message->setTitle(__("[TAO Notification System] Process").' : '.$processName);
        			$message->setBody($content);
        			$message->setTo($toEmail);
        			$message->setFrom("notifications@tao.lu");
        			
        			$messages[$notificationResource->uriResource] = $message;
        		}
        	}
        	
        	if(count($messages) > 0){
        		$adapter->setMessages(&$messages);
        		$returnValue = (count($messages) == $adapter->send());
        		
        		foreach($adapter->getMessages() as $notificationUri => $message){
        			if($message->getStatus() == tao_helpers_transfert_Message::STATUS_SENT){
        				$notificationResource = new core_kernel_classes_Resource($notificationUri);
        				
        				$notificationResource->setPropertyValue($this->notificationSentProp, GENERIS_TRUE);
        				$notificationResource->setPropertyValue($notificationDateProp, date("Y-m-d H:i:s"));
        			}
        		}
        	}
        	
        }
        
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000228C end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_NotificationService */

?>