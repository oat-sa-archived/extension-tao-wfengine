<?php

error_reporting(-1);
/**
 * @author Lionel Lecaque
 * 	lionel.lecaque@tudor.lu
 *
 */
class RandomSelector implements Selector {

	/**
	 * @var core_kernel_classes_RdfList
	 */
	private $list;
	/**
	 * @var core_kernel_classes_RdfList
	 */
	private $remainning;

	private $execution;

	private $remainingArray;

	private $head;

	private $isSelectorList;


	/**

	* @param $list
	* @param $nbItemToPick
	* @return void
	*/
	public function __construct(ActivitiesListExecution $list, $nbItemToPick = -1 , $finishedActivity = null){

		$this->execution = $list;
		$this->list = $list->getRdfList();
		$this->remainning = $this->execution->getRemaining();

		$this->head = $this->list->getHead();
		$logger = new common_Logger('WFEngine Process Execution Random', Logger::debug_level);

		//List contains activity list
		if ($this->isSelectorList()) {
			$logger->debug('Random Selector on a List :',__FILE__,__LINE__);
			
			if($this->execution->getRemainingArray() == false){
				$this->remainingArray = $this->list->getArray();
				shuffle($this->execution->getRemainingArray());

				$this->execution->setRemainingArray(serialize($this->remainingArray));
			}
			else {
				$this->remainingArray = unserialize($this->execution->getRemainingArray());
				array_unshift($this->remainingArray,array_pop($this->remainingArray));
				$logger->debug('See if items is not already finised if so find a new one',__FILE__,__LINE__);
//				echo __FILE__.__LINE__;var_dump($this->remainingArray);
//				echo __FILE__.__LINE__;var_dump($finishedActivity);
				if(!empty($this->remainingArray)){
					while(in_array($this->remainingArray[0],$finishedActivity)) {
						$logger->debug('removed list already finished : ' . $this->remainingArray[0] ,__FILE__,__LINE__);
						array_shift($this->remainingArray);
					}
				}
				else{
					trigger_error('no more remaining activity finished',E_NOTICE);
				}
				$this->execution->setRemainingArray(serialize($this->remainingArray));
			}
			
			
			
			
			
			
//			$count = sizeOf($this->remainingArray);
//			$r = rand(1,$count-1);
//			$val1 = $this->remainingArray[0];
//			$val2 = $this->remainingArray[$r];
//			$logger->debug('See if items is not already finised if so find a new one',__FILE__,__LINE__);
//			$logger->debug('random pick : ' . $val2 ,__FILE__,__LINE__);
//			while(in_array($val2,$finishedActivity)){
//				$r = rand(1,$count-1);
//				$val2 = $this->remainingArray[$r];
//				$logger->debug('new random pick : ' . $val2 ,__FILE__,__LINE__);
//			}
//			$logger->debug('new one found swatt with first',__FILE__,__LINE__);
//			$this->remainingArray[0] = $val2;
//			$this->remainingArray[$r] = $val1;
//			echo __FILE__.__LINE__;var_dump($this->remainingArray,$finishedActivity);
				
				
			//			echo __FILE__.__LINE__;var_dump(array_diff($this->remainingArray,$finishedActivity));

		}
		//List contains items
		else{
			if(!$list->restored){
				$logger->debug('Random Selector on a item :',__FILE__,__LINE__);
				$this->remainning = $this->execution->getRemaining();
				$temp = $this->list->shuffle($nbItemToPick);

				$this->remainning->setHead($temp->getHead());
				$this->remainning->setTail($temp->getTail());
			}
		}



	}



	/**
	 * @return boolean
	 */
	private function isSelectorList(){
		if($this->isSelectorList == null){
			$typeProp = new core_kernel_classes_Property(RDF_TYPE);
			$this->isSelectorList = $this->head->getUniquePropertyValue($typeProp)->uriResource == CLASS_ACTIVITIES_LIST;
		}
		return $this->isSelectorList;
	}


	/**
	 * @return boolean
	 */
	public function hasNext(){

		$logger = new common_Logger('WFEngine Process Execution Random', Logger::debug_level);

		if ($this->isSelectorList()) {

			$returnValue = !empty($this->remainingArray)
			&& $this->remainingArray[0]->uriResource != RDF_NIL;

		}
		else {
			$returnValue = $this->remainning->uriResource != RDF_NIL
			&& $this->remainning->getHead()->uriResource != RDF_NIL;
		}


		if(!$returnValue){
			$logger->debug('LIST IS FINISHED :',__FILE__,__LINE__);
			$this->execution->setFinished($true);
			
		}
		return $returnValue;


	}


	/**
	 * @return core_kernel_classes_Resource
	 */
	public function next(){
		$logger = new common_Logger('WFEngine Process Execution Random', Logger::debug_level);
		$logger->info('Random  next :',__FILE__,__LINE__);

		if ($this->isSelectorList()) {
				
			$returnValue = new core_kernel_classes_Resource($this->remainingArray[0]);
				
			$this->execution->setUp(false);
			$logger->info('Next Item is an activity List : '  ,__FILE__,__LINE__);
		}
		else {
			$returnValue = $this->remainning->getHead();
			$this->execution->setUp(true);
			$logger->debug('Removing uri' . $returnValue->uriResource,__FILE__,__LINE__);
			$logger->debug('Removing name' . $returnValue->getLabel(),__FILE__,__LINE__);
			$this->remainning->remove($returnValue);
		}
			

		if($returnValue != null )
		{
			$logger->debug('Return uri : ' . $returnValue->uriResource,__FILE__,__LINE__);
			$logger->debug('Return name : ' . $returnValue->getLabel(),__FILE__,__LINE__);
			return $returnValue;
		}
		else {
			var_dump($this->remainning,$this->remainingArray);
			throw new common_Exception('Problem getting next element');
		}
	}


}
?>