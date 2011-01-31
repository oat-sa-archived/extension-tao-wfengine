<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
return array(
	'name' => 'wfEngine ',
	'description' => 'Workflow Engine extension',
	'additional' => array(
		'version' => '2.0',
		'author' => 'CRP Henri Tudor',
		'dependances' => array('tao'),
		'models' => 'http://www.tao.lu/middleware/wfEngine.rdf',
		'install' => array( 
			'rdf' => dirname(__FILE__). '/models/ontology/wfengine.rdf'
		),
		'classLoaderPackages' => array( 
			dirname(__FILE__).'/actions/',
			dirname(__FILE__).'/models/classes/',
			dirname(__FILE__).'/helpers/'

		 )	
	)
);
?>