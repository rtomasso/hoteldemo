# hoteldemo
Simple POC for Gilded Rose Inn.
This is a very "version 1" implementation.

## Configuration

Checkout these files to webserver running PHP 7 (I developed this with apache). Server needs read/write access to 'data' subdirectory.

Application has a RESTful API exposed via index.php. It accepts GET and POST requests.
The API expects an 'action' parameter and usually one or more other parameters, some required, some optional.
Making a GET or POST request with no arguments will return a very basic usage notice. A more complete description of the API will be in this ReadMe.

## Q&A
###	At a high level, how does your system work?

*Front End*

This demo has a single URI to access the API, at index.php. Every request has to include an 'action' parameter to tell the API what you want to do. You can get a summary of available actions with an empty request. The API returns json data, and expects the POST body to be in json format.

The available GET actions are: vacancy, confirm, and cleaning.

* vacancy is how you request if a desired reservation is available. It requires two parameters, guests and bags, both integers, and has an optional parameter, night. It will return a room number and a message on success.

* confirm is how you confirm a reservation. It requires a guest name and a room number. Optional parameters are number of guests or bags and the night of the reservation. It will return a reservation object on success.

* cleaning will return the cleaning schedule for the next morning. It has no other parameters.

GET actions that find something return a 2xx code. GET actions that don't match will return a 4xx code.

The available POST actions are: reserve and clear.

* reserve is how you make a reseveration at the Inn. Required parameters are the room number, guests, and bags. There is optional night parameter. It will return the reservation detail on success.

* clear is an administrative command to remove all the reservations from the Inn. It needs an admin parameter to avoid accidental removal of all the reservations. It returns a message on success.

POST actions that do something return a 2xx code. POST actions that don't work will return a 4xx code.

In all cases the 'night' parameter defaults to 'tonight'. The other valid value is 'tmrw' (for tomorrow night). If you want to reference something for tomorrow night, you will need to specify the night parameter.

*Back End*

All the interaction with the API is done through the Inn class. There are other classes to handle the rooms, reservations and the cleaning gnomes. The current state of the Inn is stored in local json files. Every new reservation updates these files.

The Inn is a container of Rooms. These Rooms may be reserved for a given night by one or more guests and their luggage. These reservations are stored as RoomNights. This demo only uses "tonight" and "tmrw" as the available nights, but this basic logic could be extended to more nights.

When a reservation request is made, the list of available rooms for that night are checked, and the room most suitable for the guest(s) is selected. If a request is too "big" for the room, then the call fails and the user is prompted to check the availability of rooms.

The classes have basic validity checking, but it is by no means production code. It will prevent things like booking a room that doesn't exist or jamming 3 bags into a room with no storage.

###	How would we extend your system if we had to add more rooms, more business logic constraints, more gnomes?

More rooms can be added by updating the rooms.json file.

More gnomes are not needed unless there are more rooms or rooms can hold more guests. If there were multipe gnome crews, then there'd need to be gnome objects created and some logic to divide the cleaning duties made. Could be a simple round robin or crew 1 gets odd rooms and crew 2 gets even rooms or something else. If it was possible not to clean all the rooms in one shift, then some logic would be needed to know what room(s) would be unavailable to reserve for that night. That could change as new reservations come in. You could potentially find weird edge cases like a shared room becoming unavailable.

More business logic could be fitted into the booking selection. There are dozens of potential rules that could be made for how and when to allow reseervations. Not to mention the ability to cancel a reservation. I don't know if you'd need a Rules class, but I could see extending the Room types to cover a lot of this. Perhaps some 2-bed rooms might only allow couples to book, or sharable rooms have to hold guests of the same gender. Some rooms might have different base charges, that would require no extra work as I've built this, just define the different rate. A room could have a minimum or maximum booking range. At the minimum there would be new functions to manage these rules. This would likely be easier with a database to narrow the initial room selection query.

New business logic of any complexity would likely require more datapoints to support it. That's a redesign of the core classes and future schema.

###	What documentation, websites, papers, etc. did you consult for this assignment?

I looked up examples of HTTP Status Codes for the responses, particularly the errors. 

I did check a few RESTful systems for examples of responses and codes and parameter format.

I did check the PHP and MYSQL manual whenever my system did something unexpected, which was far too often. Something kept breaking my MAMP install.

By happy chance I caught an interview with one of the early pioneers of REST so got some insight how it's "supposed to work". It was a good reminder that GET makes no changes and is idempotent. And POST is used to create new things and should not create the same thing twice. After that I read the wikipedia article on REST.

###	What third-party libraries or other tools does your application use? How did you choose each library or framework you used?

I decided not to use any. Not because some aren't useful, but that seemed an added complexity for setup and configuation. Plus I kept having problems with my MAMP installation and didn't want another thing to break while I developed this. There may have been packages that could have easily handled this, but I personally found it more valuable to hand-roll this.

###	How long did you spend on this exercise? If you had unlimited time to spend on this, how would you spend it and how would you prioritize each item? 

Actual coding was probably in the 6-8 hour range. I ran into several just bizarre prolems with apache, php and git that derailed my work for a time, so I can't give any more accurate estimate.

If I had unlimited time on this, or even a standard 2-3 week sprint, the final result would be implented differently.

First I would use a database rather than json files to store the data, especially if this were going out into the real world. So I'd likely start with brainstorming the various data objects I would need and if those objects were persistent, develop their schema and relationships.

Then I'd brainstoem the various user stories, or at least likely actions this system would provide in the API. That would get me a set of functionality that had to be exposed and handled. This would also refine the data model. With this I could add a specification for the API to the design document.

I'd ask if someone was available to review the design to make sure I didn't miss anything and that it made sense to other people. Once that was done I'd likely start roughing out the classes and initialization data. I'd build the sanity test "script" so I would know when I was done with the handlers for the various API calls.

At this point the coding should go fairly smoothly, along with updates to the design documents. No plan is perfect so the API could be tweaked based on how the code was behaving and perhaps finding ways to unify the parameters so the whole API felt more consistent. There could also be tweaks to the schema like adding a foreign key or a mapping table to make a common search easier, for example.

###	If you were going to implement a level of automated testing to prepare this for a production environment, how would you go about doing so?

Create a script to execuate a series of curl commands and check against the expected output.

Create different rooms.json files with a corresponsing set of test actions to make sure other configurations worked.

Write a whole bunch of negative tests.

Pick a programming language with a REST client library and build up all sorts of requests and examine the response data in a programmatic way. There should be several places where "input==output" checks can be made. Not to mention systematic tests like keep having one guest reserve a shared room until it is full and make sure it is the correct number of iterations. Program could generate random data to test input validation. Validating the returned JSON data woudl be fairly automatic.

With access to the number of rooms and their public traits, those could drive a program to build queries to test various bounds and known good and bad requests.
