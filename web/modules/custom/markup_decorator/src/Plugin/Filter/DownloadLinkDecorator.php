<?php

declare(strict_types=1);

namespace Drupal\markup_decorator\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\file\FileInterface;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;
use Drupal\media\MediaInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Decorate download links by adding attributes.
 *
 * @todo Consider adding 'download' attribute.
 * @todo Also detect direct links to files?
 * @todo Add support for rabbit_hole?
 */
#[Filter(
  id: 'decorate_download_link',
  title: new TranslatableMarkup('Download link decorator'),
  type: FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
  settings: [
    'class' => '',
    'title' => '',
    'custom_title' => '',
  ],
)]
final class DownloadLinkDecorator extends FilterBase implements ContainerFactoryPluginInterface {

  protected const string LINK_TITLE_ENTITY_LABEL = 'name';
  protected const string LINK_TITLE_FILE_NAME = 'filename';
  protected const string LINK_TITLE_FILE_DESCRIPTION = 'description';
  protected const string LINK_TITLE_CUSTOM = 'custom';

  /**
   * Constructs a new BsiMediaViewModeAlign instance.
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
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form['help'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Alter download links by specifying additional attributes.'),
    ];

    $form['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('CSS class'),
      '#default_value' => $this->settings['class'],
    ];

    $form['title'] = [
      '#type' => 'select',
      '#title' => $this->t('Title text'),
      '#options' => [
        static::LINK_TITLE_ENTITY_LABEL => $this->t('Entity label'),
        static::LINK_TITLE_FILE_NAME => $this->t('File name'),
        static::LINK_TITLE_FILE_DESCRIPTION => $this->t('Replace the file name by its description when available'),
        static::LINK_TITLE_CUSTOM => $this->t('Custom'),
      ],
      '#empty_option' => $this->t('- None -'),
      '#default_value' => $this->settings['title'],
    ];
    $form['custom_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Custom text'),
      '#default_value' => $this->settings['custom_title'],
      '#states' => [
        'visible' => [
          ':input[name="filters[decorate_download_link][settings][title]"]'
            => ['value' => static::LINK_TITLE_CUSTOM],
        ],
        'required' => [
          ':input[name="filters[decorate_download_link][settings][title]"]'
            => ['value' => static::LINK_TITLE_CUSTOM],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    if (!array_diff($this->settings, $this->defaultConfiguration()['settings'])) {
      return new FilterProcessResult($text);
    }

    $html_dom = Html::load($text);
    $xpath = new \DOMXPath($html_dom);
    $nodes = $xpath->query('//a[@data-entity-type="media" and @data-entity-uuid]');

    $titles_by_uuid = [];
    foreach ($nodes as $node) {
      try {
        $url = Url::fromUri('internal:' . $node->getAttribute('href'));
      }
      catch (\Throwable $th) {
        continue;
      }
      // @todo Add support for download handlers provided by other modules.
      if (!$url->isRouted() || $url->getRouteName() !== 'media_entity_download.download') {
        continue;
      }

      $uuid = $node->getAttribute('data-entity-uuid');
      $titles_by_uuid[$uuid] = $node->getAttribute('title') ?: NULL;
    }
    if (empty($titles_by_uuid)) {
      return new FilterProcessResult($text);
    }

    $entities = $this->entityTypeManager->getStorage('media')
      ->loadByProperties(['uuid' => array_keys($titles_by_uuid)]);
    foreach ($entities as $entity) {
      $titles_by_uuid[$entity->uuid()] = $titles_by_uuid[$entity->uuid()]
        ?: $this->getLinkTitle($entity);
    }

    foreach ($nodes as $link) {
      $uuid = $node->getAttribute('data-entity-uuid');
      if (!array_key_exists($uuid, $titles_by_uuid)) {
        continue;
      }

      if ($titles_by_uuid[$uuid] !== NULL) {
        $link->setAttribute('title', $titles_by_uuid[$uuid]);
      }
      if ($this->settings['class'] !== '') {
        $class = $link->getAttribute('class') . ' ' . $this->settings['class'];
        $link->setAttribute('class', trim($class));
      }
    }

    return new FilterProcessResult(Html::serialize($html_dom));
  }

  /**
   * Get link title based on provided entity and filter settings.
   */
  private function getLinkTitle(MediaInterface $entity): ?string {
    if ($this->settings['title'] === '') {
      return NULL;
    }

    $items = $entity->get($entity->getSource()
      ->getSourceFieldDefinition($entity->getBundleEntity())
      ->getName());
    if (!$items instanceof EntityReferenceFieldItemList
      || !($file = $items->entity) || !$file instanceof FileInterface) {
      unset($file);
    }

    return match ($this->settings['title']) {
      static::LINK_TITLE_ENTITY_LABEL => $entity->label(),
      static::LINK_TITLE_FILE_NAME && $file !== NULL => $file->getFilename(),
      static::LINK_TITLE_FILE_DESCRIPTION && $items !== NULL
        => $items->first()?->get('description')->getValue() ?: $file->getFilename(),
      static::LINK_TITLE_CUSTOM => $this->settings['custom_title'],
      default => NULL,
    };
  }

}
