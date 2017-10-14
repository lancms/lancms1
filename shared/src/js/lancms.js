import 'babel-polyfill';
import Promise from 'promise-polyfill';
import 'whatwg-fetch';
import { create as kioskCreate } from '@/kiosk';

// To add to window
if (!window.Promise) {
  window.Promise = Promise;
}

(function() {
  kioskCreate('.kiosk-gui');
})();
