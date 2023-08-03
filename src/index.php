<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
require('functions.inc.php');

$output = array(
	"error" => false,
	"message" => "",
  	"items" => "",
	"attendance" => 0,
	"sorted_attendance" => ""
);

$item_1 = $_REQUEST['item_1'];
$item_2 = $_REQUEST['item_2'];
$item_3 = $_REQUEST['item_3'];
$item_4 = $_REQUEST['item_4'];
$attendance_1 = $_REQUEST['attendance_1'];
$attendance_2 = $_REQUEST['attendance_2'];
$attendance_3 = $_REQUEST['attendance_3'];
$attendance_4 = $_REQUEST['attendance_4'];
$total_hours_1 = $_REQUEST['total_hours_1'];
$total_hours_2 = $_REQUEST['total_hours_2'];
$total_hours_3 = $_REQUEST['total_hours_3'];
$total_hours_4 = $_REQUEST['total_hours_4'];

$items = array($item_1,$item_2,$item_3,$item_4);
$attendances = array($attendance_1,$attendance_2,$attendance_3,$attendance_4);
$total_hours = array($total_hours_1, $total_hours_2, $total_hours_3, $total_hours_4);

// Check if any session names are empty (as outlined in Section B of spec)
foreach($items as $item) {
	if(empty($item)) {
	  $output['error'] = true;
	  $output['message'] = "Item names cannot be empty.";
	  echo json_encode($output);
	  exit();
	}
}

for($i = 0; $i < count($attendances); $i++) {
	$attendance = $attendances[$i];
	$total_assigned_hours = $total_hours[$i]; // Get the corresponding total hours
  
	// Check if attendance is a number/numeric string OR a float instead of an int
	if(!is_numeric($attendance) || (int)$attendance != $attendance) {
	  $output['error'] = true;
	  $output['message'] = "Attendance hours must be integers.";
	  echo json_encode($output);
	  exit();
	}

	// Check if total hours is a number/numeric string OR a float instead of an int (even though it's hard coded)
	if(!is_numeric($total_assigned_hours) || (int)$total_assigned_hours != $total_assigned_hours) {
		$output['error'] = true;
		$output['message'] = "Total hours must be integers.";
		echo json_encode($output);
		exit();
	}
  
	// Check if attendance is within acceptable range
	if($attendance > $total_assigned_hours) {
	  $output['error'] = true;
	  $output['message'] = "Attendance hours cannot exceed total assigned hours.";
	  echo json_encode($output);
	  exit();
	}

	// Check if attendance is non-negative
	if ($attendance < 0) {
		$output['error'] = true;
		$output['message'] = "Attendance hours cannot be negative.";
		echo json_encode($output);
		exit();
	}

	// Check if total hours is non-negative
	if ($total_assigned_hours < 0) {
		$output['error'] = true;
		$output['message'] = "Total hours cannot be negative.";
		echo json_encode($output);
		exit();
	}
}

$sorted_attendance=getSortedAttendance($items, $attendances);

$output['items']=$items;
$output['attendance']=$attendances;
$output['sorted_attendance']=$sorted_attendance;

echo json_encode($output);
exit();
?>