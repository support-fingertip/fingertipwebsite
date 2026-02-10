# Fingertip Plus Website

A complete, production-ready website for **Fingertip Plus** - a premier Salesforce Partner specializing in Salesforce CRM, web, and mobile application development.

## 🌟 Features

- **Premium Corporate Design** - Modern, professional interface with Salesforce-inspired color palette
- **Fully Responsive** - Mobile-first design that works perfectly on all devices
- **Admin Panel** - Complete content management system for blog posts and contact submissions
- **Blog System** - Full-featured blog with rich text editor and image uploads
- **Contact Management** - Secure contact form with email notifications and spam protection
- **SEO Optimized** - Proper meta tags, Open Graph tags, and semantic HTML
- **Performance Optimized** - Fast loading with smooth animations
- **Security First** - CSRF protection, rate limiting, XSS prevention, and secure file uploads

## 📋 Requirements

- **PHP** 7.4 or higher
- **MySQL** 5.7 or higher
- **Apache** with mod_rewrite enabled
- **GoDaddy Shared Hosting Compatible** - No command line tools required!

## 🚀 Installation on GoDaddy Shared Hosting

### Step 1: Upload Files

1. Download all files from this repository
2. Connect to your GoDaddy hosting via **FTP** (FileZilla recommended)
3. Upload all files to your `public_html` directory (or subdomain folder)
4. Ensure the `uploads` directory is created and writable (chmod 755)

### Step 2: Create Database

1. Log in to your **GoDaddy cPanel**
2. Go to **MySQL Databases**
3. Create a new database (e.g., `fingertip_website`)
4. Create a new MySQL user with a strong password
5. Add the user to the database with **ALL PRIVILEGES**
6. Note down your database credentials:
   - Database name
   - Database username
   - Database password
   - Database host (usually `localhost`)

### Step 3: Configure Database Connection

1. Open `config.php` in a text editor
2. Update the database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_database_name');  // Change this
   define('DB_USER', 'your_username');        // Change this
   define('DB_PASS', 'your_password');        // Change this
   ```
3. Update the site URL:
   ```php
   define('SITE_URL', 'https://fingertipplus.com');  // Change to your domain
   ```
4. Save and upload the updated `config.php`

### Step 4: Run Database Setup

1. Open your browser and navigate to: `https://yourdomain.com/setup.php`
2. The setup script will:
   - Create all necessary database tables
   - Insert sample blog posts
   - Set up the admin user
3. You should see a "Setup Complete" message
4. **IMPORTANT:** Delete `setup.php` immediately after successful setup for security

### Step 5: Access Admin Panel

1. Navigate to: `https://yourdomain.com/admin/`
2. Login with default credentials:
   - Username: `admin`
   - Password: `fingertip@2024`
3. **IMPORTANT:** Change your password immediately after first login

### Step 6: Final Configuration

1. Test the contact form by submitting a test message
2. Upload your own blog posts via the admin panel
3. Update contact information in `index.html` and footer
4. Replace placeholder images with your own (if desired)

## 📁 File Structure

```
/
├── index.php               # Main homepage (with PHP for CSRF tokens)
├── blog.php                # Blog listing page
├── blog-post.php          # Individual blog post view
├── contact-handler.php    # Contact form handler
├── blog-api.php           # API for blog posts
├── config.php             # Database configuration
├── setup.php              # One-time database setup (delete after use)
├── .htaccess              # Apache configuration & URL rewriting
│
├── admin/                 # Admin Panel
│   ├── index.php          # Login page
│   ├── login.php          # Login handler
│   ├── logout.php         # Logout handler
│   ├── auth.php           # Authentication middleware
│   ├── dashboard.php      # Admin dashboard
│   ├── contacts.php       # Contact management
│   ├── blog-list.php      # Blog post list
│   ├── blog-create.php    # Create new post
│   ├── blog-edit.php      # Edit post
│   ├── upload.php         # Image upload handler
│   ├── includes/          # Header & sidebar includes
│   └── assets/
│       └── admin.css      # Admin panel styling
│
├── css/
│   └── style.css          # Main website styles
│
├── js/
│   └── main.js            # Main website JavaScript
│
├── images/                # Your website images
│
└── uploads/               # Blog image uploads
    └── .htaccess          # Security for uploads
```

## 🎨 Customization

### Change Colors

Edit `css/style.css` and update the CSS variables:
```css
:root {
    --navy: #0D1B2A;
    --blue: #00A1E0;
    --teal: #1CB5AC;
    --orange: #FF6B35;
}
```

### Update Company Information

1. Edit `index.html` to update:
   - Company description in About section
   - Contact information
   - Social media links
   - Footer content

2. Edit `config.php` to update:
   - Site name
   - Site email
   - Site URL

### Add/Remove Services

Edit the Services section in `index.php` - each service is a `.service-card` div.

### Change Industry Images

Replace the Unsplash URLs in the Industries section with your own images.

## 🔐 Security Features

- **CSRF Protection** - All forms include CSRF tokens
- **Rate Limiting** - Prevents spam (max 5 contact submissions per hour per IP)
- **XSS Prevention** - All output is sanitized with `htmlspecialchars()`
- **SQL Injection Protection** - PDO with prepared statements
- **File Upload Security** - Type and size validation, no PHP execution in uploads
- **Session Security** - HTTPOnly cookies, session regeneration, timeout
- **Password Hashing** - Bcrypt hashing for admin passwords

## 📧 Email Configuration

The contact form uses PHP's built-in `mail()` function. On GoDaddy:

1. Email should work by default
2. If emails aren't sending, contact GoDaddy support to enable the mail function
3. Alternatively, configure SMTP (requires additional code modification)

## 🛠️ Troubleshooting

### Issue: "Database Connection Failed"
- Verify database credentials in `config.php`
- Ensure database exists in cPanel
- Check that user has proper permissions

### Issue: "setup.php Not Found"
- Clear your browser cache
- Check file permissions (should be 644)
- Verify the file was uploaded correctly

### Issue: "Contact Form Not Working"
- Check browser console for JavaScript errors
- Verify `contact-handler.php` has correct permissions
- Check PHP error logs in cPanel

### Issue: "Images Not Uploading"
- Verify `uploads/` directory exists
- Check directory permissions (should be 755)
- Ensure PHP upload size limits are adequate

### Issue: "Admin Can't Login"
- Default username: `admin`
- Default password: `fingertip@2024`
- Clear browser cache and cookies
- Check browser console for errors

## 📱 Mobile Responsiveness

The website is fully responsive with breakpoints at:
- Desktop: 1200px+
- Tablet: 768px - 1199px
- Mobile: < 768px

## 🌐 Browser Support

- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)
- Mobile browsers (iOS Safari, Chrome Mobile)

## ⚡ Performance Optimization

- Optimized images (use WebP format when possible)
- Minified CSS and JavaScript in production
- Lazy loading for images
- Efficient database queries with indexing
- Browser caching via .htaccess

## 📝 Blog Management

### Creating Posts

1. Go to Admin Panel → Create New Post
2. Enter title (slug auto-generates)
3. Add content using rich text editor
4. Upload featured image
5. Set status (Draft or Published)
6. Click "Create Post"

### Editing Posts

1. Go to Admin Panel → All Posts
2. Click "Edit" on any post
3. Make changes
4. Click "Update Post"

### Embedding Images in Content

Use the image button in the TinyMCE editor to upload and insert images directly into your blog content.

## 🔄 Updates & Maintenance

### Updating Content

- **Homepage**: Edit `index.php`
- **Blog Posts**: Use Admin Panel
- **Contact Info**: Edit `index.php` and footer

### Database Backups

Regular backups recommended via cPanel:
1. Go to cPanel → phpMyAdmin
2. Select your database
3. Click "Export" → "Quick" → "Go"
4. Save the `.sql` file

### File Backups

Use FTP to download entire website periodically.

## 📞 Support

For technical issues:
- Check documentation above
- Review error logs in cPanel
- Contact your hosting provider for server-related issues

For customization requests:
- Hire a PHP developer familiar with vanilla PHP/MySQL

## 📄 License

This website is proprietary software for Fingertip Plus. All rights reserved.

## 🎯 Version

Current Version: 1.0.0
Last Updated: February 2024

---

**Built with ❤️ for Fingertip Plus**

*No frameworks, no dependencies, just pure PHP, MySQL, and modern web standards - perfect for GoDaddy shared hosting!*
