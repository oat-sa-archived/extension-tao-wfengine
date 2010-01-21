<?php
/**
 * @author Lionel Lecaque 
 * lionel.lecaque@tudor.lu
 *
 */
interface Selector {
	
	/**
	 * @return boolean
	 */
	public function hasNext();
	
	/**
	 * @return core_kernel_classes_Resource
	 */
	public function next();
	

}
?>