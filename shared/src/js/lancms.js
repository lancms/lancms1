import 'babel-polyfill';
import Promise from 'promise-polyfill';
import 'whatwg-fetch';
import { create as kioskCreate } from '@/kiosk';

// To add to window
if (!window.Promise) {
  window.Promise = Promise;
}

window.addEventListener('load', function() {
  kioskCreate('.kiosk-gui');
});
