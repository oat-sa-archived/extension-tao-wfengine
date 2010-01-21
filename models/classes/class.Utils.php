<?php

error_reporting(E_ALL);

/**
 * WorkFlowEngine - class.Utils.php
 *
 * $Id$
 *
 * This file is part of WorkFlowEngine.
 *
 * Automatic generated with ArgoUML 0.24 on 29.09.2008, 13:52:25
 *
 * @author firstname and lastname of author, <author@example.org>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--31--4660acca:119ecd38e96:-8000:000000000000086C-includes begin
// section 10-13-1--31--4660acca:119ecd38e96:-8000:000000000000086C-includes end

/* user defined constants */
// section 10-13-1--31--4660acca:119ecd38e96:-8000:000000000000086C-constants begin
// section 10-13-1--31--4660acca:119ecd38e96:-8000:000000000000086C-constants end

/**
 * Short description of class Utils
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 */
class Utils
{
    // --- ATTRIBUTES ---

	public static $logger;

    // --- OPERATIONS ---

    /**
     * Short description of method sendMail
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    public function sendMail()
    {
        // section 10-13-1--31--4660acca:119ecd38e96:-8000:000000000000086E begin
        // section 10-13-1--31--4660acca:119ecd38e96:-8000:000000000000086E end
    }

    /**
     * Short description of method isUri
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param string
     * @return boolean
     */
    public function isUri($uri)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31--4660acca:119ecd38e96:-8000:0000000000000870 begin
		if( preg_match( '/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}'
				   .'((:[0-9]{1,5})?\/.*)?$/i' ,$uri))
			{
			  return true;
			}
			else
			{
			  if (strpos($uri,"#")===0) {return true;} else {
			  return false;}
			}
        // section 10-13-1--31--4660acca:119ecd38e96:-8000:0000000000000870 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteR
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    public function deleteR()
    {
        // section 10-13-1--31--4660acca:119ecd38e96:-8000:0000000000000875 begin
        // section 10-13-1--31--4660acca:119ecd38e96:-8000:0000000000000875 end
    }

    /**
     * Short description of method contains
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param wfResource
     * @param array
     * @return boolean
     */
    public static function contains( wfResource $resource, $setOfResources)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31-7f1456d9:11a242e5517:-8000:0000000000000961 begin
		foreach ($setOfResources as $wfResource)
			{
				if ($resource->uri == $wfResource->uri) {return true;}
			}
        // section 10-13-1--31-7f1456d9:11a242e5517:-8000:0000000000000961 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRemoteKB
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param string
     * @return string
     */
    public function getRemoteKB($baseClass)
    {
        $returnValue = (string) '';

        // section 10-13-1--31-7f1456d9:11a242e5517:-8000:0000000000000967 begin
			include_once("../../../generis/core/view/generis_utils.php");
		include_once("../../../generis/core/view/generis_tree.php");
					$urimodel = substr($baseClass,0,strpos($baseClass,"#"));
					$file = str_replace("www.tao.lu/Ontologies/",$_SERVER["HTTP_HOST"]."/generis/Ontologies/",$urimodel);
					$dlmodel = importrdfs($_SESSION["session"],$urimodel,$file);
					Wfengine::singleton()->sessionGeneris=$dlmodel["pSession"];

					$idsub = getSubscribeesurl(Wfengine::singleton()->sessionGeneris,array($baseClass),"");
					foreach ($idsub as $key => $val)
						{
						$result = getRDFfromaremotemodule(Wfengine::singleton()->sessionGeneris,array($_SESSION["datalg"]), array($val[0]),false,"1");
						if (!(is_string($result))) {
							Wfengine::singleton()->sessionGeneris=$result["pSession"];
							$_SESSION["session"]=$result["pSession"];
							//print_r( unserialize(urldecode(Wfengine::singleton()->sessionGeneris[0])));
							}
						else
							{
								$output.="<script language=\"JavaScript\">window.alert('At least one module has not been reached (Bad login/password/url)');</script>";
							}
						}


        // section 10-13-1--31-7f1456d9:11a242e5517:-8000:0000000000000967 end

        return (string) $returnValue;
    }

    /**
     * Short description of method createProcessExecution
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param array
     * @return ProcessExecution
     */
    public function createProcessExecution($description)
    {
        $returnValue = null;

        // section 10-13-1--31-7f1456d9:11a242e5517:-8000:000000000000096A begin

		$classOfProcessInstances = "http://10.13.1.225/middleware/taoqual.rdf#119010455660544";
		$subject ="#".time().rand(0,65535);
		$errormessage = setStatement(Wfengine::singleton()->sessionGeneris,$subject,"http://www.w3.org/1999/02/22-rdf-syntax-ns#type",$classOfProcessInstances,"r","","","r");

		foreach ($description["properties"] as $key=>$val)
				{

					$privileges="";
					$properyAssignationismultiple = true;
					foreach ($val as $tripleid=>$avalue)
						{
							if (substr($tripleid,0,8) == "tripleid"){$properyAssignationismultiple = false;	}
						}
					$predicate = $key;
					if ($properyAssignationismultiple)
					{
						removeSubjectPredicate(Wfengine::singleton()->sessionGeneris,$subject,$predicate);

						$val =array_unique($val);
						foreach ($val as $tripleid=>$avalue)
						{

							$avalue = str_replace("+","%2B",$avalue);
							$object = urldecode($avalue);
							if (Utils::isURI($object)) {$object_is="r";$l_language="";}

							else {$object_is="l";$l_language="EN";}

							if ($object!="NULL") {
									setStatement(Wfengine::singleton()->sessionGeneris,$subject,$predicate,$object,$object_is,$l_language,"","r",$privileges);
							}

						}
					}
					else
					{
						foreach ($val as $tripleid=>$avalue)
						{
							$avalue = str_replace("+","%2B",$avalue);
							$object = urldecode($avalue);

							if (Utils::isURI($object))	{$object_is="r";$l_language="";}

							else {$object_is="l";$l_language=$_SESSION["datalg"];}
							if ($object!="NULL")		{
							editStatement(Wfengine::singleton()->sessionGeneris,substr($tripleid,8),$object,$object_is,$l_language,"","r");
							}
						}
					}
				}
			//add status
			setStatement(Wfengine::singleton()->sessionGeneris,$subject,STATUS,PROPERTY_PINSTANCES_STATUS,"r","","","r",$privileges);

			//add default action code.
			setStatement(Wfengine::singleton()->sessionGeneris,$subject,PROPERTY_PROCESSINSTANCE_ACTIONCODE,RESOURCE_ACTIONCODE_PROCEED_INTERVIEW,"r","","","r",$privileges);

			//add executionOf
			setStatement(Wfengine::singleton()->sessionGeneris,$subject,PROPERTY_PINSTANCES_EXECUTIONOF,urldecode($description["executionOf"]),"r","","","r",$privileges);

			// We perform special behaviours for if PIAAC enabled.
			if (defined('PIAAC_ENABLED'))
			{
				$var_interviewee_uri = null;

				// We get the interviewee uri from the process description.
				foreach ($description['properties'] as $propKey => $propValue)
				{
					if ($propKey == VAR_INTERVIEWEE_URI)
					{
						$var_interviewee_uri = $propValue[0];
						break;
					}
				}

				if ($var_interviewee_uri)
				{


					// We also remove all running processes with this interviewee because it
					// might causes consistency issues.
					$processExecutions = core_kernel_classes_Session::singleton()->model->execSQL("AND predicate = '". RDF_TYPE ."' AND object LIKE '". CLASS_PROCESS_EXECUTIONS ."'");

					foreach ($processExecutions as $processExecution)
					{
						$explodedExecutionUri = explode('#', $processExecution['subject']);
						$exec = new core_kernel_classes_Resource($processExecution['subject']);

						//echo 'exec: ' . $processExecution['subject'];
						//echo 'subject: ' . $subject;

						if (('#' . $explodedExecutionUri[1]) != $subject)
						{
							//echo 'exec that might be deleted';
							try
							{
//								$interviewee = $exec->getUniquePropertyValue(new core_kernel_classes_Property(VAR_INTERVIEWEE_URI));
////
////								// Reset interviewee ...
//								resetInterviewee($interviewee->uriResource);
////
//								$explodedIntervieweeUri = explode('#', $interviewee->uriResource);
////
//								if (('#'. $explodedIntervieweeUri[1]) == $var_interviewee_uri)
////								{
//									$processToRemove = new ProcessExecution($exec->uriResource);
//									var_dump($processToRemove);
//									$processToRemove->remove();
////									//echo $exec->uriResource . ' removed';
////								}
							}
							catch(common_Exception $e)
							{
								//echo 'errooor';
							}
						}
					}

					// Okay we can begin to "infer" some useful variables for the PIAAC case.
					// useful variables...
					$currentMonth = intval(date('n'));
					$currentYear = intval(date('Y'));
					$lastYear = $currentYear - 1;
					$randomValue = rand(1,4);

					// Month of the interview.
					createLiteralEffectiveVariableFor($var_interviewee_uri, 'A_D01a1', $currentMonth);
					createLiteralEffectiveVariableFor($var_interviewee_uri, 'A_D01a2', $lastYear);
					createLiteralEffectiveVariableFor($var_interviewee_uri, 'A_D01a3', $currentYear);
					createLiteralEffectiveVariableFor($var_interviewee_uri, 'RANDOM',  $randomValue);
				}

			}


			$returnValue = new ProcessExecution($subject);

			// Additional Variables
			if (defined('PIAAC_VERSION') && PIAAC_VERSION)
			{
				$processExecutionResource = new core_kernel_classes_Resource(INTERVIEW_NS . $returnValue->uri);
				createLiteralEffectiveVariableFor($var_interviewee_uri, 'COUNTRYCODE', PIAAC_VERSION);
				createLiteralEffectiveVariableFor($var_interviewee_uri, 'PROCESSURI', urlencode($processExecutionResource->uriResource));
			}
			
			$processVars = $returnValue->getVariables();
			$processVars = Utils::processVarsToArray($processVars);

			$initialActivities = $returnValue->process->getRootActivities();

			PiaacDataHolder::build($subject);

			foreach ($initialActivities as $key=>$activity)
			{
				//add token
				setStatement(Wfengine::singleton()->sessionGeneris,$subject,PROPERTY_PINSTANCES_TOKEN,$activity->uri,"r","","","r",$privileges);

				// Add in path
				setStatement(Wfengine::singleton()->sessionGeneris,$subject,PROPERTY_PINSTANCES_PROCESSPATH,$activity->uri,'r','','r',$privileges);
				setStatement(Wfengine::singleton()->sessionGeneris,$subject,PROPERTY_PINSTANCES_FULLPROCESSPATH,$activity->uri,'r','','r',$privileges);

				// OnBefore initial activity.
				// If the initial Activity has inference rules on before... let's run them !
				$activity->feedFlow(0);
				if (count($activity->onBeforeInferenceRule))
				{
					foreach ($activity->onBeforeInferenceRule as $onbir)
					{
						$onbir->execute($processVars);
					}
				}
			}

			// If the inital activity is "hidden", let's run it.
			$initializedProcess = new ProcessExecution($subject);

			if ($initializedProcess->currentActivity[0]->isHidden)
			{
				$initializedProcess->performTransition();
			}

			// We log the "NEW CASE" event in the log file.
			if (defined('PIAAC_ENABLED'))
			{
				$event = new PiaacEvent('BQ_ENGINE', 'Creation of the process',
										'process_created', getIntervieweeUriByProcessExecutionUri($subject));
				PiaacEventLogger::getInstance()->trigEvent($event);
			}

        // section 10-13-1--31-7f1456d9:11a242e5517:-8000:000000000000096A end

        return $returnValue;
    }

    /**
     * Short description of method renderSvg
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param string
     * @param string
     * @return string
     */
    public function renderSvg($uri, $svg)
    {
        $returnValue = (string) '';

        // section 10-13-1--31-7f1456d9:11a242e5517:-8000:0000000000000971 begin
		$file = "./view/svgprocess/".base64_encode($uri).".svg";
		$svgfile = fopen($file,"wb");fwrite($svgfile,$svg);fclose($svgfile);
		$file = "../../../WorkFlowEngine/view/svgprocess/".base64_encode($uri).".svg";
		$returnValue= '<span style="position:relative;"><embed src="'.$file.'" type="image/svg+xml"  width="665" height="2800"/></span>';
		/*
		$returnValue= '
		<span style=width=50%>
		<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="1000" height="920" id="process_svgm" align="middle">
			<param name="allowScriptAccess" value="sameDomain" />
			<param name="movie" value="exulis.swf?file='.$file.'" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#ffffff" />
			<embed src="./view/exulis/eXULiS.swf?file='.$file.'" quality="high" bgcolor="#ffffff" width="1000" height="920" name="process_svg" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
			</object>
		</span>';
		*/

        // section 10-13-1--31-7f1456d9:11a242e5517:-8000:0000000000000971 end

        return (string) $returnValue;
    }

    /**
     * Short description of method processVarsToArray
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param array
     * @return array
     */
    public function processVarsToArray($variables)
    {
        $returnValue = array();

        // section 10-13-1-85-687db94c:11cade0c19a:-8000:0000000000000A1D begin
        foreach ($variables as $var)
        	$returnValue[$var->uri] = $var->value;
        // section 10-13-1-85-687db94c:11cade0c19a:-8000:0000000000000A1D end

        return (array) $returnValue;
    }

    public function getLastViewableActivityFromPath(array $path, $from)
    {
    	$beginIndex = 0;
    	$i = 0;
    	$activity = null;

    	while ($i < count($path) && $path[$i] != $from)
    		$i++;

    	if ($i == count($path))
    		throw new common_Exception('Unable to find last viewable activity from path');


    	$beginIndex = $i;

    	while ($i >= 0)
    	{
    		$activity = new Activity($path[$i], true);
    		if (!$activity->isHidden)
    			return $activity;

    		$i--;
    	}

    	throw new common_Exception('Unable to find last viewable activity from path');
    }

    public static function getGenericLogger()
    {
    	if (!isset(self::$logger))
    		self::$logger = new common_Logger(Logger::fatal_level);

    	return self::$logger;
    }

} /* end of class Utils */

?>