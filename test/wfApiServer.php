<?php

switch($_POST['action']){

	case 'get':
		(isset($_POST['token'])) ? ($_POST['token'] == '7114e56cb3b9423314a425500afb41fc56183000') ? $saved = true : $saved = false : $saved = false;
		$context =array('myContext' => 
						array(
							'integer' =>	12,
							'obj'	=> array( 'arr' => array(1, 2) )
						)
					);
		echo json_encode($context);
		break;
		
	case 'set':
		(isset($_POST['token'])) ? ($_POST['token'] == '7114e56cb3b9423314a425500afb41fc56183000') ? $saved = true : $saved = false : $saved = false;
		
		
		$saved = $saved && (isset($_POST['context']['myContext']['obj']['arr']));
		echo json_encode(array('saved' => $saved));
		break;
}
?>