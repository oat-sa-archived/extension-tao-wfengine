<div id="interactiveService-form">
	
	<?=get_data("formInteractionService")?>
	<input type="button" name="submit-interactiveService" id="submit-interactiveService" value="save"/>
</div>

<script type="text/javascript">

$(function(){
	//get the initial selected value, if exists: 
	var initalSelectedValue = $("select[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION)?>]").val();
	
	// alert($("select[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION)?>]").html());
	$("select[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION)?>]").change(function(e){
		if(confirm(__("Sure?"))){
			
			$("#<?=get_data("formId")?> :INPUT :gt(3)").attr("disabled","disabled");
			$("select[id=<?=tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION)?>]").removeAttr("disabled");
			$("#<?=get_data("formId")?>").append("<p>"+__('reloading form...')+"</p>");
			
			//send the form
			$.ajax({
				url: authoringControllerPath+'saveCallOfService',
				type: "POST",
				data: $("#<?=get_data("formId")?>").serialize(),
				dataType: 'json',
				success: function(response){
					if(response.saved){
						//call ajax function again to get the new form
						ActivityTreeClass.selectTreeNode($("#callOfServiceUri").val());
					}else{
						$("#interactiveService-form").html("save failed:" + response);//debug
					}
				}
			});
		}else{
			//reset the option:
			$("#<?=get_data("formId")?> option[value="+initalSelectedValue+"]").attr("selected","selected");
		}
		
	});
	
	$("#submit-interactiveService").click(function(){
		$.ajax({
			url: authoringControllerPath+'saveCallOfService',
			type: "POST",
			data: $("#<?=get_data("formId")?>").serialize(),
			dataType: 'json',
			success: function(response){
				if(response.saved){
					var selectedNode = $("#callOfServiceUri").val();
					$("#interactiveService-form").html(__("interactive service saved"));
					refreshActivityTree();
					ActivityTreeClass.selectTreeNode(selectedNode);
				}else{
					$("#interactiveService-form").html("interactive service save failed:" + response);//debug
				}
			}
		});
	});
	
	//init switches:
	$(":input").each(function(i){
		var startIndex = $(this).attr('id').indexOf('_choice_0');
		if(startIndex>0){
			var clazz = $(this).attr('id').substring(0,startIndex);
			initParameterSwitch(clazz);
			
		}
		
	});
});

function initParameterSwitch(clazz){
	
	switchParameterType(clazz);
	$("input:radio[name="+clazz+"_choice]").change(function(){switchParameterType(clazz);});
	$("#"+clazz+"_var").change(function(){switchParameterType(clazz);});
}

function switchParameterType(clazz){
	
	var value = $("input:radio[name="+clazz+"_choice]:checked").val();
	
	if(value == 'constant'){
		enable($("[id="+clazz+"_constant]"));
		disable($("[id="+clazz+"_var]"));
		// console.log(clazz+"_var");
		// console.log($("input[name="+clazz+"_var]"));
	}else if(value == 'processvariable'){
		disable($("[id="+clazz+"_constant]"));
		enable($("[id="+clazz+"_var]"));
		// console.log('var');
	}else{
		disable($("[id="+clazz+"_constant]"));
		disable($("[id="+clazz+"_var]"));
		// console.log('oth');
	}
}

function disable(object){
	object.parent().attr("disabled","disabled");
	object.parent().hide();
	// console.log(object.attr('id'));
}

function enable(object){
	object.parent().removeAttr("disabled");
	object.parent().show();
}

</script>
