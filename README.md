# hoteldemo
Simple POC for Gilded Rose Inn.
This is a very "version 1" implementation.

## Configuration

Copy files to webserver running PHP 7 (I developed this with apache). Server needs read/write access to 'data' subdirectory.

Application has a RESTful API exposed via index.php. It accepts GET and POST requests.
The API expects an 'action' parameter and usually one or more other parameters, some required, some optional.
Making a GET or POST request with no arguments will return a very basic usage notice. A more complete description of the API will be in this ReadMe.

## Q&A
###	At a high level, how does your system work? 
###	How would we extend your system if we had to add more rooms, more business logic constraints, more gnomes?
###	What documentation, websites, papers, etc. did you consult for this assignment?
###	What third-party libraries or other tools does your application use? How did you choose each library or framework you used?
###	How long did you spend on this exercise? If you had unlimited time to spend on this, how would you spend it and how would you prioritize each item? 
###	If you were going to implement a level of automated testing to prepare this for a production environment, how would you go about doing so?
