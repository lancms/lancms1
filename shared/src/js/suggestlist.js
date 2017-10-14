import Popper from 'popper.js';
import debounce from 'lodash.debounce';
import immediate from 'immediate';
import fetch from '@/fetch';
import { parseFromHtml, insertAfter } from '@/dom';
import suggestListTemplate from '@/templates/suggest_list.html';

export default class SuggestList {
  constructor({ container, input, url }) {
    this.listPopperInstance = null;
    this.listElement = null;
    this.container = container;
    this.url = url;
    this.input = input;
    this.inputKeyPressCb = null;
    this.previousItems = null;
  }

  init() {
    this.inputKeyPressCb = debounce(this.handleSuggestion.bind(this), 300);
    this.input.addEventListener('keyup', this.inputKeyPressCb, false);
  }

  async handleSuggestion(event) {
    if (typeof event.target.value !== 'string') return;

    const items = await fetch(this.parseUrl({ query: event.target.value }), { credentials: 'same-origin' });

    if (!Array.isArray(items)) return;

    immediate(() => {
      if (this.hasItemsChanged(items, this.previousItems)) {
        this.destroyList();
        this.createList(items);
      }
    });
  }

  hasItemsChanged(data, previousData = null) {
    const previous = previousData !== null ? JSON.stringify(previousData) : '';

    return previous !== JSON.stringify(data);
  }

  createList(items) {
    this.container.classList.add('suggest-list-container');
    this.input.classList.add('suggest-list-open');
    this.listElement = parseFromHtml(suggestListTemplate({ items }))[0];
    this.listPopperInstance = new Popper(this.input, this.listElement, {
      placement: 'top-end',
    });
    insertAfter(this.listElement, this.input);

    this.previousItems = items;
  }

  destroyList() {
    this.container.classList.remove('suggest-list-container');
    this.input.classList.remove('suggest-list-open');

    if (this.listPopperInstance !== null) {
      this.listPopperInstance.destroy();
      this.listPopperInstance = null;
    }

    if (this.listElement !== null) {
      this.listElement.remove();
      this.listElement = null;
    }
  }

  parseUrl({ query }) {
    return this.url.replace('$query$', query);
  }

  destroy() {
    this.destroyList();
    this.input.removeEventListener('keyup', this.inputKeyPressCb);
    this.input = null;
    this.container = null;
  }
}
