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
		
		var dialogHtml = '<div id="activity-variables-popup" style="margin-top:5px;"> \
			<div style="height:30px;"> \
				<a href="#" class="activity-variable-action activity-variable-add"><img src="/tao/views/img/add.png"/> Add a variable</a> \
			</div> \
			<div style="height:405px;"> \
				<table id="activity-variables-grid"></table> \
			</div> \
			<div style="height:30px; margin-top:10px;"> \
				<a href="#" class="activity-variable-action activity-variable-add"><img src="/tao/views/img/add.png"/> Add a variable</a> \
			</div> \
		</div>';
		
		//instantiate the dialog box
		$(dialogHtml).dialog({
			'height'	: 500
			, 'width'	: 400
			//, 'modal'	: true
			, 'close': function(event, ui) {
				$('#activity-variables-grid').jqGrid('GridUnload');
				$('#activity-variables-popup').dialog('destroy').remove();
				delete activityVariablesGrid;
			}
			, editurl: "server.php"
			, viewrecords: true
		});
		
		//the grid model
		//var activityVariablesModel = model['http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesCurrentActivityExecutions']['subgrids']['variables']['subgrids'];
		var activityVariablesModel = [
			{'id' : 'code' , 'name' : 'code', type:'text', editable: true, edittype:'text'}
			, {'id' : 'value' , 'name' : 'value', 'weight':3, 'widget':'ActivityVariable',  type:'text', editable: true, edittype:'text' }
		];
		//the current activities grid options
		var activityVariablesGridOptions = {
			'title'  : __('Activity Variables')
		};
		//instantiate the grid widget
		var activityVariablesGrid = new TaoGridClass('#activity-variables-grid', activityVariablesModel, '', activityVariablesGridOptions);
		//display the data
		var rowData = grid.getRowData(rowId);
		activityVariablesGrid.add(rowData['variables']);
		
		//bind actions
		$('#activity-variables-popup').find('.activity-variable-add').click(function(){
			
			var codeSelectBox = 'label';
			
			activityVariablesGrid.add({'newRowId':{
				'code'		: 'label'
				, 'value'	: ['yes']
			}});
			console.log($(activityVariablesGrid.selector));
			//$(activityVariablesGrid.selector).jqGrid('editRow', 'newRowId', true, oneditfunc, succesfunc, url, extraparam, aftersavefunc,errorfunc, afterrestorefunc);
			//$('#activity-variables-grid').jqGrid('editRow', 'newRowId');
			//var cell = activityVariablesGrid.jqGrid.getCell('newRowId', 'value');
			//console.log(activityVariablesGrid.jqGrid.getInd('newRowId'));
			//TaoGridActivityVariableAdapter.edit(activityVariablesGrid, this, cell, 'value');
		});
	});
}