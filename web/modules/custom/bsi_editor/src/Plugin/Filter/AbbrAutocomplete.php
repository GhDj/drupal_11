<?php

declare(strict_types=1);

namespace Drupal\bsi_editor\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Text filter to replace <abbr title="<term uuid>"> with title from entity.
 */
#[Filter(
  id: 'bsi_editor_abbr_autocomplete',
  title: new TranslatableMarkup('Abbreviation autocomplete'),
  type: FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
)]
final class AbbrAutocomplete extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a new AbbrAutocomplete instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    $result = new FilterProcessResult($text);
    if (stristr($text, '<abbr title="') === FALSE) {
      return $result;
    }

    $dom = Html::load($text);
    $xpath = new \DOMXPath($dom);
    $nodes = $xpath->query('//abbr[@title]');

    $labels_by_uuid = [];
    foreach ($nodes as $node) {
      /** @var \DOMElement $node */
      $title = $node->getAttribute('title');
      if (preg_match('/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$/i', $title)) {
        $labels_by_uuid[$title] = FALSE;
      }
    }
    if (empty($labels_by_uuid)) {
      return $result;
    }

    /** @var array<\Drupal\taxonomy\TermInterface> $entities */
    $entities = $this->entityTypeManager->getStorage('taxonomy_term')
      ->loadByProperties(['uuid' => array_keys($labels_by_uuid)]);
    foreach ($entities as $entity) {
      $labels_by_uuid[$entity->uuid()] = $entity->getDescription();
    }

    foreach ($nodes as $node) {
      /** @var \DOMElement $node */
      $uuid = $node->getAttribute('title');
      match ($title = ($labels_by_uuid[$uuid] ?? NULL)) {
        // Title was not a uuid.
        NULL => NULL,
        // Uuid could not be resolved.
        FALSE => $node->removeAttribute('title'),
        default => $node->setAttribute('title', $title),
      };
    }

    return $result->setProcessedText(Html::serialize($dom));
  }

}
