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

function WfRunner(activityExecutionUri, processUri, activityExecutionNonce) {
	this.activityExecutionUri = activityExecutionUri;
	this.processUri = processUri;
	this.nonce = activityExecutionNonce;
	
	this.services = [];
	
	this.processBrowserModule = window.location.href.replace(/^(.*\/)[^/]*/, "$1");
	
}

WfRunner.prototype.initService = function(serviceUri, style, url) {
	var serviceApi = new ServiceWfImpl(serviceUri, this);
	this.services.push(serviceApi);
	
	var $aFrame = $('<iframe class="toolframe" frameborder="0" style="'+style+'" src="'+this.processBrowserModule+'loading"></iframe>').appendTo('#tools');
	$aFrame.unbind('load').load(function(){
		$(this).attr('src', url);
		$(this).unbind('load');

		$(this).load(function() {
			// Auto adapt tool container regarding iframe heights
			var frame = this;
			var doc = frame.contentWindow || frame.contentDocument;

			if (doc.document) {
				doc = doc.document;
			}

			var oldHeight = $('#tools').height();
			var height = $(doc).height();
			$('#tools').height(height + oldHeight);
		});

		if (jQuery.browser.msie) {
			this.onreadystatechange = function(){	
				if(this.readyState == 'complete'){
						serviceApi.connect(this);	
					}
				};
			} else {		
				this.onload = function(){
					serviceApi.connect(this);	
				};
			}
		});

}

WfRunner.prototype.forward = function() {
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