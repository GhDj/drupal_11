/**
 * BSI Document Tables - TABLE OF CONTENTS
 */
(function($) {
  'use strict';

  function generateTableOfContents(context) {
    $('.js-paragraphs-toc', context).remove();

    const $sections = $('.field--name-field-paragraphs .paragraphs-subform', context);
    if ($sections.length === 0) return;

    let tocHtml = '<div class="js-paragraphs-toc">';
    tocHtml += '<h3>Inhaltsverzeichnis</h3>';
    tocHtml += '<ul class="bsi-toc-list">';

    $sections.each(function(index) {
      const $section = $(this);

      // Find section title
      let $titleField = $section.find('.field--name-field-section-title input');
      if ($titleField.length === 0) {
        $titleField = $section.find('input[name*="field_section_title"]');
      }

      if ($titleField.length > 0) {
        const sectionId = 'bsi-section-' + index;
        const sectionTitle = $titleField.val() || Drupal.t('Section @index', {
          '@index': index + 1,
        });

        // Add ID to section for jumping
        $section.attr('id', sectionId);

        // Add to TOC
        tocHtml += `<li><a href="#${sectionId}" class="bsi-toc-link">${sectionTitle}</a></li>`;
      }
    });

    tocHtml += '</ul></div>';

    // Insert TOC before the sections
    $('.field--name-field-paragraphs').before(tocHtml);

    // Add click handlers for smooth scrolling
    $('.bsi-toc-link').on('click', function(e) {
      e.preventDefault();
      const $target = $(this.getAttribute('href'));
      if ($target.length) {
        $('html, body').animate({
          scrollTop: $target.offset().top - 50
        }, 500);
      }
    });
  }

  // Combined function
  function runAll() {
    generateTableOfContents();
  }

  // Execute
  runAll();
  $(document).ready(runAll);
  $(document).ajaxComplete(function() {
    setTimeout(runAll, 500);
    setTimeout(runAll, 1000);
  });
  $(document).on('click', 'input[type="submit"], button', function() {
    setTimeout(runAll, 1000);
    setTimeout(runAll, 2000);
  });

  // Update TOC when section titles change
  $(document).on('input', 'input[name*="field_section_title"]', function() {
    setTimeout(generateTableOfContents, 300);
  });

})(jQuery);
