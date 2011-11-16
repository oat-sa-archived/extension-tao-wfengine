/**
 * This class is a grid column adapter used to format cells to fit specific needs
 * Adapt activity variable content

 * @see TaoGridClass
 * 
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * 
 * @author Alfonsi Cédric, <taosupport@tudor.lu>
 */

/**
 * The TaoGridActivityVariableAdapter constructor
 */

function TaoGridActivityVariableAdapter(){}

TaoGridActivityVariableAdapter.formatter = function(cellvalue, options, rowObject){

	var returnValue = '';

	for(var i in cellvalue){
		returnValue += '<span class="activity-variable-value">'+cellvalue[i]+'</span>';
	}
	
	return returnValue;
}

TaoGridActivityVariableAdapter.postCellFormat = function(grid, cell, rowId, columnId)
{
	$(cell).bind('dblclick', function(){
		TaoGridActivityVariableAdapter.edit(grid, this, rowId, columnId);
	});
}

TaoGridActivityVariableAdapter.addEditRow = function(cell, value)
{
	var editCellValueHtml = '<span class="activity-variable-value"> \
			<input type="text" value="'+value+'" /> \
			<img class="activity-variable-delete" src="/tao/views/img/delete.png"/> \
	</span>';
	$(cell).find('.activity-variable-edit-container').append(editCellValueHtml);
}

TaoGridActivityVariableAdapter.edit = function(grid, cell, rowId, columnId)
{
	var editHtml = '<div class="activity-variable-edit-container"></div>'
		+'<div class="activity-variable-actions"> \
			<a href="#" class="activity-variable-add"><img src="/tao/views/img/add.png"/> Add</a> \
			<a href="#" class="activity-variable-save"><img src="/tao/views/img/save.png"/> Save</a> \
			<a href="#" class="activity-variable-cancel"><img src="/tao/views/img/revert.png"/> Cancel</a> \
		</div>';
	
	//get the variables values
	var $cellValues = $(cell).find('span');
	//empty the block
	$(cell).empty().html(editHtml);
	
	//foreach values add an edit box
	$cellValues.each(function(){	
		var cellValue = $(this).html();
		TaoGridActivityVariableAdapter.addEditRow(cell, cellValue);
	});

	//bind the delete action
	$(cell).find('.activity-variable-delete').click(function(){
		$(this).parent().remove()
	});
	//bind the add action
	$(cell).find('.activity-variable-add').click(function(){
		TaoGridActivityVariableAdapter.addEditRow(cell, '');
	});
	//bind the add action
	$(cell).find('.activity-variable-save').click(function(){
		//
	});
	//bind the add action
	$(cell).find('.activity-variable-cancel').click(function(){
		$(cell).empty().append($cellValues);
	});
}