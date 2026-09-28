<?php

declare(strict_types=1);

namespace Drupal\bsi_media\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\rabbit_hole\BehaviorSettingsManagerInterface;
use Drupal\rabbit_hole\FormManglerService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Hooks related to node behavior and form customization.
 */
class MediaRabbitHoleHooks {

  use StringTranslationTrait;

  public function __construct(
    #[Autowire(service: 'rabbit_hole.behavior_settings_manager')]
    protected readonly BehaviorSettingsManagerInterface $behaviorManager,
  ) {}

  /**
   * Implements hook_form_BASE_FORM_ID_alter() for media_form.
   */
  #[Hook('form_media_form_alter')]
  public function alterRabbitHole(array &$form, FormStateInterface $form_state, string $form_id): void {
    if (!isset($form['rabbit_hole']['rh_action'])) {
      return;
    }

    /** @var \Drupal\media\MediaInterface $entity */
    $entity = $form_state->getFormObject()->getEntity();

    $bundle_entity = $entity->getBundleEntity();
    $bundle_default = $this->behaviorManager->loadBehaviorSettingsAsConfig(
      $bundle_entity->getEntityTypeId(),
      $bundle_entity->id()
    );

    $bundle_action = $bundle_default->get('action');

    $options = $form['rabbit_hole']['rh_action']['#options'];
    $options = array_intersect_key($options, array_flip(['display_page', 'page_not_found']));
    $options[FormManglerService::RABBIT_HOLE_USE_DEFAULT] = $options[$bundle_action];
    unset($options[$bundle_action]);
    $form['rabbit_hole']['rh_action']['#options'] = $options;

    $form['rabbit_hole']['#title'] = $this->t('Visibility');
    $form['rabbit_hole']['#description'] = $this->t('This content will only be displayed in the public media library if the standalone page is activated.');
  }

}
