import Vue from 'vue';
import immediate from 'immediate';
import LancmsSuggest from '@/Suggest.vue';

const isDevelopment = process.env.NODE_ENV === 'development';
Vue.config.silent = !isDevelopment;
Vue.config.devtools = isDevelopment;
Vue.config.performance = isDevelopment;
Vue.config.productionTip = isDevelopment;

export default class KioskGui {
  constructor(element) {
    this.element = element;
  }

  init() {
    new Vue({
      el: this.element.querySelector('#kiosk-suggest'),
      components: {
        LancmsSuggest,
      },
    });
  }
}

export function create(sel, parent = null) {
  if (parent === null) parent = document;

  const elements = parent.querySelectorAll(sel);

  for (const element of elements) {
    immediate(() => {
      const gui = new KioskGui(element);
      gui.init();
    });
  }
}
