/**
 * This class is a grid column adapter used to format cells to fit specific needs
 * Adapt activity variables content

 * @see TaoGridClass
 * 
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * 
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 */

/**
 * The TaoGridActivityVariablesAdapter constructor
 */

function TaoGridActivityVariablesAdapter(){}

TaoGridActivityVariablesAdapter.formatter = function(cellvalue, options, rowObject){

	var returnValue = '';

	returnValue = '<a href="#" disabled="false" tyle="cursor: pointer;">'
			+'<span class="ui-icon ui-icon-flag" style="float:left; cursor:pointer;"></span><span>'+__('Edit')+'</span>'
		+'</a>';
	
	return returnValue;
}

TaoGridActivityVariablesAdapter.postCellFormat = function(grid, cell, rowId, columnId)
{
	/**
	 * Instantiate the variables grid
	 */
	$(cell).find('a').click(function()
	{
		var processId = selectedProcessId;
		var activityId = rowId;
		
		//instantiate the dialog box
		$('<div id="activity-variables-popup"><table id="activity-variables-grid"></table></div>').dialog({
			'height'	: 500
			, 'width'	: 400
			, 'modal'	: true
			, 'close': function(event, ui) {
				console.log('destroy');
				//activityVariablesGrid.destroy();
				$('#activity-variables-grid').jqGrid('GridUnload');
				delete activityVariablesGrid;
				$('#activity-variables-popup').remove();
			}
		});
		
		$( "#activity-variables-popup" ).bind( "dialogclose", function(event, ui) {
			 console.log('close');
			});
		
		//the grid model
		var activityVariablesModel = model['http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesCurrentActivityExecutions']['subgrids']['variables']['subgrids'];
		//the current activities grid options
		 var activityVariablesOptions = {
			'title'  : __('Activity Variables')
		};
		//instantiate the grid widget
		var activityVariablesGrid = new TaoGridClass('#activity-variables-grid', activityVariablesModel, '', activityVariablesOptions);

		var variables = monitoringData[processId]['http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesCurrentActivityExecutions'][activityId]['variables'];
		activityVariablesGrid.add(variables);		
	});
}
