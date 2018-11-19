<?php

include_once "roomNightClass.php";
include_once "reservationClass.php";
include_once "gnomeSquadClass.php";

/* the Inn, plus some operations.
 	array of Rooms
	array of Reservations
	default room rate
	default storage rate
	mutex
	what's available now()
	what's available tonight()
	what's available for date()
	is room x available()
	book a room()
	book room x()
	confirm room x()
	checkout room x()
	read state of the inn()
	write state of the inn()
	lock state of the inn()
	initialize inn()
	best room choice()
 */

class Inn {
	public $numRooms = 0;
	public $rooms = array();
	public $roomNights = array(); // for tonight and tomorrow
	public $reservations = array();  // for tonight and tomorrow
	public $roomRate = 20;
	public $storageRate = 2;
	
	public $totalBookings = 0;
	protected $maxGuests = 0;
	
	// Some useful state conditions
	public $state = FALSE;
	protected $vacancy = array('tonight' => TRUE, 'tmrw' => TRUE); 
	protected $cleaning = FALSE; // today
	protected $checkout = FALSE;
	protected $mutex = FALSE;

public function __construct() {
	$debug = 0;
	$jsonData = file_get_contents(Room::DataFile);
	if ($debug) var_dump($jsonData); 
	//print "<br/><br/>";
	$array = json_decode($jsonData, true);
	if ($debug) var_dump($array); 

	$this->numRooms = count($array);
	$this->maxGuests = 0;
	$this->roomNights["tonight"] = array();
	$this->roomNights["tmrw"]    = array();

	foreach ($array as $n => $data) {
		// These are defaults, may be overridden by config file
		// The ?? operator isn't working!!!
		if (empty($data['roomRate']))
			$data['roomRate'] = $this->roomRate;
		if (empty($data['storageRate']))
			$data['storageRate'] = $this->storageRate;
		if (empty($data['number']))
			$data['number'] = $n;
		if ($data['beds'] > $this->maxGuests)
			$this->maxGuests = $data['beds'];

		$this->rooms[$n] = new Room($data);
		// fill in default room night data
		$this->roomNights["tonight"][$n] = new RoomNight($data);
		$this->roomNights["tmrw"][$n]    = new RoomNight($data);
	}
}

public function availableRoom(int $guests, int $bags, string $night="tonight") {
	if ($guests > $this->maxGuests)
		return 0;
	if (! $this->vacancy[$night]) 
		return 0;

	$room = 0;
	// find a room based on bags and guests
	foreach ($this->roomNights[$night] as $rn) {
		if ($rn->bookable($guests, $bags) )
			return $rn->number;
	}
	return $room;
}


// wants a reservation array or object
public function bookRoom(array $res) {
	// validate reservation
	if (empty($res['night']))
		$res['night'] = 'tonight';
	if (!Reservation::validate($res, ['guests','room','name','bags','night']) )
		return FALSE;
	if ($res['room'] > $this->numRooms)
		return FALSE;
	if ($res['guests'] > $this->maxGuests)
		return FALSE;
	
	// determine vacancy
	// stuff can happen b/t availability an dbooking
	if (! $this->vacancy[$res['night']] )
		return FALSE;
	// set mutex?
	$rmNight = $this->roomNights[$res['night']][$res['room']];
	if (! $rmNight->bookable($res['guests'], $res['bags']) )
		return FALSE;
	// book the room
	// figure out cost per guest
	$rmNight->reserve($res['guests'], $res['bags']);
	$res['totalCharge'] = $rmNight->costPerGuest();
	$res = new Reservation($res);
	array_push($this->reservations, $res);
	$this->saveReservations();

	return $res;
}

public function isRoomAvailable(int $room, string $night) { // do I need rest of res data?
	if ($room > $this->numRooms)
		return FALSE;
	if ($this->roomNights[$night][$room]->available )
		return TRUE;

	return FALSE;
}

// return true if such a reservation exists, false otherwise
public function confirmReservation(array $res) { // could be reservation object
	if ($res['room'] > $this->numRooms)
		return FALSE;
	// find this in the reservations list. 
	foreach ($this->reservations as $reservation) {
		if ($reservation->find($res) )
			return $reservation;
	}
	return FALSE;
}

// protected?
public function computeVacancy() {
	$vac = FALSE;
	foreach ($rooms['tonight'] as $rm) {
		if (! $rm->full)
			$vac = TRUE;
	}
	$this->vacancy['tonight'] = $vac;
	$vac = FALSE;
	foreach ($rooms['tmrw'] as $rm) {
		if (! $rm->full )
			$vac = TRUE;
	}
	$this->vacancy['tmrw'] = $vac;
}

public function calculateBilling(string $night="tonight") {
	$total = 0;
	foreach ($this->roomNights[$night] as $rm) {
		$total += $rm->totalCost();
	}
	return $total;
}

public function cleaningTime() {
}

// Reservation utility operations

// must be done after rooms are configued
public function applyReservations() {
}

public function saveReservations() {
	$data = json_encode($this->reservations); //?
	if (file_put_contents(Reservation::DataFile, $data, LOCK_EX) )
		return TRUE;
	else
		return FALSE; // raise error ?
}

// this maay need some work.
public function readReservations() {
	$this->reservations = array();
	if ($jsonData = file_get_contents(Reservation::DataFile) ) {
		$reslist = json_decode($jsonData, true);	// an array of objects
		foreach ($reslist as $r) { // build obj list
			$res = new Reservation($r);
			array_push($this->reservations, $res);
		}
		return TRUE;
	}
	else
		return FALSE; // raise error ?
}


}

?>
