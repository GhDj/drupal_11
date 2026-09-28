<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Hooks related to menus.
 */
class MenuHooks implements TrustedCallbackInterface {

  const int MAX_LINK_DEPTH = 2;

  const string MAIN_MENU_NAME = 'main';

  public function __construct(
    protected readonly PathMatcherInterface $pathMatcher,
    protected readonly MenuLinkTreeInterface $menuLinkTree,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks(): array {
    return ['overrideViewMode'];
  }

  /**
   * Implements hook_entity_prepare_view() to set link field titles.
   */
  #[Hook('entity_prepare_view')]
  public function alterMenuLinkContent($entity_type_id, array $entities, array $displays, $view_mode): void {
    if ($entity_type_id !== 'menu_link_content') {
      return;
    }
    foreach ($entities as $entity) {
      $entity->get('link')->title = $entity->label();
    }
  }

  /**
   * Callback to override the view mode of menu_link_custom in main navigation.
   */
  public static function overrideViewMode(array $element): array {
    $element['content']['#view_mode'] = 'primary';
    return $element;
  }

  /**
   * Implements hook_block_view_BASE_BLOCK_ID_alter() for system_menu_block.
   */
  #[Hook('block_view_system_menu_block_alter')]
  public function alterMenuViewMode(array &$build, BlockPluginInterface $block): void {
    if ($block->getDerivativeId() !== 'main' || $block->getConfiguration()['expand_all_items'] !== TRUE) {
      return;
    }
    $build['#pre_render'][] = [static::class, 'overrideViewMode'];
  }

  /**
   * Implements hook_form_BASE_FORM_ID_alter() for menu_link_content.
   *
   * Menu extra is only available under certain conditions.
   */
  #[Hook('form_menu_link_content_form_alter')]
  public function alterMenuExtra(array &$form, FormStateInterface $form_state, string $form_id): void {
    if (!isset($form['field_menu_extra'])) {
      return;
    }

    // Depth can be changed while editing the content, rely on frontend checks.
    $form['field_menu_extra']['#states']['visible'][] = [
      ':input[name="menu_parent"]' => ['value' => static::MAIN_MENU_NAME . ':'],
    ];

    /** @var \Drupal\menu_link_content\MenuLinkContentInterface $entity */
    $entity = $form_state->getFormObject()->getEntity();
    if ($entity->isNew()) {
      return;
    }

    // ...but the number of child levels does not change.
    $link_id = 'menu_link_content:' . $entity->uuid();
    $form['field_menu_extra']['#access'] = $form['field_menu_extra']['#access']
      && $this->menuLinkTree->getSubtreeHeight($link_id) <= static::MAX_LINK_DEPTH;
  }

}
