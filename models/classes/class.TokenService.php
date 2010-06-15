<?php

error_reporting(E_ALL);

/**
 * Enable you to manage the token. 
 * A token is an abstract container embeding the data of a user 
 * for a particular execution of a process acitivity.
 * It helps you to define data scope and contexts,0
 *  to manage the process time line, to wait the other users and
 * to know the current state of a process execution.
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
// section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001F98-includes begin
// section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001F98-includes end

/* user defined constants */
// section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001F98-constants begin
// section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001F98-constants end

/**
 * Enable you to manage the token. 
 * A token is an abstract container embeding the data of a user 
 * for a particular execution of a process acitivity.
 * It helps you to define data scope and contexts,0
 *  to manage the process time line, to wait the other users and
 * to know the current state of a process execution.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_TokenService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute CURRENT_KEY
     *
     * @access public
     * @var string
     */
    const CURRENT_KEY = 'current_tokens';

    /**
     * Short description of attribute tokenClass
     *
     * @access protected
     * @var Class
     */
    protected $tokenClass = null;

    /**
     * Short description of attribute tokenActivityProp
     *
     * @access protected
     * @var Property
     */
    protected $tokenActivityProp = null;

    /**
     * Short description of attribute tokenActivityExecutionProp
     *
     * @access protected
     * @var Property
     */
    protected $tokenActivityExecutionProp = null;

    /**
     * Short description of attribute tokenCurrentUserProp
     *
     * @access protected
     * @var Property
     */
    protected $tokenCurrentUserProp = null;

    /**
     * Short description of attribute tokenVariableProp
     *
     * @access protected
     * @var Property
     */
    protected $tokenVariableProp = null;

    /**
     * Short description of attribute currentTokenProp
     *
     * @access protected
     * @var Resource
     */
    protected $currentTokenProp = null;

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
        // section 127-0-1-1-24bd84b1:1291d596dba:-8000:0000000000001FB9 begin
        
    	$this->tokenClass = new core_kernel_classes_Class(CLASS_TOKEN);
    	
    	$this->tokenActivityProp 			= new core_kernel_classes_Property(PROPERTY_TOKEN_ACTIVITY);
    	$this->tokenActivityExecutionProp 	= new core_kernel_classes_Property(PROPERTY_TOKEN_ACTIVITYEXECUTION);
    	$this->tokenCurrentUserProp 		= new core_kernel_classes_Property(PROPERTY_TOKEN_CURRENTUSER);
    	$this->tokenVariableProp 			= new core_kernel_classes_Property(PROPERTY_TOKEN_VARIABLE);
    	$this->currentTokenProp				= new core_kernel_classes_Property(CURRENT_TOKEN);
    	
        // section 127-0-1-1-24bd84b1:1291d596dba:-8000:0000000000001FB9 end
    }

    /**
     * Short description of method getTokens
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activityExecution
     * @return array
     */
    public function getTokens( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = array();

        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001F9A begin
        
        if(!is_null($activityExecution)){
        	
        	$activityUser = $activityExecution->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER));
        	if(!is_null($activityUser)){
	        	
	        	$apiModel  	= core_kernel_impl_ApiModelOO::singleton();
	        	$tokenCollection = $apiModel->getSubject(PROPERTY_TOKEN_ACTIVITYEXECUTION, $activityExecution->uriResource);
	        	foreach($tokenCollection->getIterator() as $token){
	        		$tokenUser = $token->getOnePropertyValue($this->tokenCurrentUserProp);
	        		if(!is_null($tokenUser)){
	        			if($tokenUser->uriResource == $activityUser->uriResource){
	        				$returnValue[$token->uriResource] = $token;
	        			}
	        		}
	        	}
        	}
        }
        
        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001F9A end

        return (array) $returnValue;
    }

    /**
     * Short description of method getCurrent
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activityExecution
     * @return core_kernel_classes_Resource
     */
    public function getCurrent( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = null;

        // section 127-0-1-1--5f764609:12927334c45:-8000:0000000000001FF2 begin
        
        $executionTokens = $this->getTokens($activityExecution);
        if(count($executionTokens) > 0){
        	foreach($executionTokens as $token){
        		 $returnValue = $token;
        		 break;
        	}
        }
        
        // section 127-0-1-1--5f764609:12927334c45:-8000:0000000000001FF2 end

        return $returnValue;
    }

    /**
     * Short description of method getCurrents
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource processExecution
     * @return array
     */
    public function getCurrents( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = array();

        // section 127-0-1-1-2013ff6:1292105c669:-8000:0000000000001FD8 begin
        
   /*     if(Session::hasAttribute(self::CURRENT_KEY) && !is_null($processExecution)){
        	$tokens = Session::getAttribute(self::CURRENT_KEY);
        	if(is_array($tokens)){
        		$key = $processExecution->uriResource;
        		if(array_key_exists($key, $tokens)){
        			foreach($tokens[$key] as $tokenUri){
        				$returnValue[] = new core_kernel_classes_Resource($tokenUri);
        			}
        		}
        	}
        }*/
        
        if(!is_null($processExecution)){
        	$tokens = $processExecution->getPropertyValuesCollection($this->currentTokenProp);
        	foreach($tokens->getIterator() as $token){
        		$returnValue[] = $token;
        	}
        }
        
        // section 127-0-1-1-2013ff6:1292105c669:-8000:0000000000001FD8 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getCurrentActivities
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource processExecution
     * @return array
     */
    public function getCurrentActivities( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = array();

        // section 127-0-1-1--6657ec7c:129368db927:-8000:0000000000001FF5 begin
        
        foreach($this->getCurrents($processExecution) as $token){
        	$activity = $token->getOnePropertyValue($this->tokenActivityProp);
        	if(!is_null($activity)){
        		$returnValue[] = $activity;
        	}
        }
        
        // section 127-0-1-1--6657ec7c:129368db927:-8000:0000000000001FF5 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setCurrents
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource processExecution
     * @param  array tokens
     * @return mixed
     */
    public function setCurrents( core_kernel_classes_Resource $processExecution, $tokens)
    {
        // section 127-0-1-1-2013ff6:1292105c669:-8000:0000000000001FF3 begin
        
    	if(!is_null($processExecution)){
	    	if(!is_array($tokens) && !empty($tokens)){
	    		$tokens = array($tokens);
	    	}
	    	/*$currentTokens = array();
    		 foreach($tokens as $token){
    		 	if($token instanceof core_kernel_classes_Resource){
    		 		$currentTokens[] = $token->uriResource;
    		 	}
    		 }
    		 $tokens = array();
    		 if(Session::hasAttribute(self::CURRENT_KEY)){
    		 	$tokens = Session::getAttribute(self::CURRENT_KEY);
    		 }
    		 $tokens[$processExecution->uriResource] = $currentTokens;
    		 Session::setAttribute(self::CURRENT_KEY, $tokens);*/
    		
    		$processExecution->removePropertyValues($this->currentTokenProp);
    		foreach($tokens as $token){
    			$processExecution->setPropertyValue($this->currentTokenProp, $token->uriResource);
    		}
    	}
    	
        // section 127-0-1-1-2013ff6:1292105c669:-8000:0000000000001FF3 end
    }

    /**
     * Short description of method setCurrentActivities
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource processExecution
     * @param  array activities
     * @param  Resource user
     * @return mixed
     */
    public function setCurrentActivities( core_kernel_classes_Resource $processExecution, $activities,  core_kernel_classes_Resource $user)
    {
        // section 127-0-1-1--6657ec7c:129368db927:-8000:0000000000001FFF begin
        
    	$tokens = array();
    	$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
    	foreach($activities as $activity){
    		$execution = $activityExecutionService->getExecution($activity, $user, $processExecution);
    		if(!is_null($execution)){
    			foreach($this->getTokens($execution) as $token){
    				if(!arra_key_exists($token->uriResource, $tokens)){
    					$tokens[$token->uriResource] = $token;
    				}
    			}
    		}
    	}
		$this->setCurrents($processExecution, $tokens);
    	
        // section 127-0-1-1--6657ec7c:129368db927:-8000:0000000000001FFF end
    }

    /**
     * Short description of method getVariables
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource token
     * @return array
     */
    protected function getVariables( core_kernel_classes_Resource $token)
    {
        $returnValue = array();

        // section 127-0-1-1-2013ff6:1292105c669:-8000:0000000000001FCA begin
        
        if(!is_null($token)){
			$tokenVarKeys = @unserialize($token->getOnePropertyValue($this->tokenVariableProp));
			if($tokenVarKeys !== false){
				if(is_array($tokenVarKeys)){
					foreach($tokenVarKeys as $key){
						$collection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CODE, $key);
						if(!$collection->isEmpty()){
							if($collection->count() == 1) {
								$property = new core_kernel_classes_Property($collection->get(0)->uriResource);
								$returnValue[] =array(
									'code'			=> $key,
									'propertyUri'	=> $property->uriResource,
									'value'			=> $token->getPropertyValues($property)
								);
							}
						}
					}
				}
			}
        }
        
        // section 127-0-1-1-2013ff6:1292105c669:-8000:0000000000001FCA end

        return (array) $returnValue;
    }

    /**
     * Short description of method create
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource activity
     * @return core_kernel_classes_Resource
     */
    public function create( core_kernel_classes_Resource $activity)
    {
        $returnValue = null;

        // section 127-0-1-1-2013ff6:1292105c669:-8000:0000000000001FCD begin
        
       $returnValue = $this->createInstance($this->tokenClass);
       $returnValue->setPropertyValue($this->tokenActivityProp, $activity->uriResource);
        
      //echo "Create token ".$returnValue->getLabel()." for activity".$activity->getLabel()."<br>";
       
        // section 127-0-1-1-2013ff6:1292105c669:-8000:0000000000001FCD end

        return $returnValue;
    }

    /**
     * Short description of method assign
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource token
     * @param  Resource activityExecution
     * @return core_kernel_classes_Resource
     */
    public function assign( core_kernel_classes_Resource $token,  core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = null;

        // section 127-0-1-1-2013ff6:1292105c669:-8000:0000000000001FD0 begin
        
       
        if(!is_null($token) && !is_null($activityExecution)){
        	 
        	$user  = $activityExecution->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER));
        	if(!is_null($user)){
        		$token->setPropertyValue($this->tokenCurrentUserProp, $user->uriResource);
        	}
        	
        	$activity  = $activityExecution->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY));
        	if(!is_null($activity)){
        		$token->setPropertyValue($this->tokenActivityProp, $activity->uriResource);
        	}
        	
        	$token->setPropertyValue($this->tokenActivityExecutionProp, $activityExecution->uriResource);
        
        	/*error_reporting(E_ALL);
        	var_dump($token, $activityExecution,$user, $activity);exit;
        	*/
        	//echo "Assign token ".$returnValue->getLabel()." to user ".$user->getLabel()." and activity execution of ".$activity->getLabel()."<br>";
        }
        $returnValue = $token;
        
        // section 127-0-1-1-2013ff6:1292105c669:-8000:0000000000001FD0 end

        return $returnValue;
    }

    /**
     * Short description of method dispatch
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array tokens
     * @param  Resource activityExecution
     * @return boolean
     */
    public function dispatch($tokens,  core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--44e2213d:12922223449:-8000:0000000000001FE0 begin
        
        if(!is_null($activityExecution)){
        	if(count($this->getTokens($activityExecution) == 0)){
	        	$activity = $activityExecution->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY));
		        foreach($tokens as $token){
		        	
		        	$tokenActivity = $token->getOnePropertyValue($this->tokenActivityProp);
		        	$tokenActivityExecution = $token->getOnePropertyValue($this->tokenActivityExecutionProp);
		        	if(!is_null($tokenActivity)){
		        		if($tokenActivity->uriResource == $activity->uriResource){
		        			
		        			if(is_null($tokenActivityExecution)){
			        			$this->assign($token, $activityExecution);
			        			$returnValue = true;
			        			break;
		        			}
		        			else if($tokenActivityExecution->uriResource == $activityExecution->uriResource){
		        				break;
		        			}
		        		}
		        	}
		        }
	        }
        }
        
        // section 127-0-1-1--44e2213d:12922223449:-8000:0000000000001FE0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method duplicate
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource token
     * @return core_kernel_classes_Resource
     */
    public function duplicate( core_kernel_classes_Resource $token)
    {
        $returnValue = null;

        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001FA2 begin
        
        if(!is_null($token)){
        
        	$newToken = $this->createInstance($this->tokenClass);
	        $variables = $this->getVariables($token);
	        if(count($variables) > 0){
	        	$keys = array();
	        	foreach($variables as $variable){
	        		foreach($variable['value'] as $value){
	        			$newToken->setPropertyValue(new core_kernel_classes_Property($variable['propertyUri']), $value); 
	        		}
	        		$keys[] = $variable['code'];
	        	}
	        	$newToken->setPropertyValue($this->tokenVariableProp, serialize($keys)); 
	        }
	        $returnValue = $newToken;
        }
        
        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001FA2 end

        return $returnValue;
    }

    /**
     * Short description of method merge
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array tokens
     * @return core_kernel_classes_Resource
     */
    public function merge($tokens)
    {
        $returnValue = null;

        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001FA5 begin
        
        //get tokens variables 
        $allVars = array();
        foreach($tokens as $i => $token){
        	$allVars[$i] = $this->getVariables($token);
        }
        
        //merge the variables
        $mergedVars = array();
        foreach($allVars as $tokenVars){
        	foreach($tokenVars as $tokenVar){
        		$key = $tokenVar['code'];
        		foreach($tokenVar['value'] as $value){
	        		if(array_key_exists($key, $mergedVars)){
	        			if(is_array($mergedVars[$key])){
	        				$mergedVars[$key][] = $value;
	        			}
	        			else{
	        				if($mergedVars[$key] != $value){
								$mergedVars[$key] = array($mergedVars[$key], $value);
	        				}
	        			}
	        		}
	        		else{
	        			$mergedVars[$key] = $value;
	        		}
        		}
        	}
        }
        
        //create the merged token
        $newToken = $this->createInstance($this->tokenClass);
        if(count($mergedVars) > 0){
        	
        	$keys = array();
        	foreach($mergedVars as $code => $values){
        		$collection = core_kernel_impl_ApiModelOO::singleton()->getSubject(PROPERTY_CODE, $code);
					if(!$collection->isEmpty()){
						if($collection->count() == 1) {
							if(is_array($values)){
								foreach($values as $value){
									$newToken->setPropertyValue(new core_kernel_classes_Property($collection->get(0)->uriResource),$value);
								}
							}
							else{
								$newToken->setPropertyValue(new core_kernel_classes_Property($collection->get(0)->uriResource),$values);
							}
						}
				}
        		$keys[] = $code;
        	}
        	
        	$newToken->setPropertyValue($this->tokenVariableProp, serialize($keys)); 
        }
        $returnValue = $newToken;
        
        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001FA5 end

        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource token
     * @return boolean
     */
    public function delete( core_kernel_classes_Resource $token)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001FA8 begin
        
        if(!is_null($token)){
        	$token->removePropertyValues($this->tokenActivityProp);
        	$token->removePropertyValues($this->tokenActivityExecutionProp);
        	$token->removePropertyValues($this->tokenCurrentUserProp);
        	$token->removePropertyValues($this->tokenVariableProp);
        	
        	$returnValue = $token->delete();
        }
        
        // section 127-0-1-1-bf84135:12912b487a0:-8000:0000000000001FA8 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method move
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource connector
     * @param  Resource user
     * @param  Resource processExecution
     * @return array
     */
    public function move( core_kernel_classes_Resource $connector,  core_kernel_classes_Resource $user,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = array();

        // section 127-0-1-1-2013ff6:1292105c669:-8000:0000000000001FD4 begin
        
     	if(!is_null($connector) && !is_null($user) && !is_null($processExecution)){
        	
        	$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
     		
        	//get the activity around the connector
        	$previousActivities = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_PRECACTIVITIES));
        	
        	//get the tokens on the previous activity
        	$tokens = array();
        	foreach($previousActivities->getIterator() as $previousActivity){
        		$previousActivityExecution = $activityExecutionService->getExecution($previousActivity, $user, $processExecution);
        		$tokens = array_merge($tokens, $this->getTokens($previousActivityExecution));
        	}
        	
        	
        	$connectorNextActivityProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_NEXTACTIVITIES);
        	
        	if(count($tokens) > 0){
        		
        		$currentTokens = array();
	        	$type = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
	        	switch($type->uriResource){
	        		case INSTANCE_TYPEOFCONNECTORS_SEQUENCE:
	        		case INSTANCE_TYPEOFCONNECTORS_SPLIT:
	        			
	        			foreach($tokens as $token){
		        			
//	        				echo "Token ".$token->getLabel()."<br>";
//        					echo "Execution ".$token->getOnePropertyValue($this->tokenActivityExecutionProp)."<br>";
//	        				print "<pre>";
//        					print_r($this->getVariables($token));
//        					print "</pre><br>";
        					
	        				//create the token for next activity
		        			$newToken = $this->duplicate($token);
		        			
		        			//bind the next activity
		        			$nextActivity = $connector->getOnePropertyValue($connectorNextActivityProp);
		        			$newToken->setPropertyValue($this->tokenActivityProp, $nextActivity->uriResource);
		        			
		        			//set as current
		        			$currentTokens[] = $newToken;
		        			
//		        			echo "New token ".$newToken->getLabel()."<br>";
//        					echo "Execution ".$newToken->getOnePropertyValue($this->tokenActivityExecutionProp)."<br>";
//	        				print "<pre>";
//        					print_r($this->getVariables($newToken));
//        					print "</pre><br>";
		        			
		        			//delete the previous
		        			$this->delete($token);
	        			}
	        			
	        			break;
	        		case INSTANCE_TYPEOFCONNECTORS_PARALLEL:
	        			
	        			foreach($tokens as $token){
		        			$nextActivities = $connector->getPropertyValuesCollection($connectorNextActivityProp);
		        			foreach($nextActivities->getIterator() as $nextActivity){
		        				$newToken = $this->duplicate($token);
		        				$newToken->setPropertyValue($this->tokenActivityProp, $nextActivity->uriResource);
		        				$currentTokens[] = $newToken;
		        			}
		        			$this->delete($token);
	        			}
	        		
	        			break;	
	        		case INSTANCE_TYPEOFCONNECTORS_JOIN:
	        				
	        				//create the token for next activity
		        			$newToken = $this->merge($tokens);
		        			
		        			//bind the next activity
		        			$nextActivity = $connector->getOnePropertyValue($connectorNextActivityProp);
		        			$newToken->setPropertyValue($this->tokenActivityProp, $nextActivity->uriResource);
		        			
		        			//set as current
		        			$currentTokens[] = $newToken;
		        			
		        			//delete the previous
		        			foreach($tokens as $token){
		        				$this->delete($token);
		        			}
	        			break;
	        	}
	        	$this->setCurrents($processExecution, $currentTokens);
        	}
        }
        
        // section 127-0-1-1-2013ff6:1292105c669:-8000:0000000000001FD4 end

        return (array) $returnValue;
    }

} /* end of class wfEngine_models_classes_TokenService */

?>