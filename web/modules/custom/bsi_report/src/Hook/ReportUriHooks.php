<?php

declare(strict_types=1);

namespace Drupal\bsi_report\Hook;

use Drupal\book\BookInterface;
use Drupal\book\BookOutlineStorageInterface;
use Drupal\bsi_report\Entity\Book;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Hook implementations related to Jahreslagebericht.
 */
class ReportUriHooks {

  use StringTranslationTrait;

  public function __construct(
    protected readonly ConfigFactoryInterface $configFactory,
    protected readonly BookOutlineStorageInterface $bookOutlineStorage,
  ) {}

  /**
   * Implements hook_entity_bundle_info_alter().
   */
  #[Hook('entity_bundle_info_alter')]
  public function entityBundleInfoAlter(array &$bundles): void {
    $allowed_types = $this->configFactory->get('book.settings')
      ->get('allowed_types') ?? [];
    foreach ($allowed_types as $type_config) {
      $bundles['node'][$type_config['content_type']]['class'] = Book::class;
    }
  }

  /**
   * Implements hook_form_FORM_ID_alter() for node_form.
   */
  #[Hook('form_node_form_alter')]
  public function alterForm(&$form, FormStateInterface $form_state, $form_id): void {
    /** @var \Drupal\node\Entity\Node $entity */
    $entity = $form_state->getFormObject()->getEntity();
    if (!$entity instanceof BookInterface) {
      return;
    }

    // New books may only be created from entry page nodes.
    if ($entity->bundle() !== 'entry_page') {
      unset($form['book']['bid']['#options'][$entity->id() ?? 'new']);
    }

    // Book pages may be unlinked in navigation.
    $form['book']['link_uri'] = [
      '#type' => 'radios',
      '#title' => $this->t('Link'),
      '#options' => [
        '' => $this->t('Link to node'),
        'route:<nolink>' => $this->t('Plain text'),
      ],
      '#default_value' => $entity->getBook()['link_uri'] ?? '',
      '#weight' => 10,
      '#states' => [
        'visible' => [
          [':input[name="book[create_new_book]"]' => ['checked' => TRUE]],
          [':input[name="book[bid]"]' => ['filled' => TRUE, '!value' => 0]],
        ],
      ],
    ];
  }

  /**
   * Implements hook_ENTITY_TYPE_update() for node.
   */
  #[Hook('node_insert')]
  #[Hook('node_update')]
  public function updateBookLinkUri(EntityInterface $node): void {
    if (!$node instanceof BookInterface || !($book = $node->getBook())) {
      return;
    }

    $book['link_uri'] = $book['link_uri'] ?? NULL;
    if (!$node->isNew() && $book['link_uri'] === ($node->getOriginal()?->getBook()['link_uri'] ?? NULL)) {
      return;
    }
    $this->bookOutlineStorage->update((int) $book['nid'], [
      'link_uri' => $book['link_uri'],
    ]);
  }

}
