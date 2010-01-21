<?php

error_reporting(E_ALL);

/**
 * WorkFlowEngine - class.ViewTable.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatic generated with ArgoUML 0.24 on 11.08.2008, 09:28:22
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--31--4660acca:119ecd38e96:-8000:00000000000008B4-includes begin
// section 10-13-1--31--4660acca:119ecd38e96:-8000:00000000000008B4-includes end

/* user defined constants */
// section 10-13-1--31--4660acca:119ecd38e96:-8000:00000000000008B4-constants begin
// section 10-13-1--31--4660acca:119ecd38e96:-8000:00000000000008B4-constants end

/**
 * Short description of class ViewTable
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class ViewTable
{
    // --- ATTRIBUTES ---

    /**
     * Short description of attribute title
     *
     * @access public
     * @var string
     */
    public $title = '';

    /**
     * Short description of attribute arrayObjects
     *
     * @access public
     * @var array
     */
    public $arrayObjects = array();

    /**
     * Short description of attribute orderedBy
     *
     * @access public
     * @var int
     */
    public $orderedBy = 0;

    /**
     * Short description of attribute ordered
     *
     * @access public
     * @var string
     */
    public $ordered = '';

    /**
     * Short description of attribute displayColumnTitle
     *
     * @access public
     * @var boolean
     */
    public $displayColumnTitle = true;

    /**
     * Short description of attribute displayValuesSettings
     *
     * @access public
     * @var array
     */
    public $displayValuesSettings = array();

    /**
     * Short description of attribute id
     *
     * @access public
     * @var int
     */
    public $id = 0;

    /**
     * Short description of attribute ajax
     *
     * @access public
     * @var boolean
     */
    public $ajax = true;

    // --- OPERATIONS ---

    /**
     * Short description of method renderHtml
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param int
     * @param int
     * @param int
     * @return string
     */
    public function renderHtml($x = 0, $y = 0, $width = 0)
    {
        $returnValue = (string) '';

        // section 10-13-1--31--4660acca:119ecd38e96:-8000:00000000000008D6 begin
		
		if ($this->ajax)
		{$returnValue .= "<span style=\"display:none;\" id=".$this->id.">";}
		$returnValue .= "<div style=background-color:white><table >";
//<th colspan=\"".sizeOf($this->displayValuesSettings)."\">".$this->title."</th>
		if (sizeOf($this->displayValuesSettings)>0)
		{
			if ($this->displayColumnTitle)
			{
			$returnValue .= "
			<tr class=lightBlue>";
			foreach ($this->displayValuesSettings as $varName => $varSettings)
				{
					$returnValue .= "<td class=\"lightBlue\">".$varName."&nbsp;&nbsp;<a href=\"index.php?do=order&param1=".$this->id."&param2=".$varName."&param3=DESC\"><img border=\"0\" src=\"./view/pics/up.png\" width=\"12\" height=\"13\"/></a><a href=\"index.php?do=order&param1=".$this->id."&param2=".$varName."&param3=ASC\"><img border=\"0\" src=\"./view/pics/down.png\" width=\"12\" height=\"13\"/></a></td>";
				}
			$returnValue .= "</tr>";
			}
			error_reporting("^E_NOTICE");
			foreach ($this->arrayObjects as $key=>$object)
				{
					
					if ($key%2 == 0) $class="odd"; else $class="even";
					$returnValue.='
					<tr class=\""'.$class.'"\">';
					foreach ($this->displayValuesSettings as $varName => $varSettings)
						{
							
							if ((isset($object->$varName)) and (is_array($object->$varName)))
							{
								$returnValue.="<td>";
								foreach ($object->$varName as $subObject)
								{
									$cellValue = $subObject->label;
									$returnValue.= $this->getCellHtmlContent($cellValue,$varSettings,$subObject->uri,$object->uri)."<br />";
									
								}
								$returnValue.="</td>";
							}
							else
							{
							/*Define the content of the cell*/
							if (isset($object->$varName)) 
								{
									if (is_object($object->$varName)) 
										{ $cellValue = $object->$varName->label;$param1= $object->$varName->uri;}
									else 
										{$cellValue = $object->$varName;$param1= $object->uri;}
								}	
								else 
									{$cellValue = $varSettings["value"] ;$param1= $object->uri;}

							/*apply settings ($varSettings) to the content*/
							$returnValue.= "<td>".$this->getCellHtmlContent($cellValue,$varSettings,$param1)."</td>";
							}
							
							
							
						}
					$returnValue.="</tr>";
					
				}
			
			
			$returnValue .="</table></div>";
			if ($this->ajax)
			{
			$height= 50 * sizeof($this->arrayObjects);
			$returnValue .='<script type="text/javascript">
			var win'.$this->id.' = dhtmlwindow.open("win'.$this->id.'", "div", "'.$this->id.'", "'.$this->title.'", "width='.$width.'px,height='.$height.'px,left='.$x.'px,top='.$y.'px,resize=1,scrolling=1");
			</script>
			';
			
			/*$returnValue .="
			</span>
			<script type=\"text/javascript\">CreateDropdownWindow('".str_replace(" ","&nbsp;",$this->title)."', ".($width).", true, '".$this->id."',".$x.",".$y.");</script>";*/
			}
						
		}
		
        // section 10-13-1--31--4660acca:119ecd38e96:-8000:00000000000008D6 end

        return (string) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param array
     * @param string
     * @param int
     * @return void
     */
    public function __construct($arrayObjects, $title, $id)
    {
        // section 10-13-1--31--4660acca:119ecd38e96:-8000:00000000000008E3 begin
		$this->id=$id;
		$this->title = $title;
		$this->arrayObjects = $arrayObjects;
        // section 10-13-1--31--4660acca:119ecd38e96:-8000:00000000000008E3 end
    }

    /**
     * Short description of method getCellHtmlContent
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param string
     * @param string
     * @param string
     * @param string
     * @return string
     */
    private function getCellHtmlContent($cellValue, $cellSettings, $param1 = "", $param2 = "")
    {
        $returnValue = (string) '';

        // section 10-13-1--31-3f408528:11a0042e513:-8000:00000000000008AB begin
		switch ($cellSettings["linkType"])
							{
									
									case "xmlHttpRequest": {

										$WindowTitle = trim(html_entity_decode(strip_tags($cellValue)));
										$WindowId = "win".rand(0,65535);
										
										$returnValue.="<a href=\"javascript:getUrl('index.php?do=".$cellSettings["do"]."&param1=".urlencode(urlencode($param1))."&param2=".urlencode(urlencode($param2))."','".$WindowTitle."',360,300,910,'".$WindowId."')\">".$cellValue."</a>";break;
									}
									case "redirect": {
										$returnValue.="<a target=_BLANK href='".strip_tags($cellValue)."'>Launch</a>";break;
									}
									case "openInNewWindow": {
										$returnValue.="<a target=_blank href=index.php?do=".$cellSettings["do"]."&param1=".urlencode(urlencode($param1))."&param2=".urlencode(urlencode($param2)).">".$cellValue."</a>";break;
									}
									case "none" : {$returnValue.="".$cellValue."";break;}
									default:
									{
										$returnValue.="<a href=index.php?do=".$cellSettings["do"]."&param1=".urlencode(urlencode($param1))."&param2=".urlencode(urlencode($param2)).">".$cellValue."</a>";		break;
									}
							}
        // section 10-13-1--31-3f408528:11a0042e513:-8000:00000000000008AB end

        return (string) $returnValue;
    }

    /**
     * Short description of method sort
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param boolean
     * @param string
     * @return void
     */
    public function sort($asc = true, $var = '')
    {
        // section 10-13-1--31-7f1456d9:11a242e5517:-8000:0000000000000F32 begin
		
			
			$varName = $var;
				
			$sortedEntriesIndexes=array();
			foreach ($this->arrayObjects as $key=>$object)
				{
					if ((isset($object->$varName)) and (is_array($object->$varName)))
							{
								
								foreach ($object->$varName as $subObject)
								{
									$cellValue = $subObject->label;
																		
								}
								
							}
							else
							{
							
							/*Define the content of the cell*/
							if (isset($object->$varName)) 
								{
									if (is_object($object->$varName)) 
										{ $cellValue = $object->$varName->label;$param1= $object->$varName->uri;}
									else 
										{$cellValue = $object->$varName;}
								}	
								else
								{$cellValue=$key;}
							

							
							}

					if (!(isset($sortedEntriesIndexes[$cellValue]))) 
						{$sortedEntriesIndexes[$cellValue]=$key;}
					else
						{$sortedEntriesIndexes[$cellValue.$key]=$key;}
				}
			if ($asc)
			{
			
			ksort($sortedEntriesIndexes);
			}
			else
			{
				krsort($sortedEntriesIndexes);
			}
			$sortedEntries=array();
			foreach ($sortedEntriesIndexes as $indexOfsourceData)
				{
					$sortedEntries[$indexOfsourceData]=$this->arrayObjects[$indexOfsourceData];
				}

			//print_r($sortedEntriesIndexes);
			$this->arrayObjects = $sortedEntries;
        // section 10-13-1--31-7f1456d9:11a242e5517:-8000:0000000000000F32 end
    }

} /* end of class ViewTable */

?>