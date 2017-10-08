import SuggestList from '@/suggestlist';
import immediate from 'immediate';

export default class KioskGui {
  constructor(element) {
    this.suggestList = null;
    this.element = element;
  }

  init() {
    const wareContainer = this.element.querySelector('.kiosk-suggestions');

    this.suggestList = new SuggestList({
      container: wareContainer,
      input: wareContainer.querySelector('input'),
      url: '?api=kiosk&action=searchitems&search=$query$',
    });
    this.suggestList.init();

    // Allow to set amount in
    // const amountNumbers = this.element.querySelectorAll('.amount-number');
    //
    // for (const numberElement of amountNumbers) {
    //   numberElement.addEventListener('click', this.handleAmountClick.bind(this), false);
    // }
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

  if (elements.length > 0) {
    elements[0].querySelector('input').focus();
  }

}
