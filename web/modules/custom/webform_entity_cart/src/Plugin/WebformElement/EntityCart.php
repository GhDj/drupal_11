<?php

namespace Drupal\webform_entity_cart\Plugin\WebformElement;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\Url;
use Drupal\webform\Plugin\WebformElementBase;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform_entity_cart\WebformEntityCartInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Webform element to manage an entity-based cart.
 *
 * @WebformElement(
 *   id = "webform_entity_cart",
 *   label = @Translation("Entity cart"),
 *   description = @Translation("Render a table to manage an entity cart."),
 *   category = @Translation("Advanced elements"),
 * )
 */
class EntityCart extends WebformElementBase implements TrustedCallbackInterface {

  /**
   * The webform_entity_cart service.
   */
  private readonly WebformEntityCartInterface $cart;

  /**
   * The renderer service.
   */
  private readonly RendererInterface $renderer;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->cart = $container->get(WebformEntityCartInterface::class);
    $instance->renderer = $container->get(RendererInterface::class);
    return $instance;
  }

  /**
   * {@inheritDoc}
   */
  public function isInput(array $element): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  protected function defineDefaultProperties(): array {
    return [
      'empty' => $this->t('Your cart is empty.'),
    ] + parent::defineDefaultProperties();
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    $form['element']['empty'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Empty text'),
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function prepare(array &$element, ?WebformSubmissionInterface $webform_submission = NULL): void {
    parent::prepare($element, $webform_submission);

    $element['#prefix'] = '<div id="entity-cart-wrapper">';
    $element['#suffix'] = '</div>';

    $element['#type'] = 'table';
    $element['#input'] = TRUE;
    $element['#tree'] = TRUE;
    $element['#header'] = [
      'item' => $this->t('Item'),
      'count' => $this->t('Count'),
      'actions' => $this->t('Actions'),
    ];

    // Apply empty text setting.
    $empty = $this->getDefaultProperty('empty');
    $element['#empty'] = $empty ?? $this->t('Your cart is empty.');

    CacheableMetadata::createFromRenderArray($element)
      ->addCacheableDependency($this->cart)
      ->applyTo($element);

    $element['#attached']['library'][] = 'webform_entity_cart/script';

    // Using children over #rows to get automatic value processing.
    $data = $this->cart->getCart();
    $element = array_merge($element, $this->buildRows($data));
  }

  /**
   * Builds all rows for the cart table.
   */
  public function buildRows(array $data): array {
    $rows = [];
    foreach ($data as $entity_type_id => $items) {
      $view_builder = $this->entityTypeManager->getViewBuilder($entity_type_id);
      $entities = $this->getEntityStorage($entity_type_id)->loadMultiple(array_keys($items));

      foreach ($items as $entity_id => $count) {
        if (!isset($entities[$entity_id])) {
          $this->messenger()->addWarning($this->t('Some items were removed from your cart.'));
          continue;
        }

        $entity = $entities[$entity_id];
        $rows[] = [
          'entity' => $view_builder->view($entity, 'cart') + [
            '#type' => 'value',
            '#value' => $entity,
          ],
          'count' => [
            '#type' => 'number',
            '#theme' => 'input__number__cart_item',
            '#title' => $this->t('Count'),
            '#title_display' => 'invisible',
            '#default_value' => $count,
            '#attributes' => [
              'class' => ['js-cart-update'],
              'data-url' => Url::fromRoute('webform_entity_cart.update', [
                'operation' => 'update',
                'entity_type_id' => $entity_type_id,
                'entity_id' => $entity_id,
              ])->toString(),
            ],
            '#min' => 1,
            '#step' => 1,
          ],
          'actions' => [
            '#type' => 'submit',
            '#value' => $this->t('Remove'),
            '#name' => 'remove_' . $entity->id(),
            '#ajax' => [
              'callback' => [static::class, 'refreshCart'],
              'wrapper' => 'entity-cart-wrapper',
            ],
            '#submit' => [
              [static::class, 'removeItemSubmit'],
            ],
            '#entity_type_id' => $entity->getEntityTypeId(),
            '#entity_id' => $entity->id(),
            '#cart_parent_key' => $element['#webform_parent_key'] ?? NULL,
            '#cart_key' => $element['#webform_key'] ?? NULL,
          ],
        ];
      }
    }

    return $rows;
  }

  /**
   * Ensures item is rendered properly during checkout.
   */
  protected function formatHtmlItem(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    $value = $this->getValue($element, $webform_submission, $options);

    // This method is triggered by various methods but the
    // value is not always in the same structure.
    // We are serializing this value on save which means that
    // after submitting the form, when this method is called we need
    // to unserialize the value.
    if (!is_array($value)) {
      $value = json_decode($value, TRUE);
    }

    $table = [
      '#type' => 'table',
      '#header' => [
        $this->t('Item'),
        $this->t('Count'),
      ],
      '#rows' => [],
    ];

    foreach ($value as $row) {
      $entity = $row['entity'];
      if (!$entity instanceof EntityInterface) {
        $entity = $this->getEntityStorage($row['entity']['entity_type_id'])->load($row['entity']['entity_id']);
      }
      $view_builder = $this->entityTypeManager->getViewBuilder($entity->getEntityTypeId());

      $entity_build = $view_builder->view($entity, 'cart');

      $table['#rows'][] = [
        [
          'data' => $entity_build,
        ],
        [
          'data' => $row['count'],
        ],
      ];

    }

    return (string) $this->renderer->renderInIsolation($table);
  }

  /**
   * {@inheritDoc}
   */
  protected function prepareElementValidateCallbacks(array &$element, ?WebformSubmissionInterface $webform_submission = NULL): void {
    parent::prepareElementValidateCallbacks($element, $webform_submission);
    $element['#element_validate'][] = [$this::class, 'elementValidate'];
  }

  /**
   * Validation handler for the form element.
   */
  public static function elementValidate(&$element, FormStateInterface $form_state, &$complete_form): void {
    // We need to save the value as a string
    // as that is what the code expects.
    // If we do not do this we get a warning from
    // saveData method.
    // This value is then unserialized in the prepare method.
    $value = $form_state->getValue($element['#webform_key']);
    if (!empty($value)) {
      foreach ($value as $key => $item) {
        if (empty($item['entity'])) {
          continue;
        }
        $entity = $item['entity'];
        $value[$key]['entity'] = [
          'entity_type_id' => $entity->getEntityTypeId(),
          'entity_id' => $entity->id(),
        ];
      }
    }

    // Needed to remove initial load error.
    $form_state->setValue($element['#webform_key'], json_encode($value));

  }

  /**
   * {@inheritdoc}
   */
  public function postSave(array &$element, WebformSubmissionInterface $webform_submission, $update = TRUE) {
    parent::postSave($element, $webform_submission, $update);

    // After cart form is submitted we clear the cart.
    $this->cart->clear();
  }

  /**
   * Refreshes the cart.
   */
  public static function refreshCart(array &$form, FormStateInterface $form_state): array {
    $form_state->setRebuild(TRUE);

    $trigger = $form_state->getTriggeringElement();

    $parent_key = $trigger['#cart_parent_key'] ?? NULL;
    $element_key = $trigger['#cart_key'] ?? NULL;

    if (
      $parent_key &&
      $element_key &&
      isset($form['elements'][$parent_key][$element_key])
    ) {
      return $form['elements'][$parent_key][$element_key];
    }

    return $form['elements']['page_cart'];
  }

  /**
   * Remove item from cart.
   */
  public static function removeItemSubmit(array &$form, FormStateInterface $form_state): void {
    $trigger = $form_state->getTriggeringElement();

    $entity_type_id = $trigger['#entity_type_id'];
    $entity_id = $trigger['#entity_id'];

    $entity = \Drupal::entityTypeManager()
      ->getStorage($entity_type_id)
      ->load($entity_id);

    \Drupal::service(WebformEntityCartInterface::class)
      ->remove(
        $entity,
        WebformEntityCartInterface::COUNT_ANY
      );

    \Drupal::messenger()->addStatus(t('Cart was updated.'));

    $form_state->setRebuild(TRUE);
  }

}
