(function (Drupal, CKEditor5) {
  'use strict';

  const { Plugin } = CKEditor5.core;
  const { ButtonView } = CKEditor5.ui;

  /**
   * Fuegt ein geschuetztes Leerzeichen (U+00A0) ein.
   */
  class NonBreakingSpaceEditing extends Plugin {
    static get pluginName() {
      return 'NonBreakingSpaceEditing';
    }

    init() {
      const editor = this.editor;

      editor.ui.componentFactory.add('nbsp', (locale) => {
        const view = new ButtonView(locale);

        view.set({
          label: Drupal.t('Insert non-breaking space'),
          tooltip: true,
          withText: false,
          icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2 14h2v2H2zM16 14h2v2h-2zM6 15h8v1H6z" fill="currentColor"/><path d="M5 5h10v2H5z" fill="none" stroke="currentColor" stroke-width="1" stroke-dasharray="2 1"/></svg>',
        });

        view.on('execute', () => {
          editor.model.change((writer) => {
            editor.model.insertContent(writer.createText('\u00A0'));
          });
          editor.editing.view.focus();
        });

        return view;
      });
    }
  }

  window.CKEditor5.bsiEditorNbsp = {
    NonBreakingSpaceEditing,
  };
})(Drupal, window.CKEditor5);
