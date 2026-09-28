<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 * Hooks related to node behavior and form customization.
 */
class NodeHooks {

  use StringTranslationTrait;

  public function __construct(
    protected readonly PathMatcherInterface $pathMatcher,
  ) {}

  /**
   * Implements hook_form_BASE_FORM_ID_alter() for node_form.
   *
   * Hide article fields based on article type.
   */
  #[Hook('form_node_form_alter')]
  public function alterArticleFields(array &$form, FormStateInterface $form_state, string $form_id): void {
    /** @var \Drupal\node\NodeInterface $entity */
    $entity = $form_state->getFormObject()->getEntity();
    if ($entity->bundle() !== 'article') {
      return;
    }

    $form['field_location']['#states']['visible'][] = [
      ':input[name="field_article_type"]' => ['value' => 'press_release'],
    ];

    // URI is hard-coded to link to entity, must not be overridden.
    // @see \Drupal\bsi_custom\Hook\EntityFormAlterHooks::alterArticleLink.
    $form['field_link']['widget'][0]['uri']['#access'] = FALSE;
    $form['field_link']['#description'] = NULL;
  }

  /**
   * Implements hook_ENTITY_TYPE_build_defaults_alter() for node.
   */
  #[Hook('node_build_defaults_alter')]
  public function alterArticleLink(array &$build, EntityInterface $entity, $view_mode): void {
    if ($entity->getEntityTypeId() !== 'node' || $entity->bundle() !== 'article' || $entity->isNew()) {
      return;
    }

    if ($field_link = $entity->get('field_link')) {
      foreach ($field_link as $item) {
        $item->uri = $entity->toUrl('canonical')->toUriString();
      }
    }
  }

  /**
   * Implements hook_form_BASE_FORM_ID_alter() for node_form.
   *
   * Stage extra is only available for the front page.
   */
  #[Hook('form_node_form_alter')]
  public function alterStageExtra(array &$form, FormStateInterface $form_state, string $form_id): void {
    if (!isset($form['field_stage_extra'])) {
      return;
    }

    /** @var \Drupal\node\NodeInterface $entity */
    $entity = $form_state->getFormObject()->getEntity();
    if ($entity->isNew()) {
      $form['field_stage_extra']['#access'] = FALSE;
      return;
    }

    $url = $entity->toUrl('canonical');
    if (!$url->isExternal()) {
      $path = '/' . $url->getInternalPath();
      $form['field_stage_extra']['#access'] = $form['field_stage_extra']['#access']
        && $this->pathMatcher->matchPath($path, '<front>');
    }
  }

  /**
   * Implements hook_field_group_form_process_build_alter().
   *
   * Allow placing 'path' element into a 'details_sidebar' field group.
   */
  #[Hook('field_group_form_process_build_alter')]
  public function alterPathGroup(array &$element, FormStateInterface $formState, array &$form): void {
    $path_group = array_find($element['#fieldgroups'], static fn (object $group)
      => in_array('path', $group->children));
    if ($path_group !== NULL) {
      $element['path']['widget'][0]['#type'] = 'container';
      foreach (Element::children($element['path']['widget'][0]) as $child) {
        $element['path']['widget'][0][$child]['#group'] = $path_group->group_name;
      }
    }
  }

  /**
   * Implements hook_form_BASE_FORM_ID_alter() for node_layout_builder_form.
   */
  #[Hook('form_node_layout_builder_form_alter')]
  public function alterLayoutBuilderForm(array &$form, FormStateInterface $form_state, string $form_id): void {
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $form_state->getFormObject()->getEntity();

    $form['revision_information']['#weight'] = 0;
    $form['revision_information']['#open'] = FALSE;

    $form['content_moderation'] = [
      '#type' => 'details',
      '#title' => $this->t('Content Moderation'),
      '#group' => 'advanced',
      '#weight' => -10,
    ];
    $form['moderation_state']['#group'] = 'content_moderation';

    $form['actions']['#weight'] = 100;
    $form['actions']['revert']['#attributes']['style'] = 'margin-left: auto;';

    $form['preview_toggle'] = &$form['actions']['preview_toggle'];
    unset($form['actions']['preview_toggle']);
    $form['preview_toggle']['#weight'] = 110;

    if (
      $entity->isTranslatable() && !$entity->isDefaultTranslation()
      && isset($form['layout_builder_message']['message']['#message_list'])
    ) {
      // Inform the user that the site uses layout_builder_st.
      $route_name = 'layout_builder.overrides.' . $entity->getEntityTypeId() . '.view';
      $form['layout_builder_message']['message']['#message_list']['warning'][] = $this->t('This is a translated version of this content. The layout can only be changed on the <a href=":url">original translation</a>.', [
        ':url' => Url::fromRoute($route_name, [
          $entity->getEntityTypeId() => $entity->id(),
        ], [
          'language' => $entity->getUntranslated()->language(),
        ])->toString(),
      ]);
    }
  }

}
