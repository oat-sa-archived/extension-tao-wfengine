<?php

error_reporting(E_ALL);

/**
 * @author Lionel Lecaque 
 * 	lionel.lecaque@tudor.lu
 *
 */
class SequentialSelector implements Selector {
		
	private $execution;
	/**
	 * @var ActivitiesListExecution
	 */
	private $list;
	/**
	 * @var ActivitiesListExecution
	 */
	private $remainning;

	/**
	 * @param $list
	 * @return void
	 */
	public function __construct(ActivitiesListExecution $execution ){
		$this->execution = $execution;
		$this->list = $execution->getRdfList();
		$this->remainning = $execution->getRemaining();
	 	if(!$execution->restored) {	
			foreach ($this->list->getCollection()->getIterator() as $activity) {
//				echo __FILE__.__LINE__;
	 			$this->remainning->add($activity);
	 		}
	 	}
	}


	

	/**
	 * @return boolean
	 */
	public function hasNext(){
		$returnValue = $this->remainning->getHead()->uriResource != RDF_NIL 
					&& $this->remainning->uri != RDF_NIL;
		if(!$returnValue){
			$this->execution->setFinished(true);
		}
		
		return $returnValue;
	}
	
	
	/**
	 * @return core_kernel_classes_Resource
	 */
	public function next(){
		$returnValue = $this->remainning->getHead();
		if($this->remainning->remove($returnValue)) 
		{			
			return $returnValue;
		}
		else {
			throw new common_Exception('Problem getting next element');
		}
	}
	

}
?>