<?php
if($_REQUEST['t']=="getRegion" && isset($_REQUEST['long']) && isset($_REQUEST['lat']) ){
	include_once("polygon.php");
	$point=array($_REQUEST['long'],$_REQUEST['lat']);

	$region=get_region($point);
	header('Content-Type: application/json');
	$return=new StdClass;
	$return->result=$region;
	echo json_encode($return);
}

?>