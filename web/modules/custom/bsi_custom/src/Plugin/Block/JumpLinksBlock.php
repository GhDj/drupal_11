<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

/**
 * Provides a sticky jump links block.
 */
#[Block(
  id: 'bsi_custom_jump_links',
  admin_label: new TranslatableMarkup('Sticky Jump Links'),
  category: new TranslatableMarkup('BSI'),
  context_definitions: [
    'node' => new EntityContextDefinition('entity:node'),
  ],
)]
final class JumpLinksBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly EntityRepositoryInterface $entityRepository,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    /** @var \Drupal\Core\Entity\FieldableEntityInterface|null $entity */
    $entity = $this->getContextValue('node');
    if ($entity === NULL || !$entity->hasField('field_paragraphs')) {
      return [];
    }
    if ($entity->getEntityTypeId() === 'node' && $entity->bundle() === 'article') {
      return [];
    }

    $links = [];
    foreach ($entity->get('field_paragraphs') as $item) {
      /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
      $paragraph = $this->entityRepository->getTranslationFromContext(
        $item->entity,
        $entity->language()->getId()
      );
      if (!$paragraph->hasField('field_jumplink') || $paragraph->get('field_jumplink')->isEmpty()) {
        continue;
      }

      $text = $paragraph->get('field_jumplink')->value;
      $link = new Link($text, Url::fromRoute('<none>', [], [
        'fragment' => 'anchor-' . Html::getId($text),
      ]));
      // Optional: Add #wrapper_attributes to add class to <li>.
      $links[] = $link->toRenderable();
    }

    if ($links === []) {
      return [];
    }

    $build['content'] = [
      '#theme' => 'item_list__jump_links',
      '#title' => new TranslatableMarkup('On this page:', [], [
        'context' => 'Jump links',
      ]),
      '#items' => $links,
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    /** @var \Drupal\Core\Entity\FieldableEntityInterface|null $entity */
    $entity = $this->getContextValue('node');
    return $entity->access('view', NULL, TRUE);
  }

}
