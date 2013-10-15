/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
var autoResizeId;

function autoResize(frame, frequence) {
	
	var $frame = $(frame);
	autoResizeId = setInterval(function() {
		$frame.height($frame.contents().height());
	}, frequence);
}

function WfRunner(activityExecutionUri, processUri, activityExecutionNonce) {
	this.activityExecutionUri = activityExecutionUri;
	this.processUri = processUri;
	this.nonce = activityExecutionNonce;
	
	this.services = [];
	
	this.processBrowserModule = window.location.href.replace(/^(.*\/)[^/]*/, "$1");
}

WfRunner.prototype.initService = function(serviceApi, style) {
	var self = this;
        this.services.push(serviceApi);
	
	serviceApi.onFinish(function() {
            return self.forward()
        });
	
	var $aFrame = $('<iframe class="toolframe" frameborder="0" style="" scrolling="no" src="'+this.processBrowserModule+'loading"></iframe>').appendTo('#tools');
	$aFrame.unbind('load').load(function(){
		$(this).attr('src', serviceApi.getCallUrl());
		$(this).unbind('load');

		if (jQuery.browser.msie) {
			this.onreadystatechange = function(){	
				if(this.readyState == 'complete'){
						serviceApi.connect(this);
						autoResize(this, 10);
					}
				};
			} else {		
				this.onload = function(){
					serviceApi.connect(this);	
					autoResize(this, 10);
				};
			}
		});

}

WfRunner.prototype.forward = function() {
	clearInterval(autoResizeId);
	
	$("#navigation").hide();
	var url = this.processBrowserModule + 'next'
		+ '?processUri=' + encodeURIComponent(this.processUri)
		+ '&activityUri=' + encodeURIComponent(this.activityExecutionUri)
		+ '&nc=' + encodeURIComponent(this.nonce)
	this.goToPage(url);
	$(this).unbind('click');
	$("#back").unbind('click');
}

WfRunner.prototype.backward = function() {
	$("#navigation").hide();
	var url = this.processBrowserModule + 'back'
		+ '?processUri=' + encodeURIComponent(this.processUri)
		+ '&activityUri=' + encodeURIComponent(this.activityExecutionUri)
		+ '&nc=' + encodeURIComponent(this.nonce)
	this.goToPage(url);
	$(this).unbind('click');
	$("#next").unbind('click');
}

WfRunner.prototype.goToPage = function(page_str){
	$("#loader").css('display', 'block');
	$("#tools").empty();
	window.location.href = page_str;
}