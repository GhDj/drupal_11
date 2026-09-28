<?php

declare(strict_types=1);

namespace Drupal\short_url\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Render\Element\PathElement;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Short URL settings for this site.
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritDoc}
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typedConfigManager,
    private readonly PathValidatorInterface $pathValidator,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityFieldManagerInterface $entityFieldManager,
  ) {
    parent::__construct($config_factory, $typedConfigManager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('path.validator'),
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'short_url_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['short_url.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $options = $this->entityTypeManager->getDefinitions();
    $options = array_filter($options, static fn (EntityTypeInterface $entity_type)
      => $entity_type instanceof ContentEntityTypeInterface
        && $entity_type->hasLinkTemplate('canonical'));
    $form['entity_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Entity types'),
      '#description' => $this->t('Select entity types that may use the short URL feature.'),
      '#options' => array_map(static fn (EntityTypeInterface $entity_type) => $entity_type->getLabel(), $options),
      '#config_target' => 'short_url.settings:entity_types',
      '#element_validate' => [[static::class, 'validateCheckboxes']],
    ];

    $form['prefix'] = [
      '#type' => 'path',
      '#title' => $this->t('Prefix'),
      '#description' => array_map(static fn ($description) => [
        '#markup' => $description,
        '#suffix' => ' ',
      ], [
        $this->t('The prefix used to generate short URLs.'),
        $this->t('Use a relative path with a slash in front.'),
        $this->t('Path may be provided with or without trailing slash, depending on the desired urls.'),
      ]),
      '#validate_path' => FALSE,
      '#convert_path' => PathElement::CONVERT_NONE,
      '#config_target' => 'short_url.settings:prefix',
      '#placeholder' => '/',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Validation callback to remove unchecked options from stored data.
   */
  public static function validateCheckboxes(array &$element, FormStateInterface $form_state, array &$complete_form): void {
    $value = $form_state->getValue($element['#parents']);
    $value = array_values(array_filter($value));
    sort($value);
    $form_state->setValue($element['#parents'], $value);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $prefix = $form_state->getValue('prefix');
    if ($prefix === '') {
      return;
    }
    if (!str_starts_with($prefix, '/')) {
      $form_state->setErrorByName('prefix', $this->t('Use a relative path with a slash in front.'));
      return;
    }
    if ($this->pathValidator->isValid($prefix)) {
      $this->messenger()->addWarning($this->t('This site already uses the prefix %prefix. Ensure this is intentional.', [
        '%prefix' => $prefix,
      ]));
    }
  }

  /**
   * {@inheritDoc}
   *
   * @todo Update action: remove or rewrite redirects using the old prefix.
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $config = $this->config('short_url.settings');
    $old_prefix = $config->getOriginal('prefix');
    $old_entity_types = $config->getOriginal('entity_types');
    parent::submitForm($form, $form_state);

    if ($old_entity_types !== $config->get('entity_types')) {
      $this->entityFieldManager->clearCachedFieldDefinitions();
    }

    $prefix = $config->get('prefix');
    if ($prefix === $old_prefix) {
      return;
    }
    if ($prefix === '') {
      $this->messenger()->addWarning($this->t('Short URLs may no longer be generated. Consider deleting existing short URLs using the <a href=":page_url">@page_title</a>.', [
        ':page_url' => Url::fromRoute('redirect.list', [
          'redirect_source__path' => ltrim($old_prefix, '/'),
        ])->toString(),
        '@page_title' => $this->t('URL Redirects'),
      ]));
    }
  }

}
