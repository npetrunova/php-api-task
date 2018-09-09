# Solution for backend developer task

For this solution I have used the Laravel 5.6 framework and MySQL database. This solution contains multiple API endpoints that will be described in more detail further down.


## Setup Instructions

### Technical Requirements

The solution is written in Laravel 5.6 and has the following technical dependencies:
-   PHP >= 7.1.3
-   PDO PHP Extension
-   Mbstring PHP Extension
-   Tokenizer PHP Extension
-   XML PHP Extension
-   Ctype PHP Extension
-   JSON PHP Extension
-   MySQL/MariaDB database
-   Composer

### Setup

- Checkout repository to your root folder.
- After checking out repository configure your web server's document / web root to be the `public` directory.
- Create a new MySQL/MariaDB database for the solution on your server.
- Rename `.env.example` file to `.env` and change DB connection settings within the file.
- Change `APP_URL` to the correct URL on your server.
- Run command `composer install` to install all required dependencies.
- Run command `php artisan migrate` to create required DB tables.
- Run command `php artisan db:seed` to seed the database with test data.

## API

This API contains endpoints for manipulating data on fields and subscribers.

### Fields API

- **GET** `http://yoururl.dev/api/getFields`- will retrieve all existing fields. No parameters needed.
- **GET** `http://yoururl.dev/api/getField/{id}`- will retrieve a single entry of a field. 
	- Parameters:
	   -  id - field id (primary key).
 - **DELETE** `http://yoururl.dev/api/deleteField/{id}`- will delete a single entry of a field. 
	 - Parameters:
	   -  id - field id (primary key).
 - **POST** `http://yoururl.dev/api/createField` - will create a new field. 
	 - Required headers:
		 - `Accept: application/json`
	 - Body(application/json): 
	 ``` 
	  {
	  "title": "Field title",
	  "type":"string"
	  }
	```
	Valid field types are: `date, number, boolean, string`.