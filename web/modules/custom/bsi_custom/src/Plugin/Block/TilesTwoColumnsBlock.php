<?php

namespace Drupal\bsi_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\media\Entity\Media;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Tiles (2 columns) block.
 *
 * @Block(
 *   id = "bsi_custom_tiles_two_columns",
 *   admin_label = @Translation("Tiles (2 columns)"),
 *   category = @Translation("BSI")
 * )
 */
class TilesTwoColumnsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the block plugin.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    $plugin_definition,
    protected readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'variant' => 'text',
      'headline' => '',
      'body' => '',
      'metric' => '',
      'media_id' => NULL,
      'ctas' => [
        'title' => '',
        'url' => '',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $current_media = NULL;

    if (!empty($this->configuration['media_id'])) {
      $current_media = $this->entityTypeManager->getStorage('media')->load($this->configuration['media_id']);
    }

    $form['variant'] = [
      '#type' => 'select',
      '#title' => $this->t('Variant'),
      '#options' => [
        'text' => $this->t('Text'),
        'media_file' => $this->t('Image/Infographic'),
        'metric' => $this->t('Metric'),
      ],
      '#default_value' => $this->configuration['variant'],
    ];

    $form['metric'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Key figure'),
      '#default_value' => $this->configuration['metric'],
      '#states' => [
        'visible' => [
          ':input[name="settings[variant]"]' => [
            'value' => 'metric',
          ],
        ],
      ],
    ];

    $form['headline'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Headline'),
      '#default_value' => $this->configuration['headline'],
    ];

    $form['media_settings'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          ':input[name="settings[variant]"]' => [
            'value' => 'media_file',
          ],
        ],
      ],
    ];
    $form['media_settings']['media_id'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Existing image'),
      '#target_type' => 'media',
      '#selection_settings' => [
        'target_bundles' => [
          'image' => 'image',
        ],
      ],
      '#default_value' => $current_media,
    ];
    $form['media_settings']['media_upload'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload new image'),
      '#upload_location' => 'public://block_media/',
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg gif webp'],
      ],
    ];

    $form['body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Short text'),
      '#default_value' => $this->configuration['body'],
    ];

    $form['ctas'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Call to Action'),
    ];

    $form['ctas']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link text'),
      '#default_value' => $this->configuration['ctas']['title'] ?? '',
    ];

    $form['ctas']['url'] = [
      '#type' => 'url',
      '#title' => $this->t('Link URL'),
      '#default_value' => $this->configuration['ctas']['url'] ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {

    $this->configuration['variant'] = $form_state->getValue('variant');
    $this->configuration['headline'] = $form_state->getValue('headline');
    $this->configuration['metric'] = $form_state->getValue('metric');
    $this->configuration['body'] = $form_state->getValue('body');
    $this->configuration['ctas'] = [
      'title' => $form_state->getValue(['ctas', 'title']),
      'url' => $form_state->getValue(['ctas', 'url']),
    ];

    $uploaded_files = $form_state->getValue('media_upload');

    // Upload takes precedence over selected media.
    if (!empty($uploaded_files)) {

      $file = $this->entityTypeManager->getStorage('managed_file')->load(reset($uploaded_files));

      if ($file) {

        $file->setPermanent();
        $file->save();

        $media = Media::create([
          'bundle' => 'image',
          'name' => $file->getFilename(),
          'field_media_image' => [
            'target_id' => $file->id(),
            'alt' => $file->getFilename(),
          ],
        ]);

        $media->save();

        $this->configuration['media_id'] = $media->id();
      }
    }
    else {

      $media_id = $form_state->getValue('media_id');

      $this->configuration['media_id'] = !empty($media_id)
        ? $media_id
        : NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $media_render = [];

    if (!empty($this->configuration['media_id'])) {

      $media = $this->entityTypeManager
        ->getStorage('media')
        ->load($this->configuration['media_id']);

      if ($media) {
        $media_render = $this->entityTypeManager
          ->getViewBuilder('media')
          ->view($media, 'default');
      }
    }

    return [
      '#variant' => $this->configuration['variant'],
      '#headline' => $this->configuration['headline'],
      '#metric' => $this->configuration['metric'],
      '#body' => $this->configuration['body'],
      '#image' => $media_render,
      '#cta_title' => $this->configuration['ctas']['title'] ?? '',
      '#cta_url' => $this->configuration['ctas']['url'] ?? '',
    ];
  }

}
