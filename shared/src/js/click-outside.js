import clickOutside from 'click-outside';

let unbind = null;

export default {
  bind(el, binding) {
    if (typeof binding.value !== 'function')
      throw new Error('Binding value must be a function');

    unbind = clickOutside(el, (e) => {
      binding.value(e);
    });
  },

  unbind() {
    if (unbind !== null) unbind();
  }
};
