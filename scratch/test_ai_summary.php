<?php

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Database.php';
require_once 'core/Model.php';
require_once 'app/models/ResolutionModel.php';
require_once 'app/models/AiValidationModel.php';

$m = new ResolutionModel();
$doc = $m->findById(2);
echo "Document: " . json_encode($doc) . "\n\n";

$ai = new AiValidationModel();
$reportId = $ai->runValidation('resolution', 2, 1);
echo "Report ID: " . var_export($reportId, true) . "\n\n";
