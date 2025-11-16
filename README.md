# Blogtopia

A simple PHP & MySQL blogging platform with a custom routing system.

## Overview

Blogtopia is a lightweight blog application built with vanilla PHP (no frameworks) and MySQL.
It features both public user-facing routes and an admin panel for managing posts, categories, comments and users.

Technologies used:

* PHP
* MySQL
* CSS (basic styling)
* Apache with mod_rewrite (for clean URLs)

## Features

### Public (User) Site

* View all blog posts on home page (`/`, `/home`, or `/blog`)
* View individual posts via `/post/{id}`
* View posts by category
* User registration & login
* Logout
* Clean, readable URLs

### Admin Panel

* Dashboard (`/admin` or `/admin/dashboard`)
* Manage posts: list, add (`/admin/addpost`), edit (`/admin/editpost/{id}`)
* Manage categories (`/admin/categories`)
* Manage comments (`/admin/comments`)
* Manage users (`/admin/users`)
* Authentication & role-based access control (admin vs regular user)

## Project Structure

```
├── Admin/          # Admin panel routes  
│   ├── Actions/    # Admin action handlers  
│   └── …  
├── Auth/           # Authentication routes  
├── User/           # User/public site routes  
│   ├── Actions/    # User action handlers  
│   └── …  
└── Config/         # Configuration files  
    └── router.php  # Custom router implementation  
```

## Routing System

All incoming HTTP requests are routed through `index.php`, which delegates routing logic to `Config/router.php`.
The system supports:

* URI patterns with parameters (e.g., `/post/{id}`)
* Middleware protections:

  * `auth`: requires user to be logged in
  * `admin`: requires an admin role
  * `guest`: requires no user logged in
* Helper functions for URLs and redirects (via `Config/url_helper.php`)

  ```php
  echo url('admin/posts');       // → /admin/posts  
  echo postUrl(1);               // → /post/1  
  redirect('post/5', ['action'=>'view']);  
  ```

### Enabling clean URLs

Make sure your Apache server has `mod_rewrite` enabled. The `.htaccess` file in the project root rewrites all requests to `index.php`.
If you deploy in a sub-directory, update the base path in `Config/router.php`, e.g.:

```php
$router = new Router('/subdirectory');
```

## Installation / Setup

1. Clone this repo:

   ```bash
   git clone https://github.com/M-Eldeeb-Dev/blogtopia-project.git
   ```
2. Create a MySQL database (e.g., `blogtopia_db`)
3. Import `blog.sql` to define the tables and seed initial data
4. Configure your database connection settings in `Config/…` (you’ll need to locate the DB config file)
5. Ensure your web server’s document root points to the project root and has mod_rewrite enabled
6. Adjust file permissions for uploads directory, if needed (e.g., `Uploads/`)
7. Visit `http://your-server/` in your browser to verify

## Usage

* Browse the blog posts via the home page
* Register a user account or login
* As an admin user, go to `/admin` to manage posts, categories, comments and users
* Add new posts, edit existing ones, moderate comments or manage site users

## Contributing

Feel free to fork the project and submit pull requests.
Suggested improvements:

* Add pagination for post lists
* Add image uploads per post
* Implement WYSIWYG editor for post content
* Add an API layer (REST or GraphQL)
* Improve styling/theme responsiveness

## License

This project is open source and provided under the MIT License. See the `LICENSE` file for full details.

## Acknowledgements

Thanks to all open-source contributors whose work inspired this project.
If you use this code or adapt it, a mention or link back is appreciated.
