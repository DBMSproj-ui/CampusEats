// Import the Axios HTTP client library for making AJAX requests
import axios from 'axios';

// Make Axios globally available via the `window` object
// This allows you to use `axios` anywhere in your scripts without needing to import it again
window.axios = axios;

// Set a default header for all outgoing Axios requests
// This tells Laravel that the request is coming from JavaScript (AJAX)
// and helps Laravel handle things like CSRF protection and response formatting
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
