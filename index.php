<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

header("Content-Type: application/json; charset=UTF-8");

include_once "innClass.php";
include_once "reservationClass.php";

$method = $_SERVER['REQUEST_METHOD'];
$request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));

switch ($method) {
  case 'GET':
    process_gets($request);
    break;
  case 'POST':
    process_posts($request);
    break;
  default:
    unsupported($request);
    break;
}

/* The GET request should contain an action (request or confirm) along with basic inputs to complete the action
	request is expecting number of guests, number of bags and optional night (defaults to tonight)
	confirm is expecting a room number and guest name and optional night (default to tonight)
*/
function process_gets($request) {

	if (empty($_GET)) {
		echo json_encode(array("message" => "Inn reservations expect parameters of action (request or confirm). ".
			"Requests also requires guests and bags and optional night. ".
			"Confirm needs a name and room number and optional night."));
		return 1;
	}

	$action = isset($_GET['action']) ? $_GET['action'] : NULL;
	if (empty($action)) { // missing argument, so stop now
	    // set response code - 400 Bad request
		http_response_code(400);
 		echo json_encode(array("message" => "Missing action in URI (request or confirm)"));
		return 0;
	}
	elseif (! in_array($action, ["request", "confirm", "cleaning"]) ) {
		http_response_code(400);
		echo json_encode(array("message" => "Invalid action in URI (expecting request, confirm, cleaning)"));
		return 0;
	}
	else
		$response = array("message" => "action sent was $action");

	// Have a valid action, now check the rest of input
	if ($action == "request") {
		if (! Reservation::validate($_GET, ['guests','bags']) ) {
			http_response_code(400);
			$response['message'] = "Required parameters for request are guests and bags";
			$response['data'] = $_GET; // debug
			echo json_encode($response);
			return 0;
		}
		$inn = new Inn;
		// don't use extract $_GET, not sure what's in it, could overwrite something important
		$guests = (int)$_GET['guests'];
		$bags = (int)$_GET['bags'];
		$night = isset($_GET['night']) ? $_GET['night'] : 'tonight';
		
		$room = $inn->availableRoom($guests, $bags, $night);
		if ($room) {
			$response['room'] = $room;
			$response['message'] = "Room $room is available for $guests guests and $bags bags";
		}
		else {
			http_response_code(404); // best simulator of unavailable resource
			$response['message'] = "That reservation request is not available. Please try another.";
		}
		echo json_encode($response);
		return;
	}
	elseif ($action == 'confirm') {
		if (! Reservation::validate($_GET, ['room','name']) ) {
			$response['message'] = "Required parameters for request are room and guest name";
			echo json_encode($response);
			return 0;
		}
		
		$_GET['night'] = isset($_GET['night']) ? $_GET['night'] : 'tonight';
		$inn = new Inn;
		$conf = $inn->confirmReservation($_GET);
		if (is_bool($conf)) { // false
			http_response_code(400); // 404?
			$response['message'] = "No matching reservation found :(";
		} else { // object
			$response['message'] = "Matching reservation found!";
			$response['reservation'] = $conf; // optional?
		}
	}
	elseif ($action == 'cleaning') {
		$inn = new Inn;
		$time = $inn->cleaningTime();
		$response['cleaningTime'] = $time / 60;
		$response['message'] = "It will take ". $time / 60 ." hours to clean the inn tomorrow. ";
		if ($inn->cleaningFinish())
			$response['message'] .= "This is enough time to clean all the rooms.";
		else
			$response['message'] .= "This is not enough time to clean all the rooms!";
	}
	
	echo json_encode($response);
	return 1;
}

function process_posts($request) {
	// get posted data
	$data = json_decode(file_get_contents("php://input"),1	);
	//var_dump($data);
	if (empty($data)) {
		echo json_encode(array("message" => "Inn reservations expect parameters of action (reserve). ".
				"Reserve requires room, name, guests and bags and optional night.") );
		return 1;
	}
	$action = isset($data['action']) ? $data['action'] : NULL;
	if (empty($action)) { // missing argument, so stop now
	    // set response code - 400 Bad request
		http_response_code(400);
 		echo json_encode(array("message" => "Missing action in URI POST (reserve)"));
		return 0;
	}
	else
		$response = array("message" => "action sent was $action");

	if ($action == "reserve") {
		// validate input
		if (! Reservation::validate($data, ['guests','bags','name','room']) ) {
			http_response_code(400);
			$response['message'] = "Required parameters for reservation action are room, name, guests and bags";
			echo json_encode($response);
			return 0;
		}
		// delay creating inn object until basic validation done
		$inn = new Inn;
		$data['night'] = isset($data['night']) ? $data['night'] : 'tonight';
		
		if ($inn->isRoomAvailable($data['room'], $data['night']) ) {
			$res = $inn->bookRoom($data);
			if ($res) {
				http_response_code(201); // or 202
				$response['message'] = "Room $res->room has been reserved by $res->name for $res->guests guests and $res->bags bags.";
				$response['reservation'] = $res;
			}
			else {
				// later may want to refine why the reservation failed
				http_response_code(409); // conflict with current reserverations
				$response['message'] = "Reservation not completed. Please check for current availability.";
			}
		} else {
			http_response_code(409); // maybe a bad room number, or room is booked
			$response['message'] = "That room is not available. Please check for current availability.";
		}
		echo json_encode($response);
		return 1;
	}

	elseif ($action == "clear") {
		if (empty($data['admin'])) {
			http_response_code(400);
			echo json_encode(array("message" => "Invalid action in URI POST (expecting reserve)"));
			return 0;
		}
		$inn = new Inn;
		$inn->clear();
		$response = array("message" => "Inn reservations cleared.");
		echo json_encode($response);
	}

	return 1;
}

function unsupported($request) {
	http_response_code(405);
	echo json_encode(array("message" => "Sorry Dave, I can't do that."));
}


return 1;
?>

