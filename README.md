# Routing System Documentation

This project uses a custom routing system based on the directory structure.

## Directory Structure

```
├── Admin/          # Admin panel routes
│   ├── Actions/    # Admin action handlers
│   └── ...
├── Auth/           # Authentication routes
├── User/           # User/public routes
│   ├── Actions/    # User action handlers
│   └── ...
└── Config/         # Configuration files
    └── router.php  # Router implementation
```

## How It Works

The routing system is initialized in `index.php` and uses `Config/router.php` to handle all incoming requests.

### URL Structure

- **Home/Blog**: `/` or `/home` or `/blog`
- **Post View**: `/post/{id}` (e.g., `/post/1`)
- **Login**: `/login`
- **Register**: `/register`
- **Logout**: `/logout`
- **Admin Dashboard**: `/admin` or `/admin/dashboard`
- **Admin Posts**: `/admin/posts`
- **Admin Add Post**: `/admin/addpost`
- **Admin Edit Post**: `/admin/editpost`
- **Admin Categories**: `/admin/categories`
- **Admin Comments**: `/admin/comments`
- **Admin Users**: `/admin/users`

### Middleware

Routes can be protected with middleware:

- `auth`: Requires user to be authenticated
- `admin`: Requires user to be admin
- `guest`: Requires user to NOT be authenticated

### Using URL Helper Functions

Include the URL helper in your files:

```php
require_once __DIR__ . '/../Config/url_helper.php';
```

Then use helper functions:

```php
// Generate URLs
echo url('admin/posts');                    // /admin/posts
echo postUrl(1);                            // /post/1
echo adminUrl('categories');                // /admin/categories
echo loginUrl();                            // /login

// Redirect
redirect('admin/dashboard');
redirect('post/5', ['action' => 'view']);

// Assets
echo asset('User/assets/css/user.css');     // /User/assets/css/user.css
echo asset('public/blog.svg');              // /public/blog.svg
```

### Adding New Routes

Edit `Config/router.php` to add new routes:

```php
// Add a new route
$router->addRoute('GET', '/new-route', 'User/newpage.php', ['auth']);
$router->addRoute('POST', '/new-route', 'User/newpage.php', ['auth']);

// Parameterized route
$router->addRoute('GET', '/category/{id}', 'User/category.php');
```

### Route Parameters

Route parameters are automatically extracted and available:

- As `$_GET` parameters (for backward compatibility)
- As variables in the handler file

Example:
- Route: `/post/{id}`
- URL: `/post/123`
- In handler: `$id` is available, and `$_GET['id']` is set to `123`

## Configuration

### .htaccess

The `.htaccess` file enables clean URLs by rewriting all requests to `index.php`. Make sure mod_rewrite is enabled on your server.

### Base Path

If your application is in a subdirectory, update the base path in `Config/router.php`:

```php
$router = new Router('/subdirectory');
```

## Examples

### Creating a Link

```php
<a href="<?= postUrl($post['id']); ?>">Read More</a>
<a href="<?= adminUrl('addpost'); ?>">Add New Post</a>
```

### Redirecting After Action

```php
// In an action file
require_once __DIR__ . '/../../Config/url_helper.php';
redirect('admin/posts');
```

### Checking Current Path

```php
require_once __DIR__ . '/../Config/url_helper.php';

if (isCurrentPath('admin')) {
    echo 'You are on the admin page';
}
```

