# Translation Management Service


In json export i have used lazy loading to reduce resource usage while iterating 100k+ records and then compressed it to reduce network resource usage so it will stay under 100ms and i am updating it using model events i could use observer but just to keep readability in mind i have added it directly in model to update json export file so customer will always get cached compressed updated file.

## Prerequisites

- PHP >= 8.2
- MySQL Server
- Composer >= 2.0
- OR
# Run as administrator...
- If Windows Run 
```bash
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.4'))
```

  
- If Linux
```bash
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)
```
- If Max OS
```bash
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.4)
```

## Installation

### 1. Clone Repository

```bash
git clone https://github.com/hamzaaslam-cs/Translation-Management-Service.git
cd Translation-Management-Service
```

### 2. Non-Docker Installation

#### 2.1 Install Dependencies
```bash
composer install
```

#### 2.2 Environment Setup
1. Create environment file:
```bash
cp .env.example .env
```

2. Configure your `.env` file with the following settings:

##### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3307
DB_DATABASE=translation_management_service
DB_USERNAME=root
DB_PASSWORD=password
```

##### Mail Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=hostname
MAIL_PORT=2525
MAIL_USERNAME=username
MAIL_PASSWORD=password
```


#### 2.3 Database Setup
1. Run migrations:
```bash
php artisan migrate
```

2. Seed the database:
```bash
php artisan db:seed
```
3. To run tests
```bash
php artisan test
````
4To run tests
```bash
php artisan key:generate
````



### 3. Docker Installation

# Start the containers

```bash
docker-compose up -d
````
# Wait a few seconds for MySQL to initialize, then run migrations
```bash
docker-compose exec app php artisan migrate
````

## Additional Notes

- Make sure your MySQL server is running before running migrations
- Ensure all required PHP extensions are installed


Check Swagger Documentation Visit:
http://127.0.0.1:8000/api/documentation

## Support

If you encounter any issues or need assistance, please open an issue in the repository.

## License

[License information here]
