<?php

namespace Drupal\short_url\Hook;

use Drupal\Component\Utility\Html;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\ContentEntityFormInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\short_url\ShortUrlHelper;

/**
 * Hooks that provide the short_url extra field to entities.
 */
class ExtraFieldHooks {

  use StringTranslationTrait;

  /**
   * The module configuration.
   */
  private readonly ImmutableConfig $config;

  public function __construct(
    protected readonly EntityTypeBundleInfoInterface $entityTypeBundleInfo,
    protected readonly EntityTypeManagerInterface $entityTypeManager,
    protected readonly ShortUrlHelper $shortUrlHelper,
    ConfigFactoryInterface $configFactory,
  ) {
    $this->config = $configFactory->get('short_url.settings');
  }

  /**
   * Implements hook_entity_extra_field_info().
   */
  #[Hook('entity_extra_field_info')]
  public function addExtraFields(): array {
    $extra = [];
    foreach ($this->config->get('entity_types') as $entity_type_id) {
      $bundle_ids = array_keys($this->entityTypeBundleInfo->getBundleInfo($entity_type_id));
      foreach ($bundle_ids as $bundle_id) {
        $extra[$entity_type_id][$bundle_id] = $this->addEntityExtraField();
      }
    }
    return $extra;
  }

  /**
   * Provide the extra field definition for the short url field.
   */
  private function addEntityExtraField(): array {
    $short_url = [
      'label' => $this->t('Short URL'),
      'description' => $this->t('The short URL for this entity.'),
      'weight' => 0,
      'visible' => FALSE,
    ];
    return [
      'form' => [
        'short_url' => $short_url,
      ],
      'display' => [
        'short_url' => $short_url,
      ],
    ];
  }

  /**
   * Implements hook_form_alter().
   */
  #[Hook('form_alter')]
  public function alterForm(array &$form, FormStateInterface $form_state, string $form_id): void {
    $form_object = $form_state->getFormObject();
    if (!$form_object instanceof ContentEntityFormInterface) {
      return;
    }
    $entity = $form_object->getEntity();
    if (!in_array($entity->getEntityTypeId(), $this->config->get('entity_types'))) {
      return;
    }
    $display = $form_object->getFormDisplay($form_state);
    if ($display->getOriginalMode() === 'delete' || !$display->getComponent('short_url')) {
      return;
    }

    $form['short_url'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#attributes' => [
        'class' => ['form-items-inline'],
      ],
    ];

    $info = $this->shortUrlHelper->getShortUrlInfo($entity);
    $form['short_url']['code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Short URL'),
      '#id' => Html::getId('short-url'),
      '#description' => $this->t('Enter a custom short code, or use the “Generate” button to get a random suggestion.'),
      '#field_prefix' => '/' . $info['prefix'],
      '#default_value' => $info['code'],
      '#size' => 10,

      '#disabled' => TRUE,
      '#attributes' => [
        'readonly' => 'readonly',
      ],
      '#element_validate' => [
        [static::class, 'validateShortUrl'],
      ],
    ];

    if (\Drupal::currentUser()->hasPermission('edit short_url')) {
      $form['actions']['submit']['#submit'][] = [static::class, 'updateShortUrl'];

      $form['short_url']['code']['#disabled'] = FALSE;
      unset($form['short_url']['code']['#attributes']['readonly']);

      $form['short_url']['generate'] = [
        '#type' => 'button',
        '#value' => $this->t('Generate'),
        '#ajax' => [
          'url' => Url::fromRoute('short_url.generate', [
            'element' => $form['short_url']['code']['#id'],
          ]),
          'event' => 'click',
        ],
        '#attributes' => [
          'title' => $this->t('Generate a unique short URL'),
        ],
        '#input' => FALSE,
        '#submit_button' => FALSE,
        '#limit_validation_errors' => TRUE,
      ];
    }
  }

  /**
   * Validation callback for the short_url extra field.
   */
  public static function validateShortUrl(array $element, FormStateInterface $form_state): void {
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $form_state->getFormObject()->getEntity();
    $helper = \Drupal::service('short_url.helper');

    $code = $form_state->getValue(['short_url', 'code']);
    if ($code && !$helper->isAvailable($code, $entity)) {
      $form_state->setError($element, t('The short code %code is already taken. Please choose or generate another one.', [
        '%code' => $code,
      ]));
    }
  }

  /**
   * Entity form builder to add the book information to the node.
   */
  public static function updateShortUrl(array &$form, FormStateInterface $form_state): void {
    if ($form_state->getValue(['short_url', 'code']) === $form['short_url']['code']['#default_value']) {
      // @todo This is a dirty workaround to avoid loading the redirect.
      return;
    }
    $entity = $form_state->getFormObject()->getEntity();
    $short_code = $form_state->getValue(['short_url', 'code']);

    /** @var \Drupal\short_url\ShortUrlHelper $short_url_helper */
    $short_url_helper = \Drupal::service('short_url.helper');
    $short_url_helper->updateShortUrl($entity, $short_code);
  }

  /**
   * Implements hook_entity_view().
   */
  #[Hook('entity_view')]
  public function prepareView(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display, $view_mode): void {
    if (!($component = $display->getComponent('short_url'))) {
      return;
    }

    if ($url = $this->shortUrlHelper->getEntityShortUrl($entity)) {
      $build['short_url'] = [
        '#theme' => 'link__short_url',
        '#title' => $this->t('Short URL'),
        '#url' => $url,
        '#weight' => $component['weight'],
      ];
    }
  }

}
