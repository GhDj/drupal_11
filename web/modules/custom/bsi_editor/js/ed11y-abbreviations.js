/**
 * @file
 * Behaviors that integrate with Editoria11y.
 */
(function (Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.bsiEditorEd11y = {
    attach (context, settings) {
      context.addEventListener('ed11yRunCustomTests', (event) => {
        if (this.init !== true) {
          this.initialize();
        }
        this.checkAbbreviations();
      });
    },

    initialize: function () {
      this.registerElements();
      this.registerTests();
      this.init = true;
    },

    registerElements: function () {
      Ed11y.findElements('caption', 'figcaption, caption, summary');
      Ed11y.findElements('abbr', 'abbr:not([title])');
    },
    registerTests: function () {
      Ed11y.M['abbrevAmbiguous'] = {
        title: Drupal.t('Manual check: This abbreviation has multiple possible meanings.'),
        tip : (abbreviation, suggestions) => {
          let tip = `<p>${Drupal.t('This text contains the abbreviation <strong>@abbreviation</strong>. There are multiple possible meanings this could refer to.', {
            '@abbreviation': abbreviation,
          })}</p>`;

          tip += `<ul>`;
          for (const suggestion of suggestions) {
            tip += `<li><a href="${Drupal.url(suggestion.url)}" target="_blank">${Ed11y.sanitizeForHTML(suggestion.title)}</a></li>`;
          }
          tip += `</ul>`;
          tip += `<p>${Drupal.t('Please edit the content and manually specify the intended suggestion using the "Abbreviation" button.')}</p>`;

          return tip;
        },
      };
    },

    checkAbbreviations: function (event) {
      // Preparing the output.
      fetch(drupalSettings.bsi_editor.abbreviations_endpoint, {
        credentials: 'same-origin',
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        const abbreviations = Object.keys(data);
        if (abbreviations.length === 0) {
          return;
        }
        const elementGroupsToCheck = ['h', 'p', 'li', 'a', 'blockquote', 'caption', 'abbr'];
        const regex = new RegExp(
          `\\b(${abbreviations.join('|')})\\b`,
          'g'
        );

        // Mark violations.
        for (const elementGroup of elementGroupsToCheck) {
          Ed11y.elements[elementGroup]?.forEach((el) => {
            if (!el.textContent) { return; }

            const text = Ed11y.computeText(el, 0, !!Ed11y.options.linkIgnoreSelector);
            [...text.matchAll(regex)].forEach((match) => {
              Ed11y.results.push({
                element: el,
                test: 'abbrevAmbiguous',
                content: Ed11y.M.abbrevAmbiguous.tip(match[0], data[match[0]]),
                position: 'beforebegin',
                dismissalKey: Ed11y.dismissalKey(`abbrevAmbiguous:${match[0]}`),
              });
            });
          });
        }
      })
      .catch(error => console.error(error))
      .finally(() => {
        // Notify that we're done.
        document.dispatchEvent(new CustomEvent('ed11yResume'));
      });
    }
  };

} (Drupal, drupalSettings));
