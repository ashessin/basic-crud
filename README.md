# CRUD

## Setup

1. Start
   - Apache HTTP server
   - MySQL Database server
2. Use correct MySQL user account
   - make sure username `root` has password `root`
   - alternatively, you may change the username and/or password in the `public/setup.php` PHP file
3. navigate to [`http://localhost:$PORT/index.php`](http://localhost:8080/index.php)

## Features

1. Read, Update, Delete, Insert can be done from single page for multiple records simultaneously.
2. Basic validations using form elements.
3. Text search and sort.
4. Foreign key Constraint satisfaction on update, delete, insert.
5. Navigation bar with active link indication.