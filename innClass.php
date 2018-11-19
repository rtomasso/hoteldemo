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
	}
}

public function availableRoom(int $guests, int $bags, string $night="tonight") {
	if ($guests > $this->maxGuests)
		return 0;
	if (! $this->vacancy[$night]) 
		return 0;
	
	$room = 1;
	// need to find best room 
	return $room;
}


// wants a reservation array or object
public function bookRoom(array $res) {
	// validate reservation
	// determine vacancy
	// book the room
	// figure out cost per guest
	return $res;
}

public function isRoomAvailable(int $room) { // do I need rest of res data?
	if ($room > $this->numRooms)
		return FALSE;
	if ($this->rooms[$night][$room]->available )
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
	foreach ($rooms["tonight"] as $rm) {
		if (! $rm->full)
			$vac = TRUE;
	}
	$this->vacancy['tonight'] = $vac;
	$vac = FALSE;
	foreach ($rooms["tmrw"] as $rm) {
		if (! $rm->full )
			$vac = TRUE;
	}
	$this->vacancy['tmrw'] = $vac;
}

public function calculateBilling(string $night="tonight") {
	$total = 0;
	foreach ($rooms[$night] as $rm) {
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
}

public function readReservations() {
}


}

?>
