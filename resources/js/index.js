// Index
require('./index/menu');
// require('./index/theme');
require('./shared/flash');
//require('./shared/new-orders');

// Fixing paypal popup not working in web-view (embedded iOS app browser like Instagram or Facebook)
window.webview = (navigator.appVersion.indexOf('AppleWebKit') !== -1 || navigator.appVersion.indexOf('Facebook') !== -1);
if(webview) document.getElementById('cart-menu-item').setAttribute('target', '_blank');