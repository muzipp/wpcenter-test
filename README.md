# WP Center Test Plugin

## Description
WP Center Test is a WordPress plugin that creates a custom endpoint and manages AJAX-driven user data fetching and display.

## Features
- Custom endpoint creation for WordPress.
- AJAX functionality to fetch user details and clear cached data.
- Interactive frontend display of user data in a popup modal.
- Cache mechanism for efficient data retrieval and performance optimization.

## Installation
1. Download the `wpcenter-test` plugin.
2. Upload the plugin files to the `/wp-content/plugins/wpcenter-test` directory, or install the plugin through the WordPress plugins screen directly.
3. Activate the plugin through the 'Plugins' screen in WordPress.

## Usage
- Visit the custom endpoint added by the plugin to view the fetched user data.
- Click on a user row to view detailed information about the user.
- Use the "Önbelleği Temizle" button to clear cached user data.

## Development

### Setup
- Clone the repository to your local development environment.
- Ensure WordPress is installed and running.

### Plugin Structure
- `wpcenter-test.php`: Main plugin file that initializes the plugin, creates a custom endpoint, and enqueues scripts and styles.
- `wpcenter-test.js`: JavaScript file for handling frontend AJAX requests and dynamic content display.
- `wpcenter-test.css`: Style file for custom styling.
- `img/`: Directory containing images used by the plugin.

### Developing New Features
1. **Adding New Endpoints**:
   - Modify `wpcenter_test_create_endpoint()` in `wpcenter-test.php` to add new custom endpoints.

2. **AJAX Functionality**:
   - Add new AJAX handlers in `wpcenter-test.php`.
   - Update `wpcenter-test.js` to handle new AJAX requests and responses.

3. **Frontend Display**:
   - Modify or extend `wpcenter_test_display_users()` in `wpcenter-test.php` for changes in user data display.
   - Update `wpcenter-test.js` for any interactive frontend changes.

4. **Styling**:
   - Update `wpcenter-test.css` to alter or enhance the visual style of the plugin.

### Testing
- Test the plugin in a local WordPress environment.
- Test all AJAX functionalities and user interactions.
- Validate the display of user data and the functionality of the cache clearing mechanism.

## License
GPL-2.0 License
