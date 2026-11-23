(function (Drupal) {
  'use strict';

  /**
   * Multi-select chip widget for CSN Profile language field.
   *
   * Replaces the hidden <select multiple> with a custom UI:
   * - Combined input area with chips + text input inline
   * - Dropdown showing all options, Deutsch first
   * - Selected items as chips with X remove button inside the input
   * - Type to filter options
   */
  Drupal.behaviors.csnLanguageSelect = {
    attach: function (context) {
      var selects = context.querySelectorAll('[data-csn-language-select]');
      selects.forEach(function (select) {
        if (select.dataset.csnInitialized) return;
        select.dataset.csnInitialized = '1';
        new LanguageChipWidget(select);
      });
    }
  };

  function LanguageChipWidget(selectEl) {
    this.select = selectEl;
    this.select.style.display = 'none';

    // Wrapper.
    this.wrapper = document.createElement('div');
    this.wrapper.className = 'csn-lang-widget';
    this.select.parentNode.insertBefore(this.wrapper, this.select.nextSibling);

    // Input area — looks like a single input, contains chips + text input.
    this.inputArea = document.createElement('div');
    this.inputArea.className = 'csn-lang-input-area form-element';
    this.wrapper.appendChild(this.inputArea);

    // Text input (inline, grows with available space).
    this.input = document.createElement('input');
    this.input.type = 'text';
    this.input.className = 'csn-lang-text-input';
    this.input.placeholder = this.select.dataset.placeholder || Drupal.t('Bitte auswählen');
    this.inputArea.appendChild(this.input);

    // Dropdown.
    this.dropdown = document.createElement('ul');
    this.dropdown.className = 'csn-lang-dropdown';
    this.wrapper.appendChild(this.dropdown);

    // Build options from select element.
    this.allOptions = [];
    Array.from(this.select.options).forEach(function (opt) {
      if (opt.value) {
        this.allOptions.push({ value: opt.value, label: opt.textContent });
      }
    }.bind(this));

    // Sort: Deutsch first, then alphabetical.
    this.allOptions.sort(function (a, b) {
      if (a.label === 'Deutsch') return -1;
      if (b.label === 'Deutsch') return 1;
      return a.label.localeCompare(b.label);
    });

    this.bindEvents();
    this.renderChips();
  }

  LanguageChipWidget.prototype.getSelectedValues = function () {
    var values = [];
    Array.from(this.select.options).forEach(function (opt) {
      if (opt.selected && opt.value) values.push(opt.value);
    });
    return values;
  };

  LanguageChipWidget.prototype.bindEvents = function () {
    var self = this;

    // Clicking the input area focuses the text input.
    this.inputArea.addEventListener('click', function () {
      self.input.focus();
    });

    this.input.addEventListener('focus', function () {
      self.inputArea.classList.add('csn-lang-input-area--focused');
      self.showDropdown();
    });

    this.input.addEventListener('blur', function () {
      self.inputArea.classList.remove('csn-lang-input-area--focused');
    });

    this.input.addEventListener('input', function () {
      self.showDropdown();
    });

    // Backspace on empty input removes last chip.
    this.input.addEventListener('keydown', function (e) {
      if (e.key === 'Backspace' && !self.input.value) {
        var selected = self.getSelectedValues();
        if (selected.length > 0) {
          self.removeSelection(selected[selected.length - 1]);
        }
      }
    });

    document.addEventListener('click', function (e) {
      if (!self.wrapper.contains(e.target)) {
        self.hideDropdown();
      }
    });
  };

  LanguageChipWidget.prototype.showDropdown = function () {
    var self = this;
    var filter = this.input.value.toLowerCase();
    var selected = this.getSelectedValues();

    this.dropdown.innerHTML = '';

    this.allOptions.forEach(function (opt) {
      if (selected.indexOf(opt.value) !== -1) return;
      if (filter && opt.label.toLowerCase().indexOf(filter) === -1) return;

      var li = document.createElement('li');
      li.className = 'csn-lang-dropdown__item';
      li.textContent = opt.label;
      li.addEventListener('mousedown', function (e) {
        e.preventDefault(); // Prevent blur on input.
        self.addSelection(opt.value);
        self.input.value = '';
        self.showDropdown(); // Refresh dropdown.
      });
      self.dropdown.appendChild(li);
    });

    this.dropdown.classList.add('csn-lang-dropdown--open');
  };

  LanguageChipWidget.prototype.hideDropdown = function () {
    this.dropdown.classList.remove('csn-lang-dropdown--open');
  };

  LanguageChipWidget.prototype.addSelection = function (value) {
    Array.from(this.select.options).forEach(function (opt) {
      if (opt.value === value) opt.selected = true;
    });
    this.renderChips();
  };

  LanguageChipWidget.prototype.removeSelection = function (value) {
    Array.from(this.select.options).forEach(function (opt) {
      if (opt.value === value) opt.selected = false;
    });
    this.renderChips();
  };

  LanguageChipWidget.prototype.renderChips = function () {
    var self = this;

    // Remove existing chips (keep the text input).
    var existingChips = this.inputArea.querySelectorAll('.csn-lang-chip');
    existingChips.forEach(function (c) { c.remove(); });

    var selected = this.getSelectedValues();

    // Insert chips before the text input.
    this.allOptions.forEach(function (opt) {
      if (selected.indexOf(opt.value) === -1) return;

      var chip = document.createElement('span');
      chip.className = 'csn-lang-chip';
      chip.innerHTML = '<span class="csn-lang-chip__label">' +
        Drupal.checkPlain(opt.label) +
        '</span><button type="button" class="csn-lang-chip__remove" aria-label="' +
        Drupal.t('Remove @lang', { '@lang': opt.label }) +
        '">&times;</button>';

      chip.querySelector('.csn-lang-chip__remove').addEventListener('mousedown', function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.removeSelection(opt.value);
      });

      self.inputArea.insertBefore(chip, self.input);
    });

    // Update placeholder — hide when chips present.
    this.input.placeholder = selected.length > 0 ? '' : (this.select.dataset.placeholder || Drupal.t('Bitte auswählen'));
  };

})(Drupal);
