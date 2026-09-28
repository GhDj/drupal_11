/**
 * BSI Document Tables - Frontend Table of Contents
 */
(function($, Drupal) {
  'use strict';

  Drupal.behaviors.bsiFrontendToc = {
    attach: function (context, settings) {
      // Only run on document pages
      if (!$('.bsi-document.document-with-tables', context).length) {
        return;
      }

      const $tocContainer = $('.js-paragraphs-toc', context);
      if ($tocContainer.length > 0) {
        Drupal.behaviors.bsiFrontendToc.updateToc(context, $tocContainer);
      }

      // Add smooth scrolling
      $('.bsi-frontend-toc-link').on('click', function(e) {
        e.preventDefault();
        const $target = $(e.target.getAttribute('href'));
        if ($target.length > 0) {
          $('html, body').animate({
            scrollTop: $target.offset().top - 80
          }, 600);
        }
      });
    },

    updateToc: function (context, $tocContainer) {
      const tocItems = Drupal.behaviors.bsiFrontendToc.generateToc(context);
      console.debug('BSI TOC: Total TOC items: ' + tocItems.length, tocItems);
      if (tocItems.length === 0) {
        return;
      }

      $tocContainer.html(
        `<div class="bsi-frontend-table-of-contents">
          <h2>${Drupal.t('Table of contents')}</h2>
          <ul class="bsi-frontend-toc-list">
            ${tocItems.map((item) =>
              `<li><a href="#${item.id}" class="bsi-frontend-toc-link">${item.title}</a></li>`
            ).join('')}
          </ul>
        </div>`);
    },
    generateToc: function (context) {
      let $sections;
      for (const selector of [
        '.field--name-field-paragraphs .field__item',
        '.paragraph',
        '.field__item',
      ]) {
        $sections = $(selector, context);
        if ($sections.length > 0) {
          break;
        }
      }
      if ($sections.length === 0) {
        return [];
      }

      const tocItems = [];
      $sections.each(function(index) {
        const $section = $(this);

        // Multiple ways to find section titles
        let sectionTitle = '';
        for (const selector of [
          '.field--name-field-section-title .field__item',
          'h1, h2, h3, h4, h5, h6',
          '[class*="section-title"], [class*="field-section-title"]',
        ]) {
          const title = $section.find(selector).text().trim();
          if (title.length !== 0) {
            const sectionId = `bsi-frontend-section-${index}`;
            $section.attr('id', sectionId);

            tocItems.push({
              id: sectionId,
              title: title,
            });

            break;
          }
        }
      });

      return tocItems;
    }
  };

})(jQuery, Drupal);
