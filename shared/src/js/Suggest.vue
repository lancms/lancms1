<template>
  <div class="suggest-list-container">
    <input
      class="ware"
      tabindex="1"
      autocomplete="off"
      type="text"
      ref="target"
      :name="inputName"
      v-model="value"
      @keyup="handleSuggestionsKeyPress" />
    <div
      class="suggest-list"
      ref="list"
      v-click-outside="close"
      v-show="state === states.open">
      <a
        v-for="item in items"
        :href="item.url"
        class="suggest-list-item">
        <span>{{ item.name }}</span>
        <span v-if="typeof item.description !== 'undefined'" class="text-muted">({{ item.description }})</span>
      </a>
      <span v-if="items.length <= 0" class="text-muted"><em>No results</em></span>
    </div>
  </div>
</template>

<script>
import Popper from 'popper.js';
import debounce from 'lodash.debounce';
import fetch from '@/fetch';
import ClickOutside from '@/click-outside';

export default {
  directives: {
    ClickOutside,
  },

  props: {
    url: {
      type: String,
      required: true,
    },

    inputName: {
      type: String,
      default: 'input',
    },
  },

  data() {
    return {
      states: {
        open: 1,
        closed: 2,
      },
      state: 2,
      value: '',
      items: [],
      popper: null,
    };
  },

  beforeDestroy() {
    if (this.popper !== null) this.popper.destroy();
  },

  mounted() {
    this.$refs.target.focus();
  },

  methods: {
    /**
     * Debounces the key press events.
     */
    handleSuggestionsKeyPress: debounce(function() {
      this.handleSuggestions();
    }, 200),

    async handleSuggestions() {
      if (this.popper === null) this.createPopper();

      await this.$nextTick();

      this.items = await fetch(this.parseUrl({ query: this.value }), { credentials: 'same-origin' });
      this.state = this.states.open;
      this.popper.update();
    },

    close() {
      if (this.state === this.states.open) {
        this.items = [];
        this.state = this.states.closed;
      }
    },

    createPopper() {
      this.popper = new Popper(this.$refs.target, this.$refs.list, {
        placement: 'top-end',
      });
    },

    parseUrl({ query }) {
      return this.url.replace('$query$', query);
    },
  },
};
</script>
