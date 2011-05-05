<?php
require_once dirname(__FILE__) . '/../includes/raw_start.php';

new wfEngine_scripts_HardifyWfEngine(array(
	'min'		=> 1,
	'required'	=> array(
		array('compile'),
		array('decompile')
	),
	'parameters' => array(
		array(
			'name' 			=> 'compile',
			'type' 			=> 'boolean',
			'shortcut'		=> 'c',
			'required'		=> true,
			'description'	=> 'Compile the workflow triple store to relational database'
		),
		array(
			'name' 			=> 'decompile',
			'type' 			=> 'boolean',
			'shortcut'		=> 'd',
			'required'		=> true,
			'description'	=> 'Get the data from the workflow relational database to the triple store (if previously compiled)'
		)
	)
));
?>