<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\ParagraphInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Theme hooks related to the field widgets.
 */
class ParagraphHooks {

  public function __construct(
    protected readonly PathMatcherInterface $pathMatcher,
  ) {}

  /**
   * Implements hook_field_widget_complete_WIDGET_TYPE_form_alter() for paragraphs.
   */
  #[Hook('field_widget_complete_paragraphs_form_alter')]
  public function alterParagraphsCompleteWidget(array &$elements, FormStateInterface $form_state, $context): void {
    if (!empty($elements['widget']['add_more']['operations']['#links'])) {
      uasort($elements['widget']['add_more']['operations']['#links'], static fn (array $a, array $b)
        => $a['title']['#value'] <=> $b['title']['#value']);
    }
  }

  /**
   * Implements hook_field_widget_single_element_WIDGET_TYPE_form_alter() for paragraphs.
   */
  #[Hook('field_widget_single_element_paragraphs_form_alter')]
  public function alterParagraphsSingleWidget(array &$element, FormStateInterface &$form_state, array $context): void {
    /** @var \Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList $items */
    $items = $context['items'];
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $items->getEntity();

    $form_alter = 'alter' . Container::camelize($element['#paragraph_type']) . 'Form';
    if (method_exists($this, $form_alter)) {
      call_user_func_array([$this, $form_alter], [
        &$element['subform'],
        $entity,
        $items->get($context['delta'])->entity,
      ]);
    }

    if ($entity instanceof NodeInterface && $entity->bundle() === 'article'
      && isset($element['subform']['field_jumplink'])
    ) {
      $element['subform']['field_jumplink']['#access'] = FALSE;
    }
  }

  /**
   * Implements hook_preprocess_HOOK() for paragraphs_summary.
   *
   * Alter the paragraph summary used in forms.
   */
  #[Hook('preprocess_paragraphs_summary')]
  public function preprocessSummary(array &$variables): void {
    $host_field_name = reset($variables['element']['#parents']);
    if (in_array($host_field_name, [
      'field_stage_items',
      'field_stage_extra',
      'field_menu_extra',
    ], TRUE)) {
      // Display only the first field (element title) for stage items.
      $variables['content'] = [array_shift($variables['content'])];
    }
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter() for paragraph.
   */
  #[Hook('theme_suggestions_paragraph_alter')]
  public function alterThemeSuggestions(array &$suggestions, array &$variables): void {
    /** @var \Drupal\paragraphs\Entity\Paragraph $entity */
    $entity = $variables['elements']['#paragraph'] ?? NULL;
    $pageContext = $this->pathMatcher->isFrontPage() ? 'front' : 'default';

    $parentBundle = $entity->getParentEntity()?->bundle();
    if (!$parentBundle) {
      return;
    }

    foreach ([] + $suggestions as $suggestion) {
      $suggestions[] = 'paragraph__' . $parentBundle . '__' . substr($suggestion, strlen('paragraph__'));
    }

    // Suggestions for stages on entry_pages.
    if ($parentBundle === 'entry_page' && in_array($variables['elements']['#view_mode'], ['stage', 'stage_extra'], TRUE)) {
      $suggestions[] = 'paragraph__' . $parentBundle . '__' . substr($suggestion, strlen('paragraph__')) . '__' . $pageContext;
    }
  }

  /**
   * Form alter handler for "sequence" type paragraphs.
   */
  public function alterSequenceForm(array &$form, ContentEntityInterface $host_entity, ?ParagraphInterface $paragraph): void {
    if ($host_entity->bundle() === 'job' && isset($form['field_display_variant'])) {
      unset($form['field_display_variant']['widget'][0]['value']['#options']['timeline']);
    }

    if (isset($form['field_display_animate'])) {
      $form['field_display_animate']['#states']['visible'] = [
        ':input[name*="[subform][field_display_variant]"]' => ['value' => 'timeline'],
      ];
    }
  }

  /**
   * Form alter handler for "key_facts" type paragraphs.
   */
  public function alterKeyFactsForm(array &$form, ContentEntityInterface $host_entity, ?ParagraphInterface $paragraph): void {
    if (isset($form['field_section_text'])) {
      $form['field_section_text']['widget'][0]['#allowed_formats'] = [
        'plain_text',
      ];
    }
  }

}
