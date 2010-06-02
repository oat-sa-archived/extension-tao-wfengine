<?php
	return array(
		'name' => 'Workflow Engine ',
		'description' => 'Workflow Engine  http://www.tao.lu',
		'additional' => array(
			'version' => '1.2',
			'author' => 'CRP Henri Tudor',
			'dependances' => array(),
			'install' => array( 
				'php' => dirname(__FILE__). '/install/install.php'
			),
			'model' => array(
				'http://www.tao.lu/Ontologies/TAODelivery.rdf',
				'http://www.tao.lu/Ontologies/TAOTest.rdf',
				'http://www.tao.lu/middleware/hyperclass.rdf',
				'http://www.tao.lu/middleware/taoqual.rdf',
				'http://www.tao.lu/middleware/Rules.rdf',
				'http://www.tao.lu/middleware/Interview.rdf',
				'http://www.tao.lu/middleware/review.rdf'
			),
		
			'classLoaderPackages' => array( 
				dirname(__FILE__).'/actions/',
				dirname(__FILE__).'/models/classes/',
				dirname(__FILE__).'/helpers/'

			 )	
			
		)
	);
?>