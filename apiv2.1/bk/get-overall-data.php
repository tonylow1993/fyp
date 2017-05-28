<?php

require "data.php";

$testcase_id = $_POST['testcase_id'];
$assignment_id = $_POST['assignment_id'];
$dataAPI = new data();

$data = array('pass'=>$dataAPI->getPassData(), 'fail'=>$dataAPI->getFailData(), 'attempt'=>$dataAPI->getAttemptData(), 'groupno'=>$dataAPI->getAssignmentGroup());

echo json_encode($data);