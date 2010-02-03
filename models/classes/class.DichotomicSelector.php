<?php

error_reporting(E_ALL);

/**
 * @author Lionel Lecaque & Eric Montecalvo
 *  lionel.lecaque@tudor.lu
 *  eric.montecalvo@tudor.lu
 *
 */
class DichotomicSelector implements Selector {

	/**
	 * @const YES constant value of the code of answer yes to make dichotomy
	 */
	const YES = '01';
	/**
	 * @const NO constant value of the code of answer no to make dichotomy
	 */
	const NO = '02';

	/**
	 * @var ActivitiesListExecution
	 */
	private $list;
	/**
	 * @var ActivitiesListExecution
	 */
	private $remainning;
	/**
	 * @var array
	 */
	private $responses;
	/**
	 * @var unknown_type
	 */
	private $currentActivty;

	private $execution;

	/**
	 * @param $list
	 * @param $prevAnswer
	 * @param $currentActivty
	 * @return void
	 */
	public function __construct(ActivitiesListExecution $list,  $prevAnswers , $currentActivty){
		$this->execution = $list;
		//		$this->list = $this->execution->getRdfList();
		//		$this->remainning = $this->execution->getRemaining();
//		echo __FILE__.__LINE__;
//		var_dump($prevAnswers);
		$this->responses = $prevAnswers;

		$this->currentActivty = $currentActivty;

		//		if(!$list->restored) {
		//			foreach ($this->list->getCollection()->getIterator() as $activity) {
		//				$this->remainning->add($activity);
		//			}
		//		}


	}


	/**
	 * @return boolean
	 */
	public function hasNext(){
		$logger = new common_Logger('WfEngine Process Execution Dicho', Logger::debug_level);

		$tab = array_values($this->responses);
		if(	empty($tab)	|| $tab[0]== DichotomicSelector::NO
		|| $tab[count($tab)-1]==DichotomicSelector::YES) {
			$this->execution->setFinished(true);
			$logger->debug('no more element',__FILE__,__LINE__);
			return false;
		}
		if(count($tab)==1 && $tab[0]!= null) {
			$logger->debug('no more element',__FILE__,__LINE__);
			$this->execution->setFinished(true);
			return false ;
		}

		$n = count($tab) - 1;
		for($i=0;$i<$n;$i++) {
			if($tab[$i]==DichotomicSelector::YES && $tab[$i+1]==DichotomicSelector::NO){
				$logger->debug('no more element',__FILE__,__LINE__);
				$this->execution->setFinished(true);
				return false;
			}
		}
		$logger->debug('elemenet left to handle',__FILE__,__LINE__);
		return true;

			
	}


	/**
	 * @return core_kernel_classes_Resource
	 */
	public function next(){
		$logger = new common_Logger('WfEngine Process Execution Dicho', Logger::debug_level);
		$logger->info('Dico  next :',__FILE__,__LINE__);
		$this->execution->setUp(true);


		$returnValue = new core_kernel_classes_Resource($this->dicho());


		return $returnValue;

	}

	/**
	 * @return uri
	 */


	public function dicho(){

		$map = array_keys($this->responses);
		$tab = array_values($this->responses);



		$n = count($tab);
		$maxIndex = $n - 1;
		$maxYesIndex = null;
		$minNoIndex = null;

		for($i=0;$i<=$maxIndex;$i++){
			if($tab[$i]==DichotomicSelector::YES){
				$maxYesIndex=$i;
			}
			else if($tab[$i]==DichotomicSelector::NO && $minNoIndex == null){
				$minNoIndex = $i;
			}
		}
		$leftIndexBorder = -1;
		if($maxYesIndex !== null) {
			$leftIndexBorder = $maxYesIndex;
		}
		$rightIndexBorder = $n;
		if($minNoIndex > $maxYesIndex){
			$rightIndexBorder = $minNoIndex;
		}
		$gap = $rightIndexBorder - $leftIndexBorder - 1;

		if($maxYesIndex === null && $minNoIndex === null){
				
			if($gap%2)  {
				return $map[ ceil($gap/2) - 1 ];
			}
			else {
				return $map[ floor($gap/2) - rand(0, 1) ];
			}
		}


		if($gap==2) {
			return $map[ $leftIndexBorder + 1 + rand(0,1) ];
		}
		else if($gap%2){
			return $map[ $leftIndexBorder + 1 + floor($gap/2) ];
		}
		else {
			return $map[ $leftIndexBorder + (int)$gap/2 + rand(0,1) ];
		}
	}
}
?>