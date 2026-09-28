<?php

declare(strict_types=1);

namespace Drupal\bsi_social\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure BSI Social settings for this site.
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'bsi_social_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['bsi_social.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['mastodon_endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mastodon endpoint'),
      '#config_target' => 'bsi_social.settings:mastodon_endpoint',
      '#placeholder' => $this->t('https://:domain/api/v1/accounts/:id/statuses'),
    ];

    $form['cache_expire'] = [
      '#type' => 'number',
      '#title' => $this->t('Cache lifetime'),
      '#description' => $this->t('Cache lifetime in seconds.'),
      '#config_target' => 'bsi_social.settings:cache_expire',
      '#required' => TRUE,
      '#placeholder' => $this->t('300'),
    ];
    return parent::buildForm($form, $form_state);
  }

}
