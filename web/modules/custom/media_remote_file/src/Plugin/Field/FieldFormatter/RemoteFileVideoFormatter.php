<?php

declare(strict_types=1);

namespace Drupal\media_remote_file\Plugin\Field\FieldFormatter;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Mime\MimeTypeGuesserInterface;

/**
 * Plugin implementation of the 'Remote Video' formatter.
 */
#[FieldFormatter(
  id: 'remote_file_video',
  label: new TranslatableMarkup('Remote Video'),
  field_types: ['link'],
)]
class RemoteFileVideoFormatter extends FormatterBase {

  public function __construct(
    string $plugin_id,
    array $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    protected readonly EntityFieldManagerInterface $entityFieldManager,
    protected readonly MimeTypeGuesserInterface $mimeTypeGuesser,
    protected readonly RendererInterface $renderer,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_field.manager'),
      $container->get('file.mime_type.guesser'),
      $container->get('renderer'),
    );
  }

  /**
   * Gets the HTML tag for the formatter.
   *
   * @return string
   *   The HTML tag of this formatter.
   */
  protected function getHtmlTag(): string {
    return 'video';
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'controls' => TRUE,
      'autoplay' => FALSE,
      'loop' => FALSE,
      'muted' => FALSE,
      'playsinline' => FALSE,
      'multiple_file_display_type' => 'tags',
      'poster_field' => NULL,
      'track_field' => NULL,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $toggleAttributes = [
      'controls' => $this->t('Show playback controls'),
      'autoplay' => $this->t('Autoplay'),
      'loop' => $this->t('Loop'),
    ];
    if ($this->getHtmlTag() === 'video') {
      $toggleAttributes['muted'] = $this->t('Muted');
      $toggleAttributes['playsinline'] = $this->t('Plays Inline');
    }
    foreach ($toggleAttributes as $key => $label) {
      $form[$key] = [
        '#title' => $label,
        '#type' => 'checkbox',
        '#default_value' => $this->getSetting($key),
      ];
    }

    $form['multiple_file_display_type'] = [
      '#title' => $this->t('Display of multiple files'),
      '#type' => 'radios',
      '#options' => [
        'tags' => $this->t('Use multiple @tag tags, each with a single source.', ['@tag' => '<' . $this->getHtmlTag() . '>']),
        'sources' => $this->t('Use multiple sources within a single @tag tag.', ['@tag' => '<' . $this->getHtmlTag() . '>']),
      ],
      '#default_value' => $this->getSetting('multiple_file_display_type'),
    ];

    $bundle_fields = $this->entityFieldManager->getFieldDefinitions($form['#entity_type'], $form['#bundle']);

    if ($this->getHtmlTag() === 'video') {
      $poster_options = [];
      foreach ($bundle_fields as $field_name => $field_definition) {
        if ($field_definition->getType() === 'image') {
          $poster_options[$field_name] = $field_definition->getLabel();
        }
      }
      $form['poster_field'] = [
        '#title' => $this->t('Poster image'),
        '#description' => $this->t('Rendering the image will use the configuration of the selected field in this display.'),
        '#type' => 'select',
        '#options' => $poster_options,
        '#empty_option' => $this->t('- None -'),
        '#default_value' => $this->getSetting('poster_field'),
      ];
    }

    $track_options = [];
    foreach ($bundle_fields as $field_name => $field_definition) {
      if ($field_definition->getType() === 'file') {
        $track_options[$field_name] = $field_definition->getLabel();
      }
    }
    $form['track_field'] = [
      '#title' => $this->t('Tracks'),
      '#type' => 'select',
      '#options' => $track_options,
      '#empty_option' => $this->t('- None -'),
      '#default_value' => $this->getSetting('track_field'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];

    $summary[] = $this->t('Playback controls: %controls', ['%controls' => $this->getSetting('controls') ? $this->t('visible') : $this->t('hidden')]);
    $summary[] = $this->t('Autoplay: %autoplay', ['%autoplay' => $this->getSetting('autoplay') ? $this->t('yes') : $this->t('no')]);
    $summary[] = $this->t('Loop: %loop', ['%loop' => $this->getSetting('loop') ? $this->t('yes') : $this->t('no')]);
    if ($this->getHtmlTag() === 'video') {
      $summary[] = $this->t('Muted: %muted', ['%muted' => $this->getSetting('muted') ? $this->t('yes') : $this->t('no')]);
      $summary[] = $this->t('Plays Inline: %playsinline', ['%playsinline' => $this->getSetting('playsinline') ? $this->t('yes') : $this->t('no')]);
    }

    $summary[] = match ($this->getSetting('multiple_file_display_type')) {
      'tags' => $this->t('Multiple file display: Multiple HTML tags'),
      'sources' => $this->t('Multiple file display: One HTML tag with multiple sources'),
    };

    if ($poster_field = $this->getSetting('poster_field')) {
      // @todo Display warning when poster field is not configured in the view mode.
      $summary[] = new FormattableMarkup('@label: %value', [
        '@label' => $this->t('Poster'),
        '%value' => $poster_field,
      ]);
    }
    if ($track_field = $this->getSetting('track_field')) {
      $summary[] = new FormattableMarkup('@label: %value', [
        '@label' => $this->t('Track'),
        '%value' => $track_field,
      ]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];

    $attributes = $this->prepareAttributes();
    $entity = $items->getEntity();
    foreach ($this->getSources($items) as $delta => $source) {
      // @todo Add source url as cacheable dependency.
      $elements[$delta] = [
        '#theme' => $this->getPluginId(),
        '#attributes' => $attributes,
        '#sources' => $source,
      ];
      if (($field_name = $this->getSetting('poster_field'))
        && ($field = $entity->get($field_name)) && !$field->isEmpty()) {
        $elements[$delta]['#poster'] = $field->view($this->viewMode);
        $attributes->setAttribute('poster', $this->getPosterUrl($elements[$delta]['#poster']));
      }
      if (($field_name = $this->getSetting('track_field'))
        && ($field = $entity->get($field_name)) && !$field->isEmpty()) {
        $elements[$delta]['#tracks'] = $this->getTracks($field);
      }
    }

    return $elements;
  }

  /**
   * Extract the video image url from a render array.
   *
   * @param array $element
   *   The rendered thumbnail field.
   *
   * @return string
   *   The url to use in the video poster attribute.
   */
  protected function getPosterUrl(array $element): string {
    $rendered = (string) $this->renderer->render($element);
    $plain = trim(strip_tags($rendered));
    if ($plain !== '') {
      return $plain;
    }

    $dom = Html::load($rendered);
    $xpath = new \DOMXPath($dom);
    return $xpath->query('//img[@src]')->item(0)->getAttribute('src');
  }

  /**
   * Prepare the attributes according to the settings.
   *
   * @return \Drupal\Core\Template\Attribute
   *   Container with all the attributes for the HTML tag.
   *
   * @see Drupal\file\Plugin\Field\FieldFormatter\FileMediaFormatterBase::prepareAttributes
   */
  protected function prepareAttributes(): Attribute {
    $attributes = new Attribute();
    $toggleAttributes = ['controls', 'autoplay', 'loop'];
    if ($this->getHtmlTag() === 'video') {
      $toggleAttributes = array_merge($toggleAttributes, ['muted', 'playsinline']);
    }
    foreach ($toggleAttributes as $attribute) {
      if ($this->getSetting($attribute)) {
        $attributes->setAttribute($attribute, $attribute);
      }
    }

    if ($width = $this->getSetting('width')) {
      $attributes->setAttribute('width', $width);
    }
    if ($height = $this->getSetting('height')) {
      $attributes->setAttribute('height', $height);
    }

    return $attributes;
  }

  /**
   * Gets source files with attributes.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The item list.
   *
   * @return array
   *   Numerically indexed array, which again contains an associative array with
   *   the following key/values:
   *     - url => \Drupal\Core\Url
   *     - source_attributes => \Drupal\Core\Template\Attribute
   */
  protected function getSources(FieldItemListInterface $items): array {
    $sources = [];
    // Because we can have the files grouped in a single media tag, we do a
    // grouping in case the multiple file behavior is not 'tags'.
    foreach ($items as $item) {
      // @todo Improve url handling, e.g. HEAD requests, ignore fragments/queries, ...
      $url = Url::fromUri($item->uri)->toString();
      $mime_type = $this->mimeTypeGuesser->guessMimeType($url);
      if (!$mime_type) {
        // @todo Handle when we add support for urls without file extension.
        continue;
      }

      $attributes = new Attribute();
      $attributes
        ->setAttribute('src', $url)
        ->setAttribute('type', $mime_type);

      $source = [
        'url' => $url,
        'attributes' => $attributes,
      ];
      if ($this->getSetting('multiple_file_display_type') === 'tags') {
        $sources[] = [$source];
      }
      else {
        $sources[0][] = $source;
      }
    }

    return $sources;
  }

  /**
   * Extract subtitle/caption tracks from all translations.
   *
   * @param \Drupal\Core\Field\EntityReferenceFieldItemListInterface $items
   *   The field item list.
   *
   * @return array
   *   The list of track items to add to the element.
   */
  protected function getTracks(EntityReferenceFieldItemListInterface $items): array {
    $tracks = [];
    /** @var \Drupal\file\Plugin\Field\FieldType\FileItem $item */
    foreach ($items as $item) {
      /** @var \Drupal\file\Entity\File $file */
      $file = $item->entity;
      foreach ($file->getTranslationLanguages(TRUE) as $language) {
        $translation = $file->getTranslation($language->getId());
        $url = $translation->createFileUrl();

        $attributes = new Attribute();
        $attributes
          ->setAttribute('kind', $this->getHtmlTag() === 'video' ? 'subtitles' : 'captions')
          ->setAttribute('src', $url)
          ->setAttribute('srclang', $language->getId())
          ->setAttribute('label', $language->getName());
        if ($language->getId() === $item->getLangcode() && ($label = $item->description)) {
          $attributes->setAttribute('label', $label);
        }

        $tracks[] = [
          'url' => $url,
          'attributes' => $attributes,
        ];
      }
    }
    return $tracks;
  }

}
