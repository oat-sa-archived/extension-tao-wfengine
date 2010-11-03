/**
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @requires jquery >= 1.4.0 {@link http://www.jquery.com}
 * 
 */

/**
 *  
 * @namespace wfApi
 * @class RecoveryContext
 */
function RecoveryContext (){
	
	//keep the current instance
	var _recoveryCtx = this;
	
	/**
	 * @type {Object}
	 */
	this.registry = null;
	
	/**
	 * @type {Object}
	 */
	this.sourceService = {
			type:	'sync',										// (async | sync | manual)
			data:	null,										//if type is manual, contains the data in JSON, else it should be null
			url:	'/wfEngine/Context/retrieve',				//the url where we retrieve the context
			params: {},	 										//the common parameters to send to the service
			method: 'post',										//sending method
			format: 'json'										//the response format, now ONLY JSON is supported
	};
	
	/**
	 * @type {Object}
	 */
	this.destinationService = {
			url:	'/wfEngine/Context/save',					//the url where we send the context
			params:  {},										//the common parameters to send to the service
			method: 'post',										//sending method
			format: 'json',										//the response format, now ONLY JSON is supported
			flush:  true										//clear the context registry once the context is saved
	};
	
	/**
	 * Initialize the service interface for the source service: 
	 *  
	 * @param {Object} environment
	 */
	this.initSourceService = function(environment){
		
		//define the source service
		if($.isPlainObject(environment)){
			
			if($.inArray(environment.type, ['manual','sync', 'async']) > -1){
				
				this.sourceService.type = environment.type;
				
				//manual behaviour
				if(this.sourceService.type == 'manual' && $.isPlainObject(environment.data)){
					this.sourceService.data = environment.data;
				}
				else{ 	//remote behaviour
			
					if(source.url){
						if(/(\/[-a-z\d%_.~+]*)*(\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$/.test(environment.url)){	//test url
							this.sourceService.url = environment.url;		//set url
						}
					}
					//ADD parameters
					if($.isPlainObject(environment.params)){	
						for(key in environment.params){
							if(isScalar(environment.params[key])){	
								this.sourceService.params[key] = environment.params[key]; 
							}
						}
					}
					if(environment.method){
						if(/^get|post$/i.test(environment.method)){
							this.sourceService.method = environment.method;
						}
					}
				}
			}
		}
	};
	
	/**
	 * 
	 */
	this.retrieveContext = function(){
		
		switch(this.sourceService.type){
		
		//we load it manually by calling directly the method with the data
		case 'manual':
			this.registry = this.sourceService.data;
			break;
		
		case 'sync':
			this.registry = $.parseJSON($.ajax({
				async		: false,
				url  		: this.sourceService.url,
				data 		: this.sourceService.params,
				type 		: this.sourceService.method
			}).responseText);
			break;
		
		case 'async':
			$.ajax({
				async		: false,
				url  		: this.sourceService.url,
				data 		: this.sourceService.params,
				type 		: this.sourceService.method,
				success		: function(received){
					this.registry = received;
				}
			});
			break;
		}
	};
	
	/**
	 * Initialize the service interface forthe destination service:  
	 *  
	 * @param {Object} environment
	 */
	this.initDestinationService = function(environment){
		if($.isPlainObject(environment)){
			if(environment.url){
				if(/(\/[-a-z\d%_.~+]*)*(\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$/.test(environment.url)){	//test url
					this.destinationService.url = environment.url;		//set url
				}
			}
			//ADD parameters
			if($.isPlainObject(environment.params)){	
				for(key in environment.params){
					if(isScalar(environment.params[key])){	
						this.destinationService.params[key] = environment.params[key]; 
					}
				}
			}
			if(environment.method){
				if(/^get|post$/i.test(environment.method)){
					this.destinationService.method = environment.method;
				}
			}
		}
	};
	
	/**
	 * 
	 */
	this.saveContext = function(){
		
		var parameters = this.destinationService.params;
		parameters['context'] = this.registry;
		
		 $.ajax({
				async		: (this.destinationService.type == 'async'),
				url  		: this.destinationService.url,
				data 		: parameters,
				type 		: this.destinationService.method,
				success		: function(data){
			 		if(data.saved){
			 			if(_recoveryCtx.destinationService.flush){
			 				this.registry = new Object();	//clear it but don't set it to null, to prevent retrieving
			 			}
			 		}
		 		}
			});
	};
	
	/**
	 * @param {String} key
	 */
	this.getContext = function(key){
		if(this.registry == null){
			this.retrieveContext();
		}
		return (this.registry[key]) ? this.registry[key] : false;
	};
	
	/**
	 * @param {String} key
	 * @param {Object} value
	 */
	this.setContext = function(key, value){
		this.registry[key] = value;
	};
}