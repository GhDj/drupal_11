<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a sticky block to display contact information.
 */
#[Block(
  id: 'bsi_custom_sticky_contact',
  admin_label: new TranslatableMarkup('Sticky Contact'),
  category: new TranslatableMarkup('BSI'),
)]
final class StickyContactBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the plugin instance.
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
  public function defaultConfiguration(): array {
    return [
      'default' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    if ($default = $this->configuration['default']) {
      $default = $this->entityTypeManager->getStorage('media')
        ->load($default);
    }

    $form['default'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Default contact'),
      '#target_type' => 'media',
      '#selection_settings' => [
        'target_bundles' => ['contact'],
      ],
      '#default_value' => $default,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['default'] = $form_state->getValue('default');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')
      ->loadByProperties(['vid' => 'contact_topics']);
    if ($default = $this->configuration['default']) {
      $default = $this->entityTypeManager->getStorage('media')
        ->load($default);
    }

    $metadata = CacheableMetadata::createFromRenderArray([])
      ->addCacheTags(['taxonomy_term_list:contact_topics']);

    $contacts = $default ? [$default] : [];
    foreach ($terms as $term) {
      $metadata->addCacheableDependency($term);
      $contact = $term->get('field_contact')?->entity;
      if ($contact && !in_array($contact, $contacts)) {
        $contacts[] = $contact;
      }
    }
    if ($contacts === []) {
      return [];
    }

    $build_list = $this->buildContacts($contacts);
    foreach (Element::children($build_list) as $key) {
      $contact_id = $build_list[$key]['#media']->id();
      $build_list[$key]['#contact_topics'] = array_filter($terms, static fn (TermInterface $term)
        => $term->get('field_contact')?->target_id === $contact_id);
    }

    $build['items'] = $build_list;
    $build['#terms'] = $terms;
    $build['#default_contact'] = $default;
    $metadata->applyTo($build);

    return $build;
  }

  /**
   * Build a render array for the provided entities.
   *
   * @param array<int, \Drupal\media\MediaInterface> $contacts
   *   The contact media entities.
   */
  public function buildContacts(array $contacts): array {
    $view_builder = $this->entityTypeManager->getViewBuilder('media');
    $build_list = $view_builder->viewMultiple($contacts, 'sticky');
    foreach ($build_list['#pre_render'] ?? [] as $callable) {
      $build_list = $callable($build_list);
    }
    unset($build_list['#pre_render'], $build_list['#sorted']);
    return $build_list;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

}
