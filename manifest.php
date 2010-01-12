<?php
	return array(
		'name' => 'Workflow Engine ',
		'description' => 'Workflow Engine  http://www.tao.lu',
		'additional' => array(
			'version' => '1.0',
			'author' => 'CRP Henri Tudor',
			'dependances' => array(),
			'install' => array( 
				'php' => dirname(__FILE__). '/install/install.php'
			),
		
			'classLoaderPackages' => array( 
				dirname(__FILE__).'/actions/',
				dirname(__FILE__).'/helpers/'

			 )	
			
		)
	);
?>