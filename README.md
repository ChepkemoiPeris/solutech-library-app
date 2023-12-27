<h2>How to Run</h2>

## Requirements

* Xampp
* Composer
* Node and npm

## **Steps to run Laravel Backend**

## Step 1:

```
git clone https://github.com/ChepkemoiPeris/solutech-library-app.git
```

```
cd solutech-library-app/api
```

## Step 2:

* ### Copy .env.example file to .env on the "api" folder.  

* ### Open XAMPP and start Apache server and MySQL server

* ### Open phpMyAdmin and create a database called "library"

* ### Open your .env file and change the database name (DB_DATABASE) to "library"

## Step 3:

```
composer install
```
 
```
php artisan migrate:fresh --seed
```

```
php artisan serve
```

<hr/>

## **Steps to run Vue.js Frontend**

## Step 1:

* ### Run Laravel api Application
* ### Open another terminal

## Step 2:

```
cd solutech-library-app/frontend
```

```
npm install
```
 
```
npm run dev
```

* ### Open your browser and type *http://localhost:3000/* OR *http://127.0.0.1:3000/*

## Sample login credentials:

# Admin login credentials:
* Email : admin@admin.com
* Password : admin123

# User login credentials:
* Email : user@user.com
* Password : 123456

 






