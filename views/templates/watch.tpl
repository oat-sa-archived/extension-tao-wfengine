<?php header( "Content-type: application/vnd.mozilla.xul+xml" ); ?>
<?xml version="1.0" ?>
<?xml-stylesheet href="chrome://global/skin/" type="text/css" ?>
<?xml-stylesheet href="CapiCalendar.css" type="text/css" ?>
<window xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
<tree id="calList" flex="1">
	<treecols>
		<treecol id="Item code" label="Item Code" flex="5"/>
		<treecol id="Input Code" label="Input Code" flex="5"/>
		<treecol id="Response" label="Response" flex="5"/>
		
	</treecols>
	<treechildren id="calData">
		
		<?php 
		foreach ($data as $itemName => $itemValue)
		{
			$treeValue = "";
			foreach ($itemValue["values"] as $value)
			{
				$treeValue .= $value." "; 	
			}
			
			$treeInputCode = "";
			
			if (isset($itemValue['inputCode']))
			{
				foreach ($itemValue["inputCode"] as $value)
				{
					$treeInputCode .= $value." ";  	
				}
			}
			
			$treeValue = str_replace('&', '&amp;', $treeValue);
			$treeValue = str_replace('"', '&quot;', $treeValue);	
			$treeValue = str_replace('\'', '&apos;', $treeValue);
			$treeValue = str_replace('<', '&lt;', $treeValue);
			$treeValue = str_replace('>', '&gt;', $treeValue);
			?>
			
			<?php if (!in_array(trim($itemName), $GLOBALS["watchexclude"])): ?>
			<treeitem>
				<treerow>
					<treecell label="<?php echo str_replace('inf_', '', $itemName);?>"/>
					<treecell label="<?php echo $treeInputCode;?>"/>
					<treecell label="<?php echo $treeValue;?>"/>
				</treerow>
			</treeitem>
			<?php else: echo $itemName . " excluded \n"; ?>
			
			<?php endif; ?>
		<?php
		}
		?>
		
	</treechildren>
</tree>
</window>