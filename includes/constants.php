<?php
define('NS_WFENGINE', 'http://www.tao.lu/middleware/wfEngine.rdf');
define('NS_RULES', 'http://www.tao.lu/middleware/Rules.rdf');
$todefine = array(
	'ENABLE_HTTP_REDIRECT_PROCESS_BROWSER' 			=> false,
	
	'GENERIS_BOOLEAN' 								=> GENERIS_NS . '#Boolean',
	'PROPERTY_USER_LABEL'							=> GENERIS_NS.'#login',
	'VAR_PROCESS_INSTANCE' 							=> NS_RULES . '#VAR_PROCESS_INSTANCE',
	
	'PROPERTY_GENERIS_ALLOWFREEVALUEOF'				=> NS_WFENGINE . '#PropertyAllowFreeValueOf',
	
	'CLASS_PROCESS'									=> NS_WFENGINE . '#ClassProcessDefinitions',
	'PROPERTY_PROCESS_VARIABLES'					=> NS_WFENGINE . '#PropertyProcessVariables',
	'PROPERTY_PROCESS_DIAGRAMDATA'					=> NS_WFENGINE . '#PropertyProcessDiagramData',
	'PROPERTY_PROCESS_ACTIVITIES'					=> NS_WFENGINE . '#PropertyProcessActivities',
	'PROPERTY_PROCESS_INIT_RESTRICTED_USER'			=> NS_WFENGINE . '#PropertyProcessInitRestrictedUser',
	'PROPERTY_PROCESS_INIT_RESTRICTED_ROLE'			=> NS_WFENGINE . '#PropertyProcessInitRestrictedRole',
	'PROPERTY_PROCESS_INIT_ACL_MODE'				=> NS_WFENGINE . '#PropertyProcessInitAccesControlMode',//!!!
	
	'CLASS_ROLE'									=> NS_WFENGINE . '#ClassRole',
	
	'CLASS_PROCESSINSTANCES'						=> NS_WFENGINE . '#ClassProcessInstances',
	'PROPERTY_PROCESSINSTANCES_STATUS'				=> NS_WFENGINE . '#PropertyProcessInstancesStatus',
	'PROPERTY_PROCESSINSTANCES_EXECUTIONOF'			=> NS_WFENGINE . '#PropertyProcessInstancesExecutionOf',
	'PROPERTY_PROCESSINSTANCES_CURRENTTOKEN'		=> NS_WFENGINE . '#PropertyProcessInstancesCurrentToken',
	'PROPERTY_PROCESSINSTANCES_PROCESSPATH'			=> NS_WFENGINE . '#PropertyProcessInstancesProcessPath',
	'PROPERTY_PROCESSINSTANCES_FULLPROCESSPATH'		=> NS_WFENGINE . '#PropertyProcessInstancesProcessFullPath',
	
	'INSTANCE_PROCESSSTATUS_RESUMED'				=> NS_WFENGINE . '#InstanceStatusResumed',
	'INSTANCE_PROCESSSTATUS_STARTED'				=> NS_WFENGINE . '#InstanceStatusStarted',
	'INSTANCE_PROCESSSTATUS_FINISHED'				=> NS_WFENGINE . '#InstanceStatusFinished',
	'INSTANCE_PROCESSSTATUS_PAUSED'					=> NS_WFENGINE . '#InstanceStatusPaused',

	'CLASS_PROCESSVARIABLES'						=> NS_WFENGINE . '#ClassProcessVariables',
	'PROPERTY_PROCESSVARIABLES_CODE'				=>  NS_WFENGINE . '#PropertyCode',
	
	'CLASS_ACTIVITIES'								=> NS_WFENGINE . '#ClassActivities',
	'PROPERTY_ACTIVITIES_INTERACTIVESERVICES'		=> NS_WFENGINE . '#PropertyActivitiesInteractiveServices',
	'PROPERTY_ACTIVITIES_RESTRICTED_USER'			=> NS_WFENGINE . '#PropertyActivitiesRestrictedUser',
	'PROPERTY_ACTIVITIES_RESTRICTED_ROLE'			=> NS_WFENGINE . '#PropertyActivitiesRestrictedRole',
	'PROPERTY_ACTIVITIES_ACL_MODE'					=> NS_WFENGINE . '#PropertyActivitiesAccessControlMode',
	'PROPERTY_ACTIVITIES_ISHIDDEN'					=> NS_WFENGINE . '#PropertyActivitiesHidden',
	'PROPERTY_ACTIVITIES_ISINITIAL'					=> NS_WFENGINE . '#PropertyActivitiesInitial',
	'PROPERTY_ACTIVITIES_CONTROLS'					=> NS_WFENGINE  .'#PropertyActivitiesControls',

	'CLASS_CONTROLS'								=> NS_WFENGINE  .'#ClassControls',
	'INSTANCE_CONTROL_BACKWARD'						=> NS_WFENGINE  .'#InstanceControlsBackward',
	'INSTANCE_CONTROL_FORWARD'						=> NS_WFENGINE  .'#InstanceControlsForward',
	
	'CLASS_CONNECTORS'								=> NS_WFENGINE . '#ClassConnectors',
	'PROPERTY_CONNECTORS_TRANSITIONRULE'			=> NS_WFENGINE . '#PropertyConnectorsTransitionRule',
	'PROPERTY_CONNECTORS_PREVIOUSACTIVITIES'		=> NS_WFENGINE . '#PropertyConnectorsPreviousActivities',
	'PROPERTY_CONNECTORS_NEXTACTIVITIES'			=> NS_WFENGINE . '#PropertyConnectorsNextActivities',
	'PROPERTY_CONNECTORS_ACTIVITYREFERENCE'			=> NS_WFENGINE . '#PropertyConnectorsActivityReference',
	'PROPERTY_CONNECTORS_TYPE' 						=> NS_WFENGINE . '#PropertyConnectorsType',
	'PROPERTY_CONNECTORS_NOTIFY'					=> NS_WFENGINE  .'#PropertyConnectorsNotificationModes',
	'PROPERTY_CONNECTORS_USER_NOTIFIED'				=> NS_WFENGINE  .'#PropertyConnectorsNotifiedUser',
	'PROPERTY_CONNECTORS_ROLE_NOTIFIED'				=> NS_WFENGINE  .'#PropertyConnectorsNotifiedRole',
	'PROPERTY_CONNECTORS_NOTIFICATION_MESSAGE'		=> NS_WFENGINE  .'#PropertyConnectorsNotificationMessage',
	
	'CLASS_TYPEOFCONNECTORS'						=> NS_WFENGINE . '#ClassTypeOfConnectors',
	'INSTANCE_TYPEOFCONNECTORS_CONDITIONAL'			=> NS_WFENGINE . '#InstanceTypeOfConnectorsConditional',
	'INSTANCE_TYPEOFCONNECTORS_SEQUENCE'			=> NS_WFENGINE . '#InstanceTypeOfConnectorsSequence',
	'INSTANCE_TYPEOFCONNECTORS_PARALLEL'			=> NS_WFENGINE . '#InstanceTypeOfConnectorsParallel',
	'INSTANCE_TYPEOFCONNECTORS_JOIN'				=> NS_WFENGINE . '#InstanceTypeOfConnectorsJoin',
	
	'CLASS_TRANSITIONRULES'							=> NS_WFENGINE . '#ClassTransitionRules',
	'PROPERTY_TRANSITIONRULES_THEN'					=> NS_WFENGINE . '#PropertyTransitionRulesThen',
	'PROPERTY_TRANSITIONRULES_ELSE'					=> NS_WFENGINE . '#PropertyTransitionRulesElse',
	
	'CLASS_NOTIFICATION_MODE' 						=> NS_WFENGINE  .'#ClassNotificationMode',
	'INSTANCE_NOTIFY_USER' 							=> NS_WFENGINE  .'#InstanceNotifyUser',
	'INSTANCE_NOTIFY_NEXT'	 						=> NS_WFENGINE  .'#InstanceNotifyNextActivityUsers',
	'INSTANCE_NOTIFY_PREVIOUS' 						=> NS_WFENGINE  .'#InstanceNotifyPreviousActivityUsers',
	'INSTANCE_NOTIFY_ROLE' 							=> NS_WFENGINE  .'#InstanceNotifyRole',

	'CLASS_NOTIFICATION' 							=> NS_WFENGINE  .'#ClassNotification',
	'PROPERTY_NOTIFICATION_TO' 						=> NS_WFENGINE  .'#PropertyNotificationTo',
	'PROPERTY_NOTIFICATION_CONNECTOR' 				=> NS_WFENGINE  .'#PropertyNotificationConnector',
	'PROPERTY_NOTIFICATION_PROCESS_EXECUTION' 		=> NS_WFENGINE  .'#PropertyNotificationProcessExecution',
	'PROPERTY_NOTIFICATION_SENT' 					=> NS_WFENGINE  .'#PropertyNotificationSent',
	'PROPERTY_NOTIFICATION_DATE' 					=> NS_WFENGINE  .'#PropertyNotificationDate',
	
	'CLASS_CALLOFSERVICES'							=> NS_WFENGINE . '#ClassCallOfServices',
	'PROPERTY_CALLOFSERVICES_SERVICEDEFINITION'		=> NS_WFENGINE . '#PropertyCallOfServicesServiceDefinition',
	'PROPERTY_CALLOFSERVICES_ACTUALPARAMETEROUT'	=> NS_WFENGINE . '#PropertyCallOfServicesActualParameterOut',
	'PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN'		=> NS_WFENGINE . '#PropertyCallOfServicesActualParameterin',
	'PROPERTY_CALLOFSERVICES_TOP'					=> NS_WFENGINE . '#PropertyCallOfServicesTop',
	'PROPERTY_CALLOFSERVICES_LEFT'					=> NS_WFENGINE . '#PropertyCallOfServicesLeft',
	'PROPERTY_CALLOFSERVICES_WIDTH'					=> NS_WFENGINE . '#PropertyCallOfServicesWidth',
	'PROPERTY_CALLOFSERVICES_HEIGHT'				=> NS_WFENGINE . '#PropertyCallOfServicesHeight',
	
	'CLASS_SERVICESDEFINITION'						=> NS_WFENGINE . '#ClassServiceDefinitions',
	'PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT'	=> NS_WFENGINE . '#PropertyServiceDefinitionsFormalParameterOut',
	'PROPERTY_SERVICESDEFINITION_FORMALPARAMIN' 	=> NS_WFENGINE . '#PropertyServiceDefinitionsFormalParameterIn',
	
	'CLASS_SUPPORTSERVICES'							=> NS_WFENGINE . '#ClassSupportServices',
	'PROPERTY_SUPPORTSERVICES_URL'					=> NS_WFENGINE .'#PropertySupportServicesUrl',
	
	'CLASS_WEBSERVICES'								=> NS_WFENGINE . '#ClassWebServices',
	
	'CLASS_FORMALPARAMETER'							=> NS_WFENGINE . '#ClassFormalParameters',
	'PROPERTY_FORMALPARAMETER_DEFAULTCONSTANTVALUE' => NS_WFENGINE . '#PropertyFormalParametersDefaultConstantValue',
	'PROPERTY_FORMALPARAMETER_DEFAULTPROCESSVARIABLE'=> NS_WFENGINE . '#PropertyFormalParametersDefaultProcessVariable',
	'PROPERTY_FORMALPARAMETER_NAME'					=> NS_WFENGINE . '#PropertyFormalParametersName',
	
	'CLASS_ACTUALPARAMETER'							=>  NS_WFENGINE . '#ClassActualParameters',
	'PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE'		=> NS_WFENGINE . '#PropertyActualParametersProcessVariable',
	'PROPERTY_ACTUALPARAMETER_CONSTANTVALUE'		=> NS_WFENGINE . '#PropertyActualParametersConstantValue',
	'PROPERTY_ACTUALPARAMETER_FORMALPARAMETER'		=> NS_WFENGINE . '#PropertyActualParametersFormalParameter',
	
	'CLASS_ACTIVITY_EXECUTION' 						=> NS_WFENGINE . '#ClassActivityExecutions',
	'PROPERTY_ACTIVITY_EXECUTION_ACTIVITY'			=> NS_WFENGINE . '#PropertyActivityExecutionsExecutionOf',
	'PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER'		=> NS_WFENGINE . '#PropertyActivityExecutionsCurrentUser',
	'PROPERTY_ACTIVITY_EXECUTION_IS_FINISHED' 		=> NS_WFENGINE . '#PropertyActivityExecutionsFinished',
	'PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION' 	=> NS_WFENGINE . '#PropertyActivityExecutionsProcessExecution',
	'PROPERTY_ACTIVITY_EXECUTION_CTX_RECOVERY'		=> NS_WFENGINE . '#PropertyActivityExecutionsContextRecovery',
	
	'CLASS_ACL_MODES'								=> NS_WFENGINE . '#ClassAccessControlModes',
	'INSTANCE_ACL_ROLE'								=> NS_WFENGINE . '#PropertyAccessControlModesRole',
	'INSTANCE_ACL_ROLE_RESTRICTED_USER'				=> NS_WFENGINE . '#PropertyAccessControlModesRoleRestrictedUser',
	'INSTANCE_ACL_USER'								=> NS_WFENGINE . '#PropertyAccessControlModesUser',
	'INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED'	=> NS_WFENGINE . '#PropertyAccessControlModesRoleRestrictedUserInherited',
	'INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY'    => NS_WFENGINE . '#PropertyAccessControlModesRoleRestrictedUserInheritedDelivery',
	
	'CLASS_TOKEN'									=> NS_WFENGINE  .'#ClassTokens',
	'PROPERTY_TOKEN_VARIABLE'						=> NS_WFENGINE  .'#PropertyTokensVariable',
	'PROPERTY_TOKEN_ACTIVITY'						=> NS_WFENGINE  .'#PropertyTokensActivity',
	'PROPERTY_TOKEN_ACTIVITYEXECUTION'				=> NS_WFENGINE  .'#PropertyTokensActivityExecution',
	'PROPERTY_TOKEN_CURRENTUSER'					=> NS_WFENGINE  .'#PropertyTokensCurrentUser',
	
	'API_LOGIN' 									=> SYS_USER_LOGIN,
	'API_PASSWORD' 									=> SYS_USER_PASS
);
?>