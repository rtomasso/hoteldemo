<?php

/**
 * The Gnome Cleaning Squad, plus some operations.
 	cleaning
	how many rooms to clean()
	how long will it take to clean()
	when will cleaning be done()
	how long to clean this room()
	list of room cleaning order()
	// staggered checkout times would make this interesting
	Since even at maximum capacity all the rooms can be cleaned within 8 hours, this can be optimized to yes.
	How long it will take to clean is today's checkouts and the factors of those rooms that become empty.
	Will assume everyone checks out or modify the room to 1 occupant and therefore not cleaned today.
	Will have a checkin option of tomorrow just to simulate this calculation.
 */

class GnomeSquad {
	// Times in minutes to avoid floating point math
	const TimePerRoom = 60;
	const TimePerGuest = 30;
	// const TimePerStorage = 0; // thinking ahead
	const MaxTimePerShift = 60 * 8; // 8 hours

	/* Some useful state conditions for future refinements
	protected $rooms = 0;
	protected $cleaning = FALSE;
	protected $mutex = FALSE;
	protected $currentRoom = 0;
	*/

// The cleaning squad needs ONE hour per room plus THIRTY minutes per person in the room to clean it.
// Expects an array of roomNight objects
// Returns time in minutes
static function totalCleaningTime(array $roomNs) { // roomnights
	$time = 0;
	$rooms = 0;
	foreach ($roomNs as $rm) {
		$rooms++;
		if ($rm->guests == 0)
			continue;
		$time += self::TimePerRoom;
		$time += $rm->guests * self::TimePerGuest;
	}
	return $time;
}

// Returns time in minutes
public function roomCleaningTime($room) { // roomnight
	$time = 0;
	if ($room->{guests} == 0)
		return 0;
	$time += $this->timePerRoom;
	$time += $room->guests * $this->timePerGuest;

	return $time;
}

// Returns true/false whether this crew will finish the cleaning by end of shift
static function finishAllRooms(array $roomNs) {
	$time = self::totalCleaningTime($roomNs);
	return ($time < self::MaxTimePerShift);
}


}

?>
