<?php
/*
Description: The point-in-polygon algorithm allows you to check if a point is
inside a polygon or outside of it.
Author: Michaël Niessen (2009)
Website: http://AssemblySys.com
 
If you find this script useful, you can show your
appreciation by getting Michaël a cup of coffee ;)
PayPal: michael.niessen@assemblysys.com
 
As long as this notice (including author name and details) is included and
UNALTERED, this code is licensed under the GNU General Public License version 3:
http://www.gnu.org/licenses/gpl.html
*/
 
class pointLocation {
    var $pointOnVertex = true; // Check if the point sits exactly on one of the vertices?
 
    function pointLocation() {
    }
 
        function pointInPolygon($point, $polygon, $pointOnVertex = true) {
        $this->pointOnVertex = $pointOnVertex;
 
        // Transform string coordinates into arrays with x and y values
        $point = $this->pointToCoordinates($point);
        $vertices = array(); 
        foreach ($polygon as $vertex) {
            $vertices[] = $this->pointToCoordinates($vertex); 
        }
 
        // Check if the point sits exactly on a vertex
        if ($this->pointOnVertex == true and $this->pointOnVertex($point, $vertices) == true) {
            return "vertex";
        }
 
        // Check if the point is inside the polygon or on the boundary
        $intersections = 0; 
        $vertices_count = count($vertices);
 
        for ($i=1; $i < $vertices_count; $i++) {
            $vertex1 = $vertices[$i-1]; 
            $vertex2 = $vertices[$i];
            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) { // Check if point is on an horizontal polygon boundary
                return "boundary";
            }
            if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) { 
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x']; 
                if ($xinters == $point['x']) { // Check if point is on the polygon boundary (other than horizontal)
                    return "boundary";
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                    $intersections++; 
                }
            } 
        } 
        // If the number of edges we passed through is odd, then it's in the polygon. 
        if ($intersections % 2 != 0) {
            return "inside";
        } else {
            return "outside";
        }
    }
 
    function pointOnVertex($point, $vertices) {
        foreach($vertices as $vertex) {
            if ($point == $vertex) {
                return true;
            }
        }
 
    }
 
    function pointStringToCoordinates($pointString) {
        $coordinates = explode(" ", $pointString);
        return array("x" => $coordinates[0], "y" => $coordinates[1]);
    }
	
	function pointToCoordinates($point) {
       
        return array("x" => $point[0], "y" => $point[1]);
    }
	
	
 
}
// get region
function get_region($point){
	
	$flora_region=array(1=>"EP",2=>"NL",3=>"YP",4=>"NW",5=>"NL",6=>"SE",7=>"NL",8=>"SL",9=>"FR",10=>"KI",11=>"NL",
12=>"MU");
	$string = file_get_contents("data/SAGovtRegions.geojson");
	$jsondata = json_decode($string, true);
	$pointLocation = new pointLocation();
	
	foreach($jsondata["LOCSA.GovernmentRegionsSA"]["features"] as $region){
	
		//$point=array(133.8186400, -28.945179);
		//$point=array(138.641195,-35.150782);//-34.8955878,138.5162018
		$polygon_point_arr=array();
		if($region["geometry"]["type"]=="Multipolygon"){
			$polygon_point_arr=$region["geometry"]["coordinates"][0];
		} else {
			$polygon_point_arr[]=$region["geometry"]["coordinates"][0];
		}
		
		
		$name=$region["properties"]["REGION"];
		//print_r($region["properties"]["OBJECTID"]);
		//array_push($polygon_point_arr,$polygon_point_arr[0]);
		//print_r($region["geometry"]);
		foreach($polygon_point_arr as $polygon_point){
			//echo "Point({$point[0]},{$point[1]}) ".$pointLocation->pointInPolygon($point, $polygon_point)." ".$name."<br\>\n";
			$result=$pointLocation->pointInPolygon($point, $polygon_point);
			if($result=="inside" || $result=="vertex"|| $result=="boundary"){
				return $flora_region[$region["properties"]["OBJECTID"]];
			}
		}
		
		
	}
	return "";
}

?>