<?php

/**
 * A Reservation, plus some operations.
	guests
	bags
	night staying
	room
	name of guest/reserver
	reserve room/x()
	checkout()
	read reservation()
	write reservation()
	delete reservation()
 */

class Reservation {
	public $room = 0;
	public $name = ''; // guest name to distiguish reses, esp for same room
	public $guests = 0; // redundant ?
	public $bags = 0;
	public $night = ''; // redundant ?

	public $state = FALSE;
	public $totalCharge = 0;

	const DataFile = "data/reservations.json";
	
	// Some useful state conditions
	protected $confirmed = FALSE;
	protected $mutex = FALSE;
	protected $completed = FALSE;
	protected $checkedin = FALSE;

function __construct($in) {
	$keys = ['night','room','guests','bags','name','totalCharge'];
	foreach ($keys as $k) {
		if (isset($in[$k]) )
			$this->$k = $in[$k];
	}
}

public function bookable($room, int $guests, int $bags) {
}

public function reserve($room, $name, int $bags, string $night='tonight') {
}

// Class function
// treat as a match operation against input array?
public function confirm(int $room, string $name, string $night="tonight") {
}

// Do the requested values make sense for a reservation
// have to use is_numeric rather than is_int because _GET values always set as strings
static function validate(array $in, array $required = []) {
	// is name a string, bags a non-zero int, checkin and checkout proper format
	foreach ($required as $key) {
		if (! array_key_exists($key, $in) )
			return FALSE;
	}
	
	if (array_key_exists('room',$in)) {
		if (! is_numeric($in['room']) )
			return FALSE;
		if ($in['room'] < 1)
			return FALSE;
	}
	if (array_key_exists('guests',$in)) {
		if (! is_numeric($in['guests']) )
			return FALSE;
		if ($in['guests'] < 1)
			return FALSE;
	}
	if (array_key_exists('bags', $in) ) {
		if (! is_numeric($in['bags']) )
			return FALSE;
		if ($in['bags'] < 0)
			return FALSE;
	}
	if (array_key_exists('name', $in) ) {
		if ($in['name'] == '')
			return FALSE;
		if (is_numeric($in['name']))
			return FALSE;
	}
	if (array_key_exists('night', $in) && $in['night'] != 'tonight' && $in['night'] != 'tmrw')
		return FALSE;
	
	return TRUE;
}

// this will only match agaist what is provided
public function find(array $in) {
	// bad data won't match against good data
	if (! Reservation::validate($in) )
		return FALSE;
	
	if (array_key_exists('room',$in)) {
		if ($in['room'] != $this->room)
			return FALSE;
	}
	if (array_key_exists('guests',$in)) {
		if ($in['guests'] != $this->guests)
			return FALSE;
	}
	if (array_key_exists('bags', $in) ) {
		if ($in['bags'] != $this->bags)
			return FALSE;
	}
	if (array_key_exists('name', $in) ) {
		if ($in['name'] != $this->name)
			return FALSE;
	}
	if (array_key_exists('night', $in) ) {
		if ($in['night'] != $this->night)
			return FALSE;
	}

	return TRUE;
}

public function computeCharge() {
}

public function usingSameRoom() {
}

}

?>
