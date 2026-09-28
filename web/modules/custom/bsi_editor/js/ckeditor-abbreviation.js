(function (Drupal, drupalSettings, debounce, once) {
  Drupal.behaviors.ckeAbbrAutocomplete = {
    attach(context, settings) {
      if (context !== document) {
        return;
      }

      const [, AbbreviationUI] = window.CKEditor5.abbreviation.Abbreviation.requires;
      if (typeof AbbreviationUI === 'undefined') {
        return;
      }
      const originalCreateFormView = AbbreviationUI.prototype._createFormView;
      AbbreviationUI.prototype._createFormView = function (...args) {
        const formView = originalCreateFormView.apply(this, args);

        Drupal.behaviors.ckeAbbrAutocomplete.attachDatalist(this.editor, formView);
        Drupal.behaviors.ckeAbbrAutocomplete.attachListeners(this.editor, formView);

        return formView;
      };

      const originalShowUi = AbbreviationUI.prototype._showUI;
      AbbreviationUI.prototype._showUI = function (...args) {
        originalShowUi.apply(this, args);
        Drupal.behaviors.ckeAbbrAutocomplete.update(this.editor, this.formView);
      }
    },

    attachDatalist: function (editor, formView) {
      formView.once('render', function () {
        const abbrInput = this.abbrInputView.fieldView.element;
        const titleInput = this.titleInputView.fieldView.element;

        const datalist = document.createElement('datalist');
        datalist.id = `abbr-autocomplete-${editor.id}`;

        titleInput.parentNode.appendChild(datalist);
        titleInput.setAttribute('autocomplete', 'off');
        titleInput.setAttribute('list', datalist.id);

        // Setup easy references for processing.
        abbrInput._datalist = datalist;
        titleInput._datalist = datalist;
        titleInput._abbr = abbrInput;
      });
    },
    attachListeners: (editor, formView) => {
      formView.listenTo(formView, 'submit', (event) => {
        editor.execute('addAbbreviation', {
          abbr: event.source.abbrInputView.fieldView.element.value,
          title: event.source.titleInputView.fieldView.element.uuid,
        });
      })

      formView.once('render', function () {
        const titleInput = this.titleInputView.fieldView.element;
        titleInput.addEventListener('input', (event) => {
          if (event instanceof InputEvent) {
            event.target.uuid = null;
            return;
          }
          Drupal.behaviors.ckeAbbrAutocomplete.select(event.target);
        });
        const abbrInput = this.abbrInputView.fieldView.element;
        abbrInput.addEventListener('input', debounce(
          (event) => {
            const input = event.target.value ?? '';
            if (event.data && input.length >= 2) {
              Drupal.behaviors.ckeAbbrAutocomplete.updateAutocomplete(event.target);
            }
          },
          300
        ));
      });
    },

    select: function (titleInput) {
      const selectedOption = titleInput._datalist.childNodes.values()
        .find((elem) => elem.value === titleInput.value);
      if (selectedOption) {
        titleInput._abbr.value = selectedOption.dataset.abbr;
        titleInput.uuid = selectedOption.dataset.uuid;
      }
    },

    _titlesByUuid: {},
    update: (editor, formView) => {
      formView.titleInputView.fieldView.errorText = null;
      const titleInput = formView.titleInputView.fieldView.element;

      Drupal.behaviors.ckeAbbrAutocomplete.updateAutocomplete(formView.abbrInputView.fieldView.element);

      const uuidTitle = Drupal.behaviors.ckeAbbrAutocomplete._titlesByUuid[titleInput.value] ?? null;
      if (uuidTitle !== null) {
        titleInput.uuid = titleInput.value;
        titleInput.value = uuidTitle;
      }
    },
    updateAutocomplete: (abbrInput) => {
      fetch(`${drupalSettings.bsi_editor.abbreviations_endpoint}/${abbrInput.value}`)
        .then(res => res.json())
        .then(data => {
          const options = [];
          for (const abbreviation in data) {
            for (const suggestion of data[abbreviation]) {
              const option = document.createElement('option');
              option.text = suggestion.title;
              option.dataset.abbr = suggestion.abbreviation;
              option.dataset.uuid = suggestion.uuid;
              options.push(option);

              Drupal.behaviors.ckeAbbrAutocomplete._titlesByUuid[suggestion.uuid] = suggestion.title;
            }
          }
          abbrInput._datalist.replaceChildren(...options);
          if (options.length > 0) {
            Drupal.announce(Drupal.t('Abbreviation title suggestions have been updated.'));
          }
        })
        .catch(error => console.error('Failed to update datalist.'));
    },
  };
})(Drupal, drupalSettings, Drupal.debounce, once);
