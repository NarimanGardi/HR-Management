# HR Management API

## Description

HR Management API is a RESTful web service that provides       functionality for managing employee data in an organization. The API supports CRUD (Create, Read, Update, Delete) operations for       employees, as well as authentication and authorization via JSON Web  Tokens (JWTs). Other features include searching for employees by     name, exporting all employee data to a CSV file, importing employee  data from a CSV file, and retrieving logs of actions taken on       employee data. The API is designed to be consumed by client       applications that need to integrate employee management functionality    into their workflow.

## Dependencies

-   PHP >= 8.1
-   Laravel/Framework: ^10.0

## Installation

*  Clone the repository: 
    ```
	git init
	git clone https://github.com/NarimanGardi/HR-Management-API
	```
    
* Install the dependencies: 
    ```
    composer install
    ```
    
* Create a new `.env` file by copying the `.env.example` file: 
    ```
    cp .env.example .env
    ```
    
*  Update the `DB_` variables in the `.env` file with your database credentials.
    
* Generate a new application key: 
    ```
    php artisan key:generate
    ```

* Link storage folder to public: 
    ```
    php artisan storage:link
    ```
    
*  Migrate the database: 
    ```
    php artisan migrate --seed
    ```
    
*  Serve the application: 
    ```
    php artisan serve
    ```
    

## Deploy Project with Nginx
*  Set up a server: you can use any cloud service provider such as Amazon Web Services, DigitalOcean, or Google Cloud Platform.

* Install Nginx.
```
sudo apt-get  update
sudo apt-get install nginx
```

* Configure Nginx. Open the Nginx configuration file located at `/etc/nginx/sites-available/default` and modify it as follows:
```php
server { 
		listen 80; 
		server_name example.com; 
		root /var/www/html/example.com/public; 
		
		index index.html index.htm index.php;
		
		location / { 
			try_files $uri  $uri/ /index.php?$query_string; 
		}
		
		location ~ \.php$ {
			include fastcgi_params; 
			fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
			fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
			fastcgi_param PATH_INFO $fastcgi_path_info;
		} 
}
```

* Install TLS certificate:
```sql
sudo apt-get install certbot python3-certbot-nginx 
sudo certbot --nginx -d example.com -d www.example.com
```

* Restart Nginx:
```sql
sudo systemctl restart nginx
```

Your Laravel application is now deployed with Nginx and TLS. You can access it by visiting your domain name in a web browser.


## API Documentation

You can find the documentation for our API [here](https://documenter.getpostman.com/view/22853456/2s93RUvXa8). The documentation provides detailed information on how to use each endpoint, as well as examples of request and response payloads.

## Unit Testing

To run the tests, open a terminal window in the root directory of the project and enter the following command:

`php artisan test` 

This will execute all of the tests in the `/tests/Feature` directory and generate a report of the results. If any tests fail, the report will indicate which tests failed and provide details on the nature of the failure.