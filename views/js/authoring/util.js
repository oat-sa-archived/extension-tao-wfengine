processUtil = new Object();

processUtil.isset = function(object){

	if(typeof(object)=='undefined' || object===null){
		return false;
	}else{
		return true;
	}

}

CL = function(arg1, arg2){
	
	if(arg1){
		if(arg2){
			console.log(arg1, arg2);
		}else{
			console.log(arg1);
		}
	}
}

CD = function(object, desc){
	
	if(desc){
		console.log(desc+':');
	}
	console.dir(object);
	
}