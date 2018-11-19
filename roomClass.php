<?php

/**
 * A Room at the Inn. The basic traits of a physical room.
	beds (num or array)
	storages (num or array)
	cleaned
	checkin time
	checkout time
	available
	rack rate (may come from Inn)
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

class Room {
	public $number = 0; // Room number on the door. Zero means no data in the room yet
	public $beds = 0;
	public $storage = 0;
	public $roomRate = 20;   // default for this project
	public $storageRate = 2; // ""
	
	// Some useful state conditions
	protected $shareable = TRUE;
	protected $available = TRUE;
	protected $cleaning = FALSE;
	protected $mutex = FALSE;
	
	// Where the base data for all the rooms is stored
	const DataFile = "data/rooms.json";
	

public function __construct(array $in) {
	// need some sort of validation here???
	$keys = ['number','beds','storage','roomRate','storageRate'];
	foreach ($keys as $k) {
		if (isset($in[$k]) )
			$this->$k = $in[$k];
	}
}

}

?>
