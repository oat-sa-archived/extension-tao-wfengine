<?php

error_reporting(E_ALL);

/**
 * TAO - wfEngine/models/classes/class.NotificationService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 10.08.2010, 16:41:04 with ArgoUML PHP module 
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
require_once('tao/models/classes/class.GenerisService.php');

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
    extends tao_models_classes_GenerisService
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

    /**
     * Short description of attribute notificationToProp
     *
     * @access protected
     * @var Property
     */
    protected $notificationToProp = null;

    /**
     * Short description of attribute notificationConnectorProp
     *
     * @access protected
     * @var Property
     */
    protected $notificationConnectorProp = null;

    /**
     * Short description of attribute notificationDateProp
     *
     * @access protected
     * @var Property
     */
    protected $notificationDateProp = null;

    /**
     * Short description of attribute notificationProcessExecProp
     *
     * @access protected
     * @var Property
     */
    protected $notificationProcessExecProp = null;

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
        
    	$this->notificationClass 			= new core_kernel_classes_Class(CLASS_NOTIFICATION);
    	$this->notificationSentProp 		= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_SENT);
    	$this->notificationToProp 			= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_TO);
        $this->notificationConnectorProp 	= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_CONNECTOR);
        $this->notificationDateProp 		= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_DATE);
        $this->notificationProcessExecProp 	= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_PROCESS_EXECUTION);
    	
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:0000000000002240 end
    }

    /**
     * Short description of method trigger
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource connector
     * @param  Resource processExecution
     * @return int
     */
    public function trigger( core_kernel_classes_Resource $connector,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000223C begin
        
        if(!is_null($connector) && !is_null($processExecution)){
	        
        	//initialize properties 
        	$connectorUserNotifiedProp			= new core_kernel_classes_Property(PROPERTY_CONNECTOR_USER_NOTIFIED);
        	$connectorRoleNotifiedProp 			= new core_kernel_classes_Property(PROPERTY_CONNECTOR_ROLE_NOTIFIED);
        	$connectorPreviousActivitiesProp 	= new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES);
        	$connectorNextActivitiesProp 		= new core_kernel_classes_Property(PROPERTY_CONENCTORS_NEXTACTIVITIES);
        	$activityExecutionUserProp 			= new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER);
        	$activityAclModeProp 				= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ACL_MODE);
        	$activityAclUserProp				= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_USER);
        	$activityAclRoleProp				= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_ROLE);
        	
        	$roleService 				= tao_models_classes_ServiceFactory::get("wfEngine_models_classes_RoleService");
        	$activityExecutionService 	= tao_models_classes_ServiceFactory::get("wfEngine_models_classes_ActivityExecutionService");
        	
        	$users = array();
        	
        	//get the notifications mode defined for that connector
        	$notifyModes = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTOR_NOTIFY));
	        foreach($notifyModes->getIterator() as $notify){
	        	
	        	//get the users regarding the notification mode
	        	switch($notify->uriResource){
	        		
	        		//users directly defined 
	        		case INSTANCE_NOTIFY_USER: 
	        			foreach($connector->getPropertyValues($connectorUserNotifiedProp) as $userUri){
	        				if(!in_array($userUri, $users)){
	        					$users[] = $userUri;
	        				}
	        			}
	        			break;
	        			
	        		//users from roles directly defined 
	        		case INSTANCE_NOTIFY_ROLE:
        				foreach($connector->getPropertyValues($connectorRoleNotifiedProp)  as $roleUri){
        					foreach($roleService->getUsers(new core_kernel_classes_Resource($roleUri)) as $userUri){
        						if(!in_array($userUri, $users)){
        							$users[] = $userUri;
        						}
        					}
        				}
	        			break;
	        			
	        		//get the users who have executed the previous activity
	        		case INSTANCE_NOTIFY_PREVIOUS:
	        			$previousActivities = $connector->getPropertyValuesCollection($connectorPreviousActivitiesProp);
	        			foreach($previousActivities->getIterator() as $activity){
	        				foreach($activityExecutionService->getExecutions($activity, $processExecution) as $activityExecution){
								$activityExecutionUser = $activityExecution->getOnePropertyValue($activityExecutionUserProp);
								if(!is_null($activityExecutionUser)){
									if(!in_array($activityExecutionUser->uriResource, $users)){
										$users[] = $activityExecutionUser->uriResource;
									}
								}				
	        				}
	        			}
	        			break;
	        			
	        		//get the users 
	        		case INSTANCE_NOTIFY_NEXT:
	        			$nextActivities = $connector->getPropertyValuesCollection($connectorNextActivitiesProp);
	        			foreach($nextActivities->getIterator() as $activity){
	        				$mode = $activity->getOnePropertyValue($activityAclModeProp);
	        				if($mode instanceof core_kernel_classes_Resource){
	        					switch($mode->uriResource){
	        						case INSTANCE_ACL_USER:
	        							foreach($activity->getPropertyValues($activityAclUserProp) as $userInstanceUri){
	        								if(!in_array($userInstanceUri, $users)){
												$users[] = $userInstanceUri;
											}
	        							}
	        							break;
	        						case INSTANCE_ACL_ROLE:
	        						case INSTANCE_ACL_ROLE_RESTRICTED_USER:
	        						case INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED:
	        							foreach($activity->getPropertyValues($activityAclRolesProp) as $roleUri){
				        					foreach($roleService->getUsers(new core_kernel_classes_Resource($roleUri)) as $userUri){
				        						if(!in_array($userUri, $users)){
				        							$users[] = $userUri;
				        						}
				        					}
	        							}
	        							break;
	        					}
	        				}
	        			}
	        			break;
	        	}
	        }
	        
	        foreach($users as $userUri){
	        	$notification = $this->notificationClass->createInstance();
	        	$notification->setPropertyValue($this->notificationToProp, $userUri);
	        	$notification->setPropertyValue($this->notificationProcessExecProp, $processExecution->uriResource);
	        	$notification->setPropertyValue($this->notificationConnectorProp, $connector->uriResource);
	        	$notification->setPropertyValue($this->notificationSentProp, GENERIS_FALSE);
	        	$notification->setPropertyValue($this->notificationDateProp, date("Y-m-d H:i:s"));
	        	$returnValue++;
	        }
        }
        
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000223C end

        return (int) $returnValue;
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
        
        //get the notifications with the sent property to false
        $apiModel  	= core_kernel_impl_ApiModelOO::singleton();
	    $notifications = $apiModel->getSubject($this->notificationSentProp->uriResource, GENERIS_FALSE);
	    foreach($notifications->getIterator() as $notification){
	    	
	    	//there a date prop by sending try. After 4 try, we stop to try (5 because the 4 try and the 1st date is the creation date) 
	    	$dates = $notification->getPropertyValues($this->notificationDateProp);
	    	if(count($dates) < 5){
	    		$returnValue[] = $notification;
	    	} 
	    }
        
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
        	
        	$connectorNotificationMessageProp 	= new core_kernel_classes_Property(PROPERTY_CONNECTOR_NOTIFICATION_MESSAGE);
        	$userMailProp 						= new core_kernel_classes_Property(PROPERTY_USER_MAIL);
        	$processExecutionOfProp 			= new core_kernel_classes_Property(EXECUTION_OF);
        	
        	//create messages from the notifications resources
        	$messages = array();
        	$notificationsToSend = $this->getNotificationsToSend();
        	foreach($notificationsToSend as $notificationResource){
        		
        		//get the message content from the connector
        		$content = '';
        		$connector = $notificationResource->getOnePropertyValue($this->notificationConnectorProp);
        		if(!is_null($connector)){
        			foreach($connector->getPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTOR_NOTIFICATION_MESSAGE)) as $content){
        				if(strlen(trim($content)) > 0){
        					break;
        				}
        			}
        		}
        		
        		//get the email of the user
        		$toEmail = '';
        		$to = $notificationResource->getOnePropertyValue($this->notificationToProp);
        		if(!is_null($to)){
        			$toEmail = (string)$to->getOnePropertyValue($userMailProp);
        		}
        		
        		//get the name of the concerned process
        		$processName = '';
        		$processExec = $notificationResource->getOnePropertyValue($this->notificationProcessExecProp);
        		if($processExec instanceof core_kernel_classes_Resource){
        			$process = $processExec->getOnePropertyValue($processExecutionOfProp);
        			if($process instanceof core_kernel_classes_Resource){
        				$processName = $process->getLabel()." / ".$processExec->getLabel();
        			}
        		}
        		
        		//create the message instance
        		
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
        				$notificationResource->editPropertyValues($this->notificationSentProp, GENERIS_TRUE);
        			}
        			//add a new date at each sending try
        			$notificationResource->setPropertyValue($this->notificationDateProp, date("Y-m-d H:i:s"));
        		}
        	}
        }
        
        // section 127-0-1-1-1609ec43:129caf00b07:-8000:000000000000228C end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_NotificationService */

?>