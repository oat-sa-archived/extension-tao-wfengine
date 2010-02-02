<?php

error_reporting(E_ALL);

/**
 * Implements svg rendering of a process.
 *
 * getSvg method returns the svg definiton. It makes use of the
 * method (building all objects describing the process) then draw swimlanes ,
 * svg ehader and footer accordingly
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
	die('This file was generated for PHP 5');
}

/**
 * include Process
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('class.Process.php');

/* user defined includes */
// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008C9-includes begin
// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008C9-includes end

/* user defined constants */
// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008C9-constants begin
// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008C9-constants end

/**
 * Implements svg rendering of a process.
 *
 * getSvg method returns the svg definiton. It makes use of the
 * method (building all objects describing the process) then draw swimlanes ,
 * svg ehader and footer accordingly
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class ViewProcess
extends Process
{
	// --- ATTRIBUTES ---

	/**
	 * Space is a two dimensional array describing activities and connectors
	 *
	 * @access private
	 * @var array
	 */
	private $space = array();

	/**
	 * horizontal space between the activites and the next connector
	 *
	 * @access public
	 * @var int
	 */
	public $hSpace = 125;

	/**
	 * vertical space between the activity and the next connector
	 *
	 * @access public
	 * @var int
	 */
	public $vSpace = 75;

	public $activityWidth = 100;

	public $activityHeight = 22;

	public $connectorWidth = 0;

	public $connectorHeight = 0;


	/**
	 * Short description of attribute hlActivities
	 *
	 * @access public
	 * @var array
	 */
	public $hlActivities = array();

	/**
	 * Short description of attribute editMode
	 *
	 * @access public
	 * @var boolean
	 */
	public $editMode = false;

	/**
	 * Short description of attribute execution
	 *
	 * @access public
	 * @var ProcessExecution
	 */
	public $execution = null;

	/**
	 * defines for each level the number of activities present in this level
	 */
	public $dimSpace = array();

	/**
	 * defines for each activities the level in which the are present,
	 * since activities are displayed once, the level for an activity is unique.
	 */
	public $dimActivities =array();

	/**
	 * grid of boolean telling if this palce is currently busy by an activity
	 **/
	public $dimGrid =array();


	public $tempActivities=array();

	// --- OPERATIONS ---

	/**
	 * recursive it renders activities , conenctors in svg then call back
	 * for consecutive activities till we reach the end of the process
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @param array
	 * @param array
	 * @return string
	 */
	private function drawActivities($activities, $previousConnectorProperties = array())
	{
		$returnValue = (string) '';
		//$activities = array_reverse($activities);
		// section -64--88-1-64--7117f567:11a0527df60:-8000:000000000000093A begin
		echo "<br/>";
		foreach ($activities as $key => $activity)
		{
			//$activity->feedFlow();
			/*if ($previousConnectorProperties[3]->uri=="http://127.0.0.1/middleware/Interview.rdf#i1224590429054911600")
				{
				echo $activity->uri;echo "<br />";

				}*/
			if (isset($previousConnectorProperties[4])) {$xIndex = $previousConnectorProperties[4];} else {$xIndex=0;}

			$Xy = $this->svgSpaceAllocate($activity,$xIndex);

			if (!(isset($this->space["drawn_Activities"][$activity->uri])))
			{
				//echo $activity->uri."<br/>";
				//draw the activity at the allocated space

				$returnValue.=$this->drawActivity($activity,$Xy[0],$Xy[1]);

			}
			else
			{
				//echo $activity->label. " is not drawn";
			}
			//links this activity with the original connector given in parameter if available
			if (sizeOf($previousConnectorProperties)>0)
			{
				$useColor="";
				if (isset($previousConnectorProperties[3]->transitionRule))
				{

					if ($previousConnectorProperties[3]->transitionRule->thenActivity instanceOf Activity)
					{
							
						if ($activity->uri ==
						$previousConnectorProperties[3]->transitionRule->thenActivity->uri)
						{
							$useColor="green";
							$returnValue.=$this->drawBezier($this->getBezier($previousConnectorProperties[0], $previousConnectorProperties[1]+7, $Xy[0]+($this->activityWidth/2), $Xy[1]),$previousConnectorProperties[2],$useColor);
						}

					}
						

					if ($previousConnectorProperties[3]->transitionRule->elseActivity instanceOf Activity)
					{
						if ($activity->uri ==
						$previousConnectorProperties[3]->transitionRule->elseActivity->uri)
						{
							$useColor="red";
							$returnValue.=$this->drawBezier($this->getBezier($previousConnectorProperties[0], $previousConnectorProperties[1]+7, $Xy[0]+($this->activityWidth/2), $Xy[1]),$previousConnectorProperties[2],$useColor);
						}

							
					}
					else
					{
						error_reporting(E_ALL);
						$xConnector = $previousConnectorProperties[3]->transitionRule->elseActivity;
						//draw the conenctor

						error_reporting("^E_NOTICE");
						$conXy = $this->svgSpaceAllocate($xConnector,$xIndex+1,$this->dimActivities[$activity->uri]);
						error_reporting("E_ALL");

						$returnValue .= $this->drawConnector($xConnector,
						$previousConnectorProperties[0],
						$previousConnectorProperties[1]+7,
						$conXy[0]+($this->activityWidth/2),
						$conXy[1]+($this->vSpace/2)
						);

						if (sizeOf($xConnector->nextActivities)>1) {$dashed = true;} else {$dashed =false;}


						$returnValue.=$this->drawActivities($xConnector->nextActivities,
						array(
						$conXy[0]+($this->activityWidth/2),
						$conXy[1]+($this->vSpace/2),
						$dashed,$xConnector,$xIndex+1));

					}

				}
				else
				{
					$returnValue.=$this->drawBezier($this->getBezier($previousConnectorProperties[0], $previousConnectorProperties[1]+7, $Xy[0]+($this->activityWidth/2), $Xy[1]),$previousConnectorProperties[2],$useColor);
				}
					
					

				//$returnValue.=$this->drawPolyLine($this->getPolyLine($previousConnectorProperties[0], $previousConnectorProperties[1]+7, $x+57, $y-2));
			}
				

			if (!(isset($this->space["drawn_Activities"][$activity->uri])))
			{
				$this->space["drawn_Activities"][$activity->uri]=true;
				//loop among all connectors for this activity
				foreach ($activity->nextConnectors as $nextConnector)
				{
					//draw the conenctor
					$returnValue .= $this->drawConnector($nextConnector,
					$Xy[0]+($this->activityWidth/2),
					$Xy[1]+($this->activityHeight),
					$Xy[0]+($this->activityWidth/2),
					$Xy[1]+($this->vSpace/2)
					);
						
					if (sizeOf($nextConnector->nextActivities)>1) {$dashed = true;} else {$dashed =false;}



					$returnValue.=$this->drawActivities($nextConnector->nextActivities,
					array(
					$Xy[0]+($this->activityWidth/2),
					$Xy[1]+($this->vSpace/2),
					$dashed,$nextConnector,$Xy[2]));

				}
			}
				
				
		}
		// section -64--88-1-64--7117f567:11a0527df60:-8000:000000000000093A end

		return (string) $returnValue;
	}

	/**
	 * returns (string) svg desdcribing swimlanes for this process and with
	 * descriptions
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @return string
	 */
	private function drawSwimLanes()
	{
		$returnValue = (string) '';

		// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008CC begin
		$roles =$this->getActors();

		$svg = "";
		$swimlanes=array();
		$nbSwimLanes=sizeOf($roles);
		($nbSwimLanes==1) ? $swimlaneWidth=650 : $swimlaneWidth=min(250,1040/$nbSwimLanes);
		$x=1;
		$y=2;

		foreach ($roles as $key=>$val)
		{
			if ($val[1]!="")
			{
				$svg.='
				<rect x = "'.$x.'" y = "'.$y.'" rx = "12" ry = "12" width = "'.$swimlaneWidth.'" height = "99%" fill = "white" stroke = "#334477" stroke-width = "1"/>';
				$svg.='
				<rect x = "'.$x.'" y = "'.$y.'"  rx = "12" ry = "12" width = "'.$swimlaneWidth.'" height = "50" fill = "url(#orange_red)" stroke = "#334477" stroke-width = "1"/>';

				$WindowTitle = trim(html_entity_decode(strip_tags($val[1])));
				$WindowId = str_replace(" ","",$WindowTitle);

				$xlinkRole='
				xlink:title="View Role '.$val[1].'" xlink:href="javascript:parent.getUrl(\'index.php?do=show&amp;param1='.urlencode(urlencode($val[0])).'\',\'Role '.$WindowTitle.'\',2,300,330,\''.$WindowId.'\')"
							';
				$svg.='
				<a '.$xlinkRole.'><text x = "'.($x+($swimlaneWidth/2)-20-(strlen($val[1]))).'" y = "'.($y+15).'" dx = "5" dy = "15" fill = "#003366" font-size = "12" font-weight="bold" font-style="normal">'.utf8_encode($val[1]).'</text></a>';	
				$svg.=$this->drawUser($x, $y);
				$swimlanes[$val[0]]["currentX"]=$x+20;
				$swimlanes[$val[0]]["currentY"]=100;

				if ($this->editMode)
				{
					$uriclassActor="#118588820437156";
					$WindowTitle = str_replace(" ","",trim(html_entity_decode(strip_tags("Edit ".$val[1]))));
					$WindowId = str_replace(" ","",$WindowTitle);

					$xlink='
						xlink:href="javascript:parent.getUrl(\'index.php?do=edit&amp;param1='.urlencode(urlencode($val[0])).'&amp;param2='.urlencode($this->uri).'\',\''.$WindowTitle.' \',2,300,300,\''.$WindowId.'\')" ';

					$svg.='
						<a '.$xlink.'>';
					$svg.='
						<use xlink:href="#pen" x= "'.($x+$swimlaneWidth-35).'" y = "7"/>
						';
					$svg.='</a>';

					$uriclassActor="#118588820437156";

					$WindowTitle = str_replace(" ","",trim(html_entity_decode(strip_tags("Remove".$val[1]))));
					$WindowId = str_replace(" ","",$WindowTitle);

					$xlink='
						xlink:href="javascript:parent.getUrl(\'index.php?do=remove&amp;param1='.urlencode(urlencode($val[0])).'&amp;param2='.urlencode($this->uri).'\',\''.$WindowTitle.'\',2,300,300,\''.$WindowId.'\')" ';

					$svg.='
						<a '.$xlink.'>';
					$svg.='
						<use xlink:href="#remove" x= "'.($x+$swimlaneWidth-35).'" y = "31"/>
						';
					$svg.='</a>';


					$uriclassActivity="#118588757437650";
					$WindowTitle = str_replace(" ","",trim(html_entity_decode(strip_tags("instanciate".str_replace("#","",$uriclassActivity)))));
					$WindowId = str_replace(" ","",$WindowTitle);
					$xlink='
						xlink:title="Create a new  activity" 	xlink:href="javascript:parent.getUrl(\'index.php?do=instanciate&amp;param1='.urlencode(urlencode($uriclassActivity)).'&amp;param2='.urlencode($this->uri).'\',\'Add an activity \',2,300,300,\''.$WindowId.'\')" ';

					$svg.='
						<a '.$xlink.'>';


					$svg.='
							<use xlink:href="#newRes" x= "'.($x+$swimlaneWidth-35).'" y = "530"/>
							';
					$svg.='</a>';


				}
				$x+=$swimlaneWidth;
			}
		}
		$returnValue=$svg;
		if ($this->editMode)
		{
			$uriclassActor="#118588820437156";

			$WindowTitle = str_replace(" ","",trim(html_entity_decode(strip_tags("Create  a new actor in".$val[1]))));
			$WindowId = str_replace(" ","",$WindowTitle);
			$xlink='
				xlink:title="Create a new actor" 	xlink:href="javascript:parent.getUrl(\'index.php?do=instanciate&amp;param1='.urlencode(urlencode($uriclassActor)).'&amp;param2='.urlencode($this->uri).'\',\''.$WindowTitle.' \',2,300,300,\''.$WindowId.'\')" ';

			$returnValue.='
				<a '.$xlink.'>';
			$returnValue.='
					<use xlink:href="#newRes" x= "'.($x+20).'" y = "10"/>
					';
			$returnValue.='</a>';
		}

		$this->space["swimlanes"]=$swimlanes;
		// section -64--88-1-64--7117f567:11a0527df60:-8000:00000000000008CC end

		return (string) $returnValue;
	}

	/**
	 * returns the complete svg of the process
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return string
	 */
	public function drawSvg()
	{
		$returnValue = (string) '';

		// section -64--88-1-64--7117f567:11a0527df60:-8000:0000000000000938 begin
		$this->feedFlow();

		$this->getDimensions();

		$returnValue .=SVGHEADER;
		if ($this->editMode) {$returnValue .=	EDITICONS;}
		$returnValue .="</defs>";
		$returnValue .=$this->drawSwimlanes();
		$returnValue .=$this->drawActivities($this->rootActivities,array());
		$returnValue .=$this->drawFooter();
		$returnValue .=$this->drawHeader();


		$returnValue .= '</svg>';
		$returnValue = utf8_encode($returnValue);
		// section -64--88-1-64--7117f567:11a0527df60:-8000:0000000000000938 end

		return (string) $returnValue;
	}

	/**
	 * Returns $x $y coordinates of a free space available for a given activitiy
	 * a given swimlane . If the activity has already been drawn, its previous
	 * are sent back
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @param Activity
	 * @param int
	 * @return array
	 */
	private function svgSpaceAllocate( $activity, $suggestXposition = 0,$suggestYpositionIndex = "0" )
	{
		$returnValue = array();

		// section 10-5-2-4--31ef9526:11a067edc9d:-8000:00000000000008F4 begin

		//suggestXposition, gives the father x position in dimGrid so that son appear below

		if (isset($this->space["activities_location"][$activity->uri]))
		{
			return array($this->space["activities_location"][$activity->uri][0],$this->space["activities_location"][$activity->uri][1],$this->space["activities_location"][$activity->uri][2]);
		}
		else
		{
			$maxHeightSvg = 550;
			$xDefault=10; $yDefault=10;
			$defaultStep = 1;
			//$this->dimSpace;
			//$this->dimActivities;
			error_reporting("^E_NOTICE");
			if ($suggestYpositionIndex !="0")
			{

				$level = $suggestYpositionIndex;
			}
			else
			{

				//retrieve level for this activity
				$level = $this->dimActivities[$activity->uri];
			}

			$nbActivitiesInThisLevel = $this->dimSpace[$level];

			//echo $activity->label; echo "&nbsp;".$level."&nbsp;".$suggestXposition."&nbsp;"; echo "<br />";
			$i=$suggestXposition; // default being 0

			if ($activity->uri =="http://127.0.0.1/middleware/Interview.rdf#i1224253739088802600")
			{
				$i=0;
			}
			if ($activity->uri =="http://127.0.0.1/middleware/Interview.rdf#i1224146303032389300")
			{
				$i=1;
			}
			if ($activity->uri =="http://127.0.0.1/middleware/Interview.rdf#i1224255418083587300")
			{
				$i=1;
			}


			if ($activity->uri =="http://127.0.0.1/middleware/Interview.rdf#i1224253921034147000")
			{
				$i=2;
			}
			if ($activity->uri =="http://127.0.0.1/middleware/Interview.rdf#i1224254095034733200")
			{
				$i=2;
			}
			//http://127.0.0.1/generis/core/view/generis_UiControllerHtml.php?do=show&param1=%23&type=p
			if ($activity->uri =="http://127.0.0.1/middleware/Interview.rdf#i1224254209049005500")
			{
				$i=3;
			}

			//http://127.0.0.1/generis/core/view/generis_UiControllerHtml.php?do=show&param1=%23&type=p
			if ($activity->uri =="http://127.0.0.1/middleware/Interview.rdf#i1224604907078989500")
			{
				$i=4;
			}

			//http://127.0.0.1/generis/core/view/generis_UiControllerHtml.php?do=show&param1=%23&type=p
			if ($activity->uri =="http://127.0.0.1/middleware/Interview.rdf#i1224587880008043300")
			{
				$i=4;
			}

			//http://127.0.0.1/generis/core/view/generis_UiControllerHtml.php?do=show&param1=%23&type=p#i1224658275004655000
			//http://127.0.0.1/generis/core/view/generis_UiControllerHtml.php?do=show&param1=%23&type=p
			if ($activity->uri =="http://127.0.0.1/middleware/Interview.rdf#i1224658275004655000")
			{
				$i=1;$level++;
			}

			//http://127.0.0.1/generis/core/view/generis_UiControllerHtml.php?do=show&param1=%23i1224587880008043300&type=p
			//http://127.0.0.1/generis/core/view/generis_UiControllerHtml.php?do=show&param1=%23&type=p#i1224604907078989500

			//	http://127.0.0.1/generis/core/view/generis_UiControllerHtml.php?do=show&param1=%23&type=p
			//http://127.0.0.1/generis/core/view/generis_UiControllerHtml.php?do=show&param1=%23&type=p

			if ($this->dimSpace[($level+1)] > 2)
			{
				$defaultStep+=2;
			}
			if ($this->dimSpace[($level)] > 2)
			{
				//$defaultStep+=1;
			}
			//if this position is busy,
			if ($this->dimGrid[$level][$i])
			{

				//try first to move left
				while (($this->dimGrid[$level][$i]) and ($i>0))
				{
						
					$i=$i-1;
				}


				//try to move right
				if ($this->dimGrid[$level][$i])
				{
					$i=$suggestXposition;
					while (($this->dimGrid[$level][$i]))
					{
						$i=$i+$defaultStep;
					}
				}


			}


			$this->dimGrid[$level][$i] = true;

			$x	= ($i)*$this->hSpace+$xDefault;
			$y	= ($level+1) * $this->vSpace+$yDefault;


			$this->space["activities_location"][$activity->uri][0]=$x;
			$this->space["activities_location"][$activity->uri][1]=$y;
			$this->space["activities_location"][$activity->uri][2]=$i;
			$returnValue = array($x,$y,$i);
		}
		// section 10-5-2-4--31ef9526:11a067edc9d:-8000:00000000000008F4 end

		return (array) $returnValue;
	}

	/**
	 * Short description of method __construct
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @param string
	 * @return void
	 */
	public function __construct($uri)
	{
		// section 10-5-2-4--31ef9526:11a067edc9d:-8000:0000000000000904 begin
		parent::__construct($uri);
		$this->space =array("x" => 0, "y" => 77,"swimlanes" =>array());
		// section 10-5-2-4--31ef9526:11a067edc9d:-8000:0000000000000904 end
	}

	/**
	 * draw a simple activity in svg
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @param Activity
	 * @param int
	 * @param int
	 * @param boolean
	 * @return string
	 */
	private function drawActivity( Activity $activity, $x, $y, $enabled = false)
	{
		$returnValue = (string) '';

		// section 10-13-1--31--3a488a3e:11a0a584d94:-8000:0000000000000909 begin

		if (Utils::contains($activity, $this->hlActivities))
		{
			$fillcolor ="url(#orange_red)";
			$textcolor="firebrick";
				
			$WindowTitle = str_replace("&nbsp;","",trim(html_entity_decode(strip_tags("Perform activity ".$activity->label))));
			//echo $WindowTitle;die();
			$WindowId = "win".rand(0,65535);

			$xlinkActivity='
					xlink:title="Perform Activity '.html_entity_decode($activity->label).'"	xlink:href="javascript:parent.getUrl(\'index.php?do=performActivity&amp;param1='.urlencode(urlencode($activity->uri)).'&amp;param2='.urlencode(urlencode($this->execution->uri)).'\',\''.$WindowTitle.'\',360,300,900,\''.$WindowId.'\')" ';
		}
		else
		{
			$fillcolor = "#F0F0F0";
			$textcolor="black";
			$WindowTitle = str_replace(" ","",trim(html_entity_decode(strip_tags("View Activity ".$activity->label))));
			$WindowId = str_replace(" ","",$WindowTitle);

			$xlinkActivity='
					xlink:title="View '.html_entity_decode($activity->label).'" 	xlink:href="javascript:parent.getUrl(\'index.php?do=show&amp;param1='.urlencode(urlencode($activity->uri)).'\',\''.$WindowTitle.'\',2,300,353,\''.$WindowId.'\')"
					';
		}


		$returnValue.='
				<a '.$xlinkActivity.'>';

		$returnValue.='
						<use xlink:href="#activity" x= "'.($x+4).'" y = "'.$y.'" fill = "'.$fillcolor.'"/>';

		$activity->label = str_replace("Activity_","",$activity->label);
		$activity->label = str_replace("_V35","",$activity->label);
		if ((strlen($activity->label)) > 17)
		{$activity->label = substr(html_entity_decode($activity->label),0,11)."...";}

		$returnValue.='
					<text x = "'.($x+8).'" y = "'.($y+4).'" dx = "2" dy = "11" fill = "'.$textcolor.'" font-weight = "bold" font-size = "10">'.html_entity_decode($activity->label).'</text>';	

		$returnValue.='
				</a>

								
				';
		if ($this->editMode)
		{
			//draw icons
			$WindowTitle = str_replace(" ","",trim(html_entity_decode(strip_tags("Edit ".$activity->label))));
			$WindowId = str_replace(" ","",$WindowTitle);
			$xlink='
						xlink:title="Edit '.html_entity_decode($activity->label).'" 	xlink:href="javascript:parent.getUrl(\'index.php?do=edit&amp;param1='.urlencode(urlencode($activity->uri)).'&amp;param2='.urlencode($this->uri).'\',\''.$WindowTitle.' \',2,300,330,\''.$WindowId.'\')" ';

			$returnValue.='
						<a '.$xlink.'>';
			$returnValue.='
						<use xlink:href="#pen" x= "'.($x+120).'" y = "'.($y-10).'"/>
						';
			$returnValue.='</a>';
			$WindowTitle = str_replace(" ","",trim(html_entity_decode(strip_tags("Remove ".$activity->label))));
			$WindowId = str_replace(" ","",$WindowTitle);
			$xlink='
						xlink:title="Remove '.html_entity_decode($activity->label).'" 	xlink:href="javascript:parent.getUrl(\'index.php?do=remove&amp;param1='.urlencode(urlencode($activity->uri)).'&amp;param2='.urlencode($this->uri).'\',\''.$WindowTitle.' \',2,300,330,\''.$WindowId.'\')" ';

			$returnValue.='
						<a '.$xlink.'>';
			$returnValue.='
						<use xlink:href="#remove" x= "'.($x+120).'" y = "'.($y+15).'"/>
						';
			$returnValue.='</a>';


			$tools = $activity->getTools();
			$i=65;
			foreach ($tools as $tool)
			{
				$WindowTitle = str_replace(" ","",trim(html_entity_decode(strip_tags("Edit ".$tool->label))));
				$WindowId = str_replace(" ","",$WindowTitle);

				$xlink=' xlink:href="javascript:parent.getUrl(\'index.php?do=edit&amp;param1='.urlencode(urlencode($tool->uri)).'&amp;param2='.urlencode($this->uri).'\',\''.$WindowTitle.' \',2,300,300,\''.$WindowId.'\')" xlink:title=" Edit the way the service '.(html_entity_decode($tool->label)).' is used (configure its inputs and ouputs variables)"
									 ';

				$returnValue.='
										<a '.$xlink.'  >';
				$returnValue.='
										<use xlink:href="#hammer" x= "'.($x+$i).'" y = "'.($y-22).'" />
										
										';


				$returnValue.='</a>';
				$i+=25;


			}

		}



		// section 10-13-1--31--3a488a3e:11a0a584d94:-8000:0000000000000909 end

		return (string) $returnValue;
	}

	/**
	 * draw a simple conector in svg according to its type (sequence, split,
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @param Connector
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 * @return string
	 */
	private function drawConnector( Connector $connector, $activityX, $activityY, $connectorX, $connectorY)
	{
		$returnValue = (string) '';

		// section 10-13-1--31--3a488a3e:11a0a584d94:-8000:000000000000090E begin
		$WindowTitle = str_replace(" ","",trim(html_entity_decode(strip_tags("Connector ".$connector->label))));
		$WindowId = str_replace(" ","",$WindowTitle);
		$xlinkActivity='
		 xlink:title="View the connector '.html_entity_decode($connector->label).'" xlink:href="javascript:parent.getUrl(\'index.php?do=show&amp;param1='.urlencode(urlencode($connector->uri)).'\',\''.$WindowTitle.'\',2,300,330,\''.$WindowId.'\')"
							';

		switch ($connector->type)
		{
			//sequence
			case CONNECTOR_SEQ:
				{
					//add connector
					$returnValue.='
							<a '.$xlinkActivity.'>
							<use xlink:href="#sequence" x="'.$connectorX.'" y="'.$connectorY.'" />
							</a>';
					$this->space["connector_location"][$connector->uri] = array($connectorX,$connectorY);
						
					//add line
					$returnValue .= $this->drawBezier($this->getBezier($activityX, $activityY, $connectorX, $connectorY-7));

					break;
				}
				//join
			case "http://10.13.1.225/middleware/taoqual.rdf#11858924147584":
				{
					if (!(isset($this->space["connector_location"][$connector->uri])))
					{
							
						$returnValue.='<use xlink:href="#sync" x = "'.($connectorX-10).'" y = "'.($connectorY-5).'" />';
						$this->space["connector_location"][$connector->uri] = array($connectorX,$connectorY);

						//add line
						$returnValue .= $this->drawBezier($this->getBezier($activityX, $activityY, $connectorX, $connectorY-10));

					}
					else
					{
							
						$returnValue.=$this->drawBezier($this->getBezier($activityX,  $activityY,  $this->space["connector_location"][$connector->uri][0]+10,  $this->space["connector_location"][$connector->uri][1]));
						/*
						 $returnValue.='<polyline points = "'.$activityX.','.$activityY.' '.$activityX.','.($activityY+10).' '.$thirdDot.','.($activityY+10).' '.$thirdDot.','.$conenctorY.' '.$connectorX.','.$conenctorY.'" stroke = "black" stroke-width = "1" fill="none" marker-end="url(#startMarker)"/>';
						 */
					}

					break;
				}
				//split
			case CONNECTOR_SPLIT:
				{
						

					if (!(isset($this->space["connector_location"][$connector->uri])))
					{
							
						$returnValue.='<use xlink:href="#split" x = "'.($connectorX-12).'" y = "'.($connectorY-6).'" />';
						$returnValue.='<text x = "'.($connectorX+10).'" y = "'.($connectorY-15).'" dx = "5" dy = "15" fill = "black" font-size = "10" font-weight = "bold">'.strip_tags(str_replace("&nbsp;","",$connector->label)).'</text>';
						$this->space["connector_location"][$connector->uri] = array($connectorX,$connectorY);
						$returnValue .= $this->drawBezier($this->getBezier($activityX, $activityY, $connectorX, $connectorY-10));
					}
					else
					{
						$returnValue.=$this->drawBezier($this->getBezier($activityX,  $activityY,  $this->space["connector_location"][$connector->uri][0]+10,  $this->space["connector_location"][$connector->uri][1]));

					}

					break;
				}

					

		}
		$x =$this->space["connector_location"][$connector->uri][0]-2*$this->hSpace+7;
		$y = $this->space["connector_location"][$connector->uri][1] ;
		if ($this->editMode)
		{
			//draw icons
			$WindowTitle = str_replace(" ","",trim(html_entity_decode(strip_tags("Edit ".$connector->label))));
			$WindowId = str_replace(" ","",$WindowTitle);
			$xlink='
			xlink:title="Edit '.html_entity_decode($connector->label).'" 	xlink:href="javascript:parent.getUrl(\'index.php?do=edit&amp;param1='.urlencode(urlencode($connector->uri)).'&amp;param2='.urlencode($this->uri).'\',\''.$WindowTitle.' \',2,300,330,\''.$WindowId.'\')" ';

			$returnValue.='
			<a '.$xlink.'>';
			$returnValue.='
			<use xlink:href="#pen"  x= "'.($x+120).'" y = "'.($y-10).'"/>
			';
			$returnValue.='</a>';
			$WindowTitle = str_replace(" ","",trim(html_entity_decode(strip_tags("Remove ".$connector->label))));
			$WindowId = str_replace(" ","",$WindowTitle);
			$xlink='
			xlink:title="Remove '.html_entity_decode($connector->label).'" 	xlink:href="javascript:parent.getUrl(\'index.php?do=remove&amp;param1='.urlencode(urlencode($connector->uri)).'&amp;param2='.urlencode($this->uri).'\',\''.$WindowTitle.' \',2,300,330,\''.$WindowId.'\')" ';

			$returnValue.='
			<a '.$xlink.'>';
			$returnValue.='
			<use xlink:href="#remove" x= "'.($x+120).'" y = "'.($y+14).'"/>
			';
			$returnValue.='</a>';
		}
		// section 10-13-1--31--3a488a3e:11a0a584d94:-8000:000000000000090E end

		return (string) $returnValue;
	}

	/**
	 * Short description of method drawHeader
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @return string
	 */
	private function drawHeader()
	{
		$returnValue = (string) '';

		// section 10-13-1--31--3a488a3e:11a0a584d94:-8000:000000000000091B begin
		//$returnValue.='<text x = "10" y = "17" dx = "2" dy = "2" fill = "black" font-weight="bold" font-size = "15">'.html_entity_decode($this->label).'</text>';
		// section 10-13-1--31--3a488a3e:11a0a584d94:-8000:000000000000091B end

		return (string) $returnValue;
	}

	/**
	 * Short description of method drawFooter
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @return string
	 */
	private function drawFooter()
	{
		$returnValue = (string) '';

		// section 10-13-1--31--3a488a3e:11a0a584d94:-8000:000000000000091D begin
		$legendLeft = 750;
		$legendTop  = 610;

		$returnValue.='<use xlink:href="#sequence" x="'.($legendLeft+10).'" y="'.($legendTop+10).'" />';
		$returnValue.='<text x = "'.($legendLeft+30).'" y = "'.($legendTop).'" dx = "5" dy = "15" fill = "black" font-size = "12">Sequence</text>';

		$returnValue.='<use xlink:href="#sync" x = "'.($legendLeft).'" y = "'.($legendTop+25).'" />';
		$returnValue.='<text x = "'.($legendLeft+30).'" y = "'.($legendTop+20).'" dx = "5" dy = "15" fill = "black" font-size = "12">Sync Join</text>';
		//to do improve the way this legend is computed
		$returnValue='';
		// section 10-13-1--31--3a488a3e:11a0a584d94:-8000:000000000000091D end

		return (string) $returnValue;
	}

	/**
	 * Short description of method drawArrow
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 * @return string
	 */
	private function drawArrow($a, $b, $c, $d)
	{
		$returnValue = (string) '';

		// section 10-13-1--31--3a488a3e:11a0a584d94:-8000:0000000000000925 begin
		$returnValue.='
			<line x1 = "'.($a).'" y1 = "'.($b).'" x2 = "'.($c).'" y2 = "'.($d).'" stroke = "black" stroke-width = "1" marker-end="url(#startMarker)"/>';
		// section 10-13-1--31--3a488a3e:11a0a584d94:-8000:0000000000000925 end

		return (string) $returnValue;
	}

	/**
	 * Short description of method drawPolyLine
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @param array
	 * @return string
	 */
	private function drawPolyLine($coordinates)
	{
		$returnValue = (string) '';

		// section 10-13-1--31--3a488a3e:11a0a584d94:-8000:000000000000092B begin
		$strCoordinates = '';
		foreach ($coordinates as $coordinate)
		{
			$strCoordinates.=''.$coordinate.',';
		}
		$returnValue.='
		<polyline points = "'.substr($strCoordinates,0,strlen($strCoordinates)-1).'" stroke = "black" stroke-width = "1" fill="none" marker-end="url(#startMarker)"/>';
		// section 10-13-1--31--3a488a3e:11a0a584d94:-8000:000000000000092B end

		return (string) $returnValue;
	}

	/**
	 * Short description of method drawUser
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @param int
	 * @param int
	 * @return string
	 */
	private function drawUser($a, $b)
	{
		$returnValue = (string) '';

		// section 10-13-1--31--3a488a3e:11a0a584d94:-8000:0000000000000931 begin
		$returnValue.='
				<g>';
		$returnValue.='
					<circle cx="'.($a+25).'" cy="'.($b+15).'" r="5"  stroke="black" fill="white" stroke-width="1" />';
		//body
		$returnValue.='
					<line x1 = "'.($a+25).'" y1 = "'.($b+20).'" x2 = "'.($a+25).'" y2 = "'.($b+32).'" stroke = "black" stroke-width = "1"/>';
		//arms
		$returnValue.='
					<line x1 = "'.($a+20).'" y1 = "'.($b+25).'" x2 = "'.($a+30).'" y2 = "'.($b+25).'" stroke = "black" stroke-width = "1"/>';
		//legs
		$returnValue.='
					<line x1 = "'.($a+25).'" y1 = "'.($b+32).'" x2 = "'.($a+30).'" y2 = "'.($b+40).'" stroke = "black" stroke-width = "1"/>';
		$returnValue.='
					<line x1 = "'.($a+25).'" y1 = "'.($b+32).'" x2 = "'.($a+20).'" y2 = "'.($b+40).'" stroke = "black" stroke-width = "1"/>';
		$returnValue.='
				</g>';
		// section 10-13-1--31--3a488a3e:11a0a584d94:-8000:0000000000000931 end

		return (string) $returnValue;
	}

	/**
	 * Short description of method getPolyLine
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 * @return array
	 */
	private function getPolyLine($originX, $originY, $destinationX, $destinationY)
	{
		$returnValue = array();

		// section 10-13-1--31-5c41752d:11a0f7dccc6:-8000:000000000000093B begin


		//add line
		if ($destinationX<$originX)
		{
			$thirdDot = $originX - (abs($originX-$destinationX)/2);
		}
		else
		{
			$thirdDot = abs($destinationX-$originX);
		}
		$returnValue =array($originX,$originY,$originX,($originY+10),$thirdDot,($originY+10),$thirdDot,$destinationY,$destinationX,$destinationY);

		// section 10-13-1--31-5c41752d:11a0f7dccc6:-8000:000000000000093B end

		return (array) $returnValue;
	}

	/**
	 * Short description of method getBezier
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @param int
	 * @param int
	 * @param int
	 * @param int
	 * @return array
	 */
	private function getBezier($originX, $originY, $destinationX, $destinationY)
	{
		$returnValue = array();

		// section 10-13-1--31-5c41752d:11a0f7dccc6:-8000:0000000000000ED1 begin

		$gapX=0; $gapY=0;
		$factorX = (abs($originX-$destinationX)/700);
		$factorY = (abs($originY-$destinationY)/1000);
		$isCurved =false;
		if (
		(
		((abs($originX-$destinationX)) > 5)
		or
		($originY>$destinationY)
		)
		and
		(
		((abs($originY-$destinationY))> 5)
		)
		)
			
		{	$isCurved =true;

		$gapX = abs($originY-$destinationY)* $factorX;
		$gapY = abs($originX-$destinationX)* $factorY;
			
		//left right
		if ($originX<=$destinationX)
		{
			$firstControlPointX = $originX+$gapX;
			$secondControlPointX = $destinationX-$gapX;
		}
		//right left
		else
		{
			$firstControlPointX = $originX-3 * $gapX ;
			$secondControlPointX = $destinationX+4 * $gapX  ;
		}
		//top down
		if ($originY<=$destinationY)
		{
			$firstControlPointY = $originY+$gapY;
			$secondControlPointY = $destinationY-$gapY;
		}
		//bottom up
		else
		{
			$firstControlPointY = $originY+6*$gapY;
			$secondControlPointY = $destinationY-6*$gapY;
		}
		//bottom up and right left
		if (($originX>$destinationX) and ($originY>$destinationY) and (abs($originX-$destinationX)>$this->hSpace*1.5) and (abs($originY-$destinationY)>$this->vSpace*1.5))
		{
			$firstControlPointX = $originX + 1 * $this->hSpace;
			$firstControlPointY = $originY+1*$gapY;

			$secondControlPointX = $destinationX+9 * $gapX  ;
			$secondControlPointY = $destinationY-4*$gapY;
		}
		}
		else

		{
			$firstControlPointX = $originX;
			$secondControlPointX = $originX;
			$firstControlPointY = $originY;
			$secondControlPointY = $originY;
		}



		$returnValue =array(
		$originX,$originY,
		$firstControlPointX,$firstControlPointY,
		$secondControlPointX,$secondControlPointY,
		$destinationX,$destinationY);



		// section 10-13-1--31-5c41752d:11a0f7dccc6:-8000:0000000000000ED1 end

		return (array) $returnValue;
	}

	/**
	 * Short description of method drawBezier
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @param array
	 * @param boolean
	 * @param string
	 * @return string
	 */
	private function drawBezier($coordinates, $dashed = false, $useColor = "black")
	{
		$returnValue = (string) '';

		// section 10-13-1--31-5c41752d:11a0f7dccc6:-8000:0000000000000ED7 begin
		$originpoint= 'M'.$coordinates[0].",".$coordinates[1];
		$firstControlPoint= 'C'.$coordinates[2].",".$coordinates[3];
		$secondControlPoint= $coordinates[4].",".$coordinates[5];
		$destinationPoint= $coordinates[6].",".$coordinates[7];
		$path = $originpoint." ".$firstControlPoint." ".$secondControlPoint." ".$destinationPoint;
		$stroke = "#660033";
		if ($useColor != "") $stroke = $useColor;
		if ($dashed)
		{
			$returnValue.='
		<path d="'.$path.'" stroke="'.$stroke.'" style="stroke-dasharray: 9, 5;"
  fill="none" marker-end="url(#startMarker)"/>';
		}
		else
		{
			$returnValue.='
		<path d="'.$path.'" stroke="'.$stroke.'"  fill="none" marker-end="url(#startMarker)"/>';
		}


		// section 10-13-1--31-5c41752d:11a0f7dccc6:-8000:0000000000000ED7 end

		return (string) $returnValue;
	}

	/**
	 * Short description of method getFormNewExecution
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return string
	 */
	public function getFormNewExecution()
	{
		$returnValue = (string) '';

		// section 10-13-1--31-7f1456d9:11a242e5517:-8000:0000000000000965 begin

		$returnValue="<div style=background-color:white;padding:2px;><form action=index.php?do=newExecution method=post>";
		$returnValue.="<input type=hidden name=posted[executionOf] value=\"".urlencode($this->uri)."\">";
		$varsDefs = $this->getProcessVars();
		$variables = $varsDefs;

		$returnValue .= "<table><th colspan=10 >Process Instanciation :: ".$this->label."</th>";
		$returnValue .= "<tr class=lightBlue>";
		$returnValue .= "<td class=lightBlue>Variable Name</td>";
		$returnValue .= "<td class=lightBlue>Value</td>";
		$returnValue .= "</tr>";
		foreach ($varsDefs as $key=>$variable)
		{
			$returnValue.="<tr>";
			$returnValue .= "<td >".$variable[0]."</td>";
			$val["PropertyRange"] = $variable[2];
			$val["PropertyKey"] = $key;
				
			Utils::getRemoteKB($val["PropertyRange"]);
				
			error_reporting(E_ALL);
			if ($variable[1]!="")
			{
				include("../../../generis/core/widgets/".urlencode($variable[1]).".php");
			}
			$widget = str_replace("instanceCreation","posted",$widget);
			$returnValue .= "<td >".$widget."</td>";
			$returnValue.="</tr>";
		}

		$returnValue.="</table><input style=\"position:relative;left:91%;top:12%;font-size:12px;\" type=submit name=posted[new] /></form></div>";
		// section 10-13-1--31-7f1456d9:11a242e5517:-8000:0000000000000965 end

		return (string) $returnValue;
	}


	public function getDimensions()
	{
		$this->tempActivities=array();
		$this->dimActivities=array();
		$dimension = 0;
		$this->getNodeDimension($this->rootActivities, $dimension);
			
			
			
	}

	public function getNodeDimension($activities,$dimension)
	{
		if (!(isset($this->dimSpace[$dimension]))) {$this->dimSpace[$dimension]=0;}
			
		foreach ($activities as $key => $activity)
		{
			$activity->feedFlow();
			if (!(isset($this->dimActivities[$activity->uri])))
			{
				$this->dimActivities[$activity->uri]=$dimension;
				$this->dimSpace[$dimension]++;
			}
			else
			{
				if ($this->dimActivities[$activity->uri]==$dimension)
				{

				}
				else
				{
					if ($this->dimActivities[$activity->uri]>$dimension)
					{

						//remove it from bottom
						$this->dimSpace[$this->dimActivities[$activity->uri]]--;
						//put it in the current dimension
						$this->dimSpace[$dimension]++;
						//change its level
						$this->dimActivities[$activity->uri]=$dimension;
					}
				}
					
			}

		}

			
		foreach ($activities as $key => $activity)
		{


			$this->tempActivities[$activity->uri]=true;


			foreach ($activity->nextConnectors as $nextConnector)
			{
					
				$filteredActivities=array();
				foreach ($nextConnector->nextActivities as $activityClone)
				{
					if (!(isset($this->tempActivities[$activityClone->uri])))
					{
						$filteredActivities[]=$activityClone;
					}
				}
				$this->getNodeDimension($filteredActivities,($dimension+1));
					
				/*
				 {	$filteredActivities=array();
				 foreach ($nextConnector->transitionRule->elseActivity->nextActivities as $activityClone)
				 {
				 if (!(isset($this->tempActivities[$activityClone->uri])))
				 {
				 $filteredActivities[]=$activityClone;
				 }
				 	
				 }
				 $this->getNodeDimension($filteredActivities,($dimension+1));

				 }
				 */


			}

		}
			
			
			
	}

}
/* end of class ViewProcess */

/*

if ($this->dimActivities[$activity->uri]>$dimension)
{
$this->dimSpace[$this->dimActivities[$activity->uri]]--;
$this->dimActivities[$activity->uri]=$dimension;

	
}
*/

?>