/**
 * This class is a grid column adapter used to format cells to fit specific needs
 * Adapt cell to switch to the previous activity

 * @see TaoGridClass
 * 
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * 
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 */

/**
 * The TaoGridPreviousActivityAdapter constructor
 */

function TaoGridActivityPreviousActivityAdapter(){}

TaoGridActivityPreviousActivityAdapter.preFormatter = function(grid, rowData, rowId, columnId)
{
	var returnValue = rowId;
	return returnValue;
}

TaoGridActivityPreviousActivityAdapter.formatter = function(cellvalue, options, rowObject)
{
	var returnValue = '<a href="#"><img src="'+root_url+'/wfEngine/views/img/previous.png"/></a>';
	return returnValue;
}

TaoGridActivityPreviousActivityAdapter.postCellFormat = function(grid, cell, rowId, columnId)
{
	var activityExecutionUri = rowId;
	$(cell).find('a').one('click', function(){
		wfApi.ActivityExecution.previous(activityExecutionUri, function(data){
			var rowData = grid.getRowData(rowId);
			refreshMonitoring([rowId]);
		}, function(){
			alert('unable to move the cursor to the previous activity '+activityExecutionUri);
		});
	});
}
