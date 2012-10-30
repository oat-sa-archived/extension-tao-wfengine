<?php
/**
 * PLACEHOLDER class for transition periode
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

class wfEngine_models_classes_ProcessAuthoringService
    extends wfAuthoring_models_classes_ProcessService {
    	
    protected function __construct() {
    	common_Logger::w('instantiation of deprecated service '.__CLASS__);
    	parent::__construct();
    }
}