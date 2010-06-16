/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @subpackage views
 * 
 * This file provide functions to drive the workflow engine from a service
 * 
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @version 0.1
 */ 

var wfApi = {
	'context': window.top.document 
};

wfApi.forward = function(){
	wfApi.context.getElementById('next').click();
};

wfApi.back = function(){
	wfApi.context.getElementById('back').click();
};


wfApi.pause = function(){
	wfApi.context.getElementById('pause').click();
};