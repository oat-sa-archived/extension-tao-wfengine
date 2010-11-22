/**
 * WF API
 * It provides a tool to manage a recoverable context.
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 * @requires jquery >= 1.4.0 {@link http://www.jquery.com}
 * 
 */

/**
 *  The RecoveryContext enables you to initialize, 
 *  send and retrieve a data structure (a context).
 *  It can be used to recover a context in case of crash.
 *  
 * @namespace wfApi
 * @class RecoveryContext
 */
function RecoveryContext (){
	
	//keep the current instance
	var _recoveryCtx = this;
	
	/**
	 * The registry store the contexts 
	 * @type {Object}
	 */
	this.registry = null;
	
	/**
	 * The parameters defining how and where to retrieve a context
	 * @type {Object}
	 */
	this.sourceService = {
			type:	'sync',										// (async | sync | manual)
			data:	null,										//if type is manual, contains the data in JSON, else it should be null
			url:	'/wfEngine/RecoveryContext/retrieve',		//the url where we retrieve the context
			params: {},	 										//the common parameters to send to the service
			method: 'post',										//sending method
			format: 'json'										//the response format, now ONLY JSON is supported
	};
	
	/**
	 * The parameters defining how and where to send a context
	 * @type {Object}
	 */
	this.destinationService = {
			type:	'async',									// (async | sync)
			url:	'/wfEngine/RecoveryContext/save',			//the url where we send the context
			params:  {},										//the common parameters to send to the service
			method: 'post',										//sending method
			format: 'json',										//the response format, now ONLY JSON is supported
			flush:  true										//clear the context registry once the context is saved
	};
	
	/**
	 * Initialize the service interface for the source service: 
	 * how and where we retrieve a context
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
			
					if(environment.url){
						if(/(\/[-a-z\d%_.~+]*)*(\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$/.test(environment.url)){	//test url
							this.sourceService.url = environment.url;		//set url
						}
					}
					//ADD parameters
					if($.isPlainObject(environment.params)){	
						for(key in environment.params){
							if($.inArray((typeof environment.params[key]).toLowerCase(), ['string', 'number', 'int', 'float', 'boolean']) > -1){
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
	 * Retrieve a context and populate the registry
	 */
	this.retrieveContext = function(){
			
			switch(this.sourceService.type){
			
			case 'manual':
				this.registry = this.sourceService.data;
				break;
			
			case 'sync':
				try{
					var response = $.parseJSON($.ajax({
						async	: false,
						url  	: this.sourceService.url,
						data 	: this.sourceService.params,
						type	: this.sourceService.method,
						dataType: this.sourceService.format
					}).responseText);
					
					if($.isPlainObject(response) || $.isArray(response)) {
						this.registry = response;
					}
					
				}
				catch(jsonException){ }
				break;
			
			case 'async':
					
				$.ajax({
					async		: false,
					url			: this.sourceService.url,
					data		: this.sourceService.params,
					type		: this.sourceService.method,
					dataType	: this.sourceService.format,
					success		: function(received){
						this.registry = received;
					}
				});
				break;
			}
	};
	
	/**
	 * Initialize the service interface forthe destination service: 
	 * how and where we send the contexts
	 *  
	 * @param {Object} environment
	 */
	this.initDestinationService = function(environment){
		
		if($.isPlainObject(environment)){
			
			if(environment.type){
				if($.inArray(environment.type, ['sync', 'async']) > -1){
					this.destinationService.type = environment.type;
				}
			}
			if(environment.url){
				if(/(\/[-a-z\d%_.~+]*)*(\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$/.test(environment.url)){	//test url
					this.destinationService.url = environment.url;		//set url
				}
			}
			//ADD parameters
			if($.isPlainObject(environment.params)){	
				for(key in environment.params){
					if($.inArray((typeof environment.params[key]).toLowerCase(), ['string', 'number', 'int', 'float', 'boolean']) > -1){
						this.destinationService.params[key] = environment.params[key]; 
					}
				}
			}
			if(environment.method){
				if(/^get|post$/i.test(environment.method)){
					this.destinationService.method = environment.method;
				}
			}
			if(environment.flush){
				this.destinationService.flush = (environment.flush === true);
			}
		}
	};
	
	/**
	 * Save the contexts by sending them to the destination service 
	 */
	this.saveContext = function(){
		
		var registryParams = this.destinationService.params;
		registryParams['context'] = new Object();
		for(key in this.registry){
			registryParams['context'][key] = this.registry[key];
		}
		
		$.ajax({
				async		: (this.destinationService.type == 'async'),
				url  		: this.destinationService.url,
				data 		: registryParams,
				type 		: this.destinationService.method,
				dataType	: this.destinationService.format,
				success		: function(data){
			 		if(data.saved){
			 			if(_recoveryCtx.destinationService.flush){
			 				_recoveryCtx.registry = new Object();	//clear it but don't set it to null, to prevent retrieving
			 			}
			 		}
		 		}
			});
	};
	
	/**
	 * Get a context defined by the key. 
	 * If not loaded, we retrieve it⋅
	 * 
	 * @param {String} key
	 * @returns {mixed} the context
	 */
	this.getContext = function(key){
		if(this.registry == null){
			this.retrieveContext();
		}
		if(this.registry != null){
			return (this.registry[key]) ? this.registry[key] : {};
		}
		return  {};
	};
	
	/**
	 * Create/edit a context
	 * 
	 * @param {String} key
	 * @param {Object} value
	 */
	this.setContext = function(key, value){
		if(this.registry == null){
			this.registry = new Object();
		}
		if(key != ''){
			this.registry[key] = value;
		}
	};
}