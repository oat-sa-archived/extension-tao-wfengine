<?php

require_once dirname(__FILE__).'/../../generis/common/inc.extension.php';	
require_once dirname(__FILE__).'/../includes/common.php';

class wfDebugger{
        
        protected $diplayPropertyLabels = true;
        protected $propertyLabels = array();
        protected $localNS = '';
        protected $order = 'resource';
        protected $unserialize = 0;
        protected $br = '<br/>';
        
        public function __construct($options = array()){
                core_control_FrontController::connect(SYS_USER_LOGIN, SYS_USER_PASS, DATABASE_NAME);
                $this->localNS = core_kernel_classes_Session::singleton()->getNameSpace();
                $this->unserialize = (isset($options['unserialize']))? (int)$options['unserialize']:0;
        }
        
        public function getPropertyLabels(){
                return $this->propertyLabels;
        }
        
        protected function data_dump($data, $sortProperty = null){
                if($data instanceof core_kernel_classes_Resource){

                        $propertyValues = array();
                        $epoch = '';
                        
                        foreach($data->getRdfTriples()->getIterator() as $triple){

                                $key = $triple->predicate;
                                
                                if(!is_null($sortProperty) && $sortProperty->uriResource == $key){
                                        $epoch = $triple->epoch;
                                }
                                
                                if($this->diplayPropertyLabels){
                                        if(!isset($this->propertyLabels[$key])){
                                                $property = new core_kernel_classes_Property($key);
                                                $this->propertyLabels[$key] = trim(strip_tags($property->getLabel()));
                                        }
                                        
                                        if(!empty($this->propertyLabels[$key])){
                                                $key = $this->propertyLabels[$key];
                                        }
                                }
                                
                                $value = $triple->object;
                                
                                if($this->unserialize && preg_match('/^a:[0-9]{1,2}:{/i', $value)){
                                        $unserializedValue = unserialize($value);
                                        if(!is_null($unserializedValue)){
                                                $desc = (is_array($unserializedValue))?'array '.count($unserializedValue).': '.  implode(', ', array_keys($unserializedValue)):'object';
                                                if($this->unserialize == 2){
                                                        $value = array("unserialized ({$desc})" => $unserializedValue);
                                                }else{
                                                        $value = array("unserialized ({$desc})" => $value);
                                                }
                                        }
                                }
                                        
                                if(isset($propertyValues[$key])){
                                        if(is_array($propertyValues[$key])){
                                                $propertyValues[$key][] = $value;
                                        }else{

                                                $propertyValues[$key] = array($propertyValues[$key], $value);
                                        }
                                }else{
                                        $propertyValues[$key] = $value;
                                }
                                
                        }

                        $returnValue = array(
                            'resource'  => "{$data->getLabel()} ({$data->uriResource})",
                            'properties' => $propertyValues
                        );
                        
                        if(!empty($epoch)){
                                $returnValue['epoch'] = $epoch;
                        }
                            
                        var_dump($returnValue);
                }else{
                        var_dump($data);
                }
        }

        public function getData($key){

                $returnValue = null;

                if(isset($_GET[$key])){
                        if(preg_match('/^i/i', $_GET[$key])){
                                $returnValue = new core_kernel_classes_Resource($this->localNS.'#'.$_GET[$key]);
                        }else{
                                $returnValue = new core_kernel_classes_Resource(urldecode($_GET[$key]));
                        }
                }

                return $returnValue;
        }

        public function debugProcessInstance(core_kernel_classes_Resource $processInstance){

               $apiModel = core_kernel_impl_ApiModelOO::singleton();

               if(!is_null($processInstance)){

                        echo 'process instance:'.$this->br;
                        $this->data_dump($processInstance);

                        $processInstances = $processInstance->getPropertyValuesCollection(new core_kernel_classes_Property(CURRENT_TOKEN));
                        echo "tokens ({$processInstances->count()}):".$this->br;
                        $sortedProcessInstances = array();
                        foreach($processInstances->getIterator() as $token){
                                $sortedProcessInstances[$token->uriResource] = $token;
                        }
                        krsort($sortedProcessInstances);
                        foreach($sortedProcessInstances as $token){
                                $this->data_dump($token);
                        }

                        $activityExecutions = $apiModel->getSubject(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION, $processInstance->uriResource);
                        echo "activity executions ({$activityExecutions->count()}):".$this->br;
                        $sortedActivityExecutions = array();
                        $timeSortingProperty = new core_kernel_classes_Property('http://www.tao.lu/middleware/taoqual.rdf#ContextRecovery');
                        
                        $i = 0;
                        foreach($activityExecutions->getIterator() as $activityExecution){
                                if(!is_null($timeSortingProperty)){
                                        $lastModifiedTime = null;
                                        try{
                                                $lastModifiedTime = $activityExecution->getLastModificationDate($timeSortingProperty);
                                        }catch(common_Exception $e){
                                                echo $e->getMessage().': '.$activityExecution->getLabel();
                                                echo $this->br;
                                                $sortedActivityExecutions[$i] = $activityExecution;$i++;
                                        }
                                        
                                        if(!is_null($lastModifiedTime)){
                                                $sortedActivityExecutions[$lastModifiedTime->format('U')] = $activityExecution;
                                        }
                                }else{
                                        $sortedActivityExecutions[$activityExecution->uriResource] = $activityExecution;
                                }
                        }
                        krsort($sortedActivityExecutions);
                        
                        
                        foreach($sortedActivityExecutions as $time => $activityExecution){
                                echo $this->br;
                                echo date('Y-m-d H:i:s', $time).":";
                                $this->data_dump($activityExecution);
                        }
                        
                }

        }

}


$options = array(
    'unserialize' => (isset($_GET['unserialize']))? intval($_GET['unserialize']):0 
);

$wfDebugger = new wfDebugger($options);
$processInstance = $wfDebugger->getData('processInstance');
$processDefinition = $wfDebugger->getData('processDefinition');
$activityExecution = $wfDebugger->getData('activityExecution');

if(!is_null($processInstance)){
        $wfDebugger->debugProcessInstance($processInstance);
}else{
        $processInstances = new core_kernel_classes_Class(CLASS_PROCESSINSTANCE);
        foreach($processInstances->getInstances() as $procExec){
                $wfDebugger->debugProcessInstance($procExec);
        }
        
}


echo "Property labels:";
var_dump($wfDebugger->getPropertyLabels());

?>