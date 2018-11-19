<?php

include "roomClass.php";
/**
 * A Room with a Reservation, plus some operations.
	guests (num or array)
	storages (num or array)
	occupied
	cleaned
	checkin date
	checkout date
	available
	array/num of guests
	rack rate 
	cost per guest
	mutex
	reserve()
	checkout()
	clean()
	usestorage()
	emptystorage()
	availabletonight()
	can I book this room bases on res req()
	read state of the room()
	write state of the room()
	initialize room()
	lock/unlock room state()

 */

 // This is the state of the room for a given night
 // Includes how occupied the room is, whether it can be shared with a following reservation
class RoomNight extends Room {
	public $guests = 0;
	public $bags = 0;
	
	public $totalCharge = 0;

	public $checkin = '';
	public $checkout = '';
	
	// Some useful state conditions
	public $state = FALSE;
	protected $full = FALSE;
	public $reserved = FALSE;// error trying to access as protected
	protected $cleaning = FALSE;
	protected $mutex = FALSE;

function __construct(array $in) {
	parent::__construct($in);	// this was failing with an error, now it's fine. weird.
	$keys = ['guests','bags','night'];
	foreach ($keys as $k) {
		if (isset($in[$k]) )
			$this->$k = $in[$k];
	}
	if ($this->guests == $this->beds)
		$this->full = TRUE;
	// do i need to worry about setting the other states
}

public function bookable(int $guests = 0, int $bags = 0) {
	if ($this->full) 
		return FALSE;
	if ($guests > $this->beds)
		return FALSE;
	if ($guests + $this->guests > $this->beds)
		return FALSE;
	if ($bags > 0) {
		if ($bags + $this->bags > $this->storage)
			return FALSE;
	}
	return TRUE;
}

// Assumes a validated reservation and available space
public function reserve(int $guests = 0, int $bags = 0) { // $reservation ?
	$this->guests += $guests;
	$this->bags   += $bags;
	$this->full   = ($this->guests == $this->beds);
	if ($this->guests)
		$this->reserved = TRUE;
	else
		$this->reserved = FALSE;

	return $this->reserved; // ?
}

// Cost per the specification doc:
// (room cost / number of guests ) + (base storage costs * number of items stored).
public function costPerGuest() {
	if ($this->guests == 0)
		return 0; // avoid div by 0 below
	return $this->roomRate / $this->guests + $this->storageRate * $this->bags;
}

public function totalCost() {
	return $this->costPerGuest * $this->guests;
}


}

?>
