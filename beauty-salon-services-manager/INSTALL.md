# Installation Guide - Beauty Salon Services Manager

## Prerequisites

Before installing the Beauty Salon Services Manager plugin, ensure your server meets the following requirements:

- **WordPress Version**: 6.0 or higher
- **PHP Version**: 7.4 or higher
- **MySQL Version**: 5.6 or higher (or MariaDB 10.0+)
- **Elementor Plugin**: Free or Pro version installed and activated

### Recommended Server Configuration

- PHP Memory Limit: 128MB or higher
- Max Upload Size: 32MB or higher
- Max Execution Time: 30 seconds or higher
- WordPress Memory Limit: 64MB or higher

## Installation Methods

### Method 1: WordPress Admin Panel (Recommended)

1. **Log in to WordPress Admin**
   - Navigate to your WordPress admin dashboard
   - URL format: `https://yoursite.com/wp-admin`

2. **Navigate to Plugins**
   - Click on **Plugins** in the left sidebar
   - Click **Add New**

3. **Upload Plugin**
   - Click the **Upload Plugin** button at the top of the page
   - Click **Choose File** and select the `beauty-salon-services-manager.zip` file
   - Click **Install Now**

4. **Activate Plugin**
   - After installation completes, click **Activate Plugin**
   - You'll see a success message

5. **Verify Installation**
   - Look for **Beauty Services** in the admin menu
   - This confirms the plugin is installed correctly

### Method 2: FTP/SFTP Upload

1. **Extract Plugin Files**
   - Unzip the `beauty-salon-services-manager.zip` file on your computer
   - You should have a folder named `beauty-salon-services-manager`

2. **Connect via FTP/SFTP**
   - Use an FTP client (FileZilla, Cyberduck, etc.)
   - Connect to your server using your FTP credentials

3. **Upload Plugin Folder**
   - Navigate to `/wp-content/plugins/` on your server
   - Upload the entire `beauty-salon-services-manager` folder

4. **Activate Plugin**
   - Go to your WordPress admin panel
   - Navigate to **Plugins > Installed Plugins**
   - Find "Beauty Salon Services Manager"
   - Click **Activate**

### Method 3: WP-CLI (For Advanced Users)

```bash
# Navigate to WordPress directory
cd /path/to/wordpress

# Install plugin from zip file
wp plugin install /path/to/beauty-salon-services-manager.zip

# Activate the plugin
wp plugin activate beauty-salon-services-manager

# Verify activation
wp plugin list | grep beauty-salon-services-manager
```

## Post-Installation Setup

### Step 1: Verify Plugin Activation

1. Go to **Plugins > Installed Plugins**
2. Ensure "Beauty Salon Services Manager" is listed and active
3. Check for **Beauty Services** menu item in admin sidebar

### Step 2: Check Elementor Compatibility

1. Go to **Elementor > Settings**
2. Ensure Elementor is activated and working
3. The Beauty Services Grid widget will be available in Elementor's widget panel

### Step 3: Flush Permalinks

This step is important for proper URL structure:

1. Go to **Settings > Permalinks**
2. Don't change anything, just click **Save Changes**
3. This flushes the rewrite rules and activates custom post type URLs

### Step 4: Configure Basic Settings (Optional)

While the plugin works out of the box, you may want to:

1. Create initial service groups (**Beauty Services > Service Groups**)
2. Add your first service (**Beauty Services > Add New Service**)
3. Test the Elementor widget on a test page

## Updating the Plugin

### From WordPress Admin

1. When an update is available, you'll see a notification
2. Go to **Plugins > Installed Plugins**
3. Click **Update Now** for Beauty Salon Services Manager
4. Wait for the update to complete

### Manual Update

1. **Backup First**: Always backup your site before updating
2. Deactivate the current version
3. Delete the old plugin folder via FTP
4. Upload the new version using Method 2 above
5. Reactivate the plugin
6. Flush permalinks (Settings > Permalinks > Save Changes)

## Troubleshooting Installation Issues

### Issue: "Plugin could not be activated"

**Solution:**
- Check if you meet the minimum WordPress and PHP requirements
- Ensure Elementor is installed and activated
- Check for PHP errors in your error log

### Issue: "The uploaded file exceeds the upload_max_filesize directive"

**Solution:**
- Use FTP installation method instead
- Or increase your server's upload limit:
  - Add to `.htaccess`: `php_value upload_max_filesize 64M`
  - Or contact your hosting provider

### Issue: Services menu not appearing

**Solution:**
- Deactivate and reactivate the plugin
- Check user role capabilities (must be Administrator)
- Clear WordPress cache if using a caching plugin

### Issue: 404 errors on service pages

**Solution:**
- Go to **Settings > Permalinks**
- Click **Save Changes** to flush rewrite rules
- If using a custom permalink structure, ensure it's supported

### Issue: Elementor widget not showing

**Solution:**
- Ensure Elementor is activated
- Clear Elementor cache: **Elementor > Tools > Regenerate CSS**
- Check if Elementor version is compatible
- Try regenerating Elementor files

## File Permissions

Ensure proper file permissions are set:

```bash
# Directories should be 755
find /path/to/wp-content/plugins/beauty-salon-services-manager -type d -exec chmod 755 {} \;

# Files should be 644
find /path/to/wp-content/plugins/beauty-salon-services-manager -type f -exec chmod 644 {} \;
```

## Multisite Installation

For WordPress Multisite:

1. **Network Activate** (applies to all sites):
   - Go to **Network Admin > Plugins**
   - Click **Network Activate** for Beauty Salon Services Manager

2. **Individual Site Activate** (per site basis):
   - Go to each site's admin panel
   - Navigate to **Plugins**
   - Activate individually per site

## Uninstallation

### Complete Removal

1. **Deactivate Plugin**
   - Go to **Plugins > Installed Plugins**
   - Click **Deactivate** under Beauty Salon Services Manager

2. **Delete Plugin**
   - After deactivation, click **Delete**
   - Confirm deletion

3. **Manual Database Cleanup** (if needed):
   ```sql
   -- Remove custom post type posts
   DELETE FROM wp_posts WHERE post_type = 'bslm_service';

   -- Remove post meta
   DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts);

   -- Remove taxonomy terms
   DELETE FROM wp_term_taxonomy WHERE taxonomy = 'bslm_service_group';

   -- Remove orphaned term relationships
   DELETE FROM wp_term_relationships WHERE term_taxonomy_id NOT IN (SELECT term_taxonomy_id FROM wp_term_taxonomy);
   ```

**Note**: Uninstalling the plugin will NOT automatically delete your services and service groups. Use the database cleanup only if you want to completely remove all data.

## Next Steps

After successful installation:

1. Read the [README.md](README.md) for feature overview
2. Check [USER-GUIDE.md](USER-GUIDE.md) for detailed usage instructions
3. Review [DEVELOPER-GUIDE.md](DEVELOPER-GUIDE.md) if you plan to customize the plugin

## Getting Help

If you encounter issues not covered here:

1. Check the [FAQ section](README.md#frequently-asked-questions) in README.md
2. Review WordPress error logs (usually in `/wp-content/debug.log`)
3. Enable WordPress debugging:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```
4. Contact support or open a GitHub issue

## System Check

Run this quick checklist after installation:

- [ ] Plugin activated successfully
- [ ] "Beauty Services" menu appears in admin
- [ ] Can create a new service
- [ ] Can create service groups
- [ ] Elementor widget appears in widget panel
- [ ] Service pages display correctly on frontend
- [ ] No PHP errors in error log
- [ ] Permalinks flushed (Settings > Permalinks > Save)

If all items are checked, your installation is complete and successful!
