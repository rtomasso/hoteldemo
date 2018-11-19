# hoteldemo
Simple POC for Gilded Rose Inn.
This is a very "version 1" implementation.

## Configuration

Chckout these files to webserver running PHP 7 (I developed this with apache). Server needs read/write access to 'data' subdirectory.

Application has a RESTful API exposed via index.php. It accepts GET and POST requests.
The API expects an 'action' parameter and usually one or more other parameters, some required, some optional.
Making a GET or POST request with no arguments will return a very basic usage notice. A more complete description of the API will be in this ReadMe.

## Q&A
###	At a high level, how does your system work? 
###	How would we extend your system if we had to add more rooms, more business logic constraints, more gnomes?

More rooms can be added by updating the rooms.json file.

###	What documentation, websites, papers, etc. did you consult for this assignment?

I looked up examples of HTTP Status Codes for the responses, particularly the errors. 


###	What third-party libraries or other tools does your application use? How did you choose each library or framework you used?

I decided not to use any. Not because some aren't useful, but that seemed an added complexity for setup and configuation. Plus I kept having problems with my MAMP installation and didn't want another thing to break while I developed this.

###	How long did you spend on this exercise? If you had unlimited time to spend on this, how would you spend it and how would you prioritize each item? 

Actual coding was probably in the 6-8 hour range. I ran into several just bizarre prolems with apache, php and git that derailed my work for a time, so I can't give any more accurate estimate.

If I had unlimited time on this, or even a standard 2-3 week sprint, the final result would be different.

First I would use a database rather than json files to store the data, especially if this were going out into the real world. So I'd likely start with brainstorming the various data objects I would need and if those objects were persistent, develop their schema and relationships.

Then I'd brainstoem the various user stories, or at least likely actions this system would provide in the API. That would get me a set of functionality that had to be exposed and handled. This would also refine the data model. With this I could add a specification for the API to the design document.

I'd ask if someone was available to review the design to make sure I didn't miss anything and that it made sense to other people. Once that was done I'd likely start roughing out the classes and initialization data. I'd build the sanity test "script" so I would know when I was done with the handlers for the various API calls.

At this point the coding should go fairly smoothly, along with updates to the design documents. No plan is perfect so the API could be tweaked based on how the code was behaving and perhaps finding ways to unify the parameters so the whole API felt more consistent. There could also be tweaks to the schema like adding a foreign key or a mapping table to make a common search easier, for example.

###	If you were going to implement a level of automated testing to prepare this for a production environment, how would you go about doing so?

Create a script to execuate a series of curl commands and check against the expected output.

Create different rooms.json files with a corresponsing set of test actions to make sure other configurations worked.

Write a whole bunch of negative tests.

Pick a programming language with a REST client library and build up all sorts of requests and examine the response data in a programmatic way. There should be several places where "input==output" checks can be made.
