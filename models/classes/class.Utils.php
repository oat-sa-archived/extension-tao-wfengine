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
	 * @param WfResource
	 * @param array
	 * @return boolean
	 */
	public static function contains( WfResource $resource, $setOfResources)
	{
		$returnValue = (bool) false;

		// section 10-13-1--31-7f1456d9:11a242e5517:-8000:0000000000000961 begin
		foreach ($setOfResources as $WfResource)
		{
			if ($resource->uri == $WfResource->uri) {return true;}
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