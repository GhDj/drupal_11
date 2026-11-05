<?php

declare(strict_types=1);

namespace Drupal\bsi_csn\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure CSN settings for this site.
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'bsi_csn_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['bsi_csn.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['bsi_url'] = [
      '#type' => 'url',
      '#title' => $this->t('BSI homepage URL'),
      '#description' => $this->t('URL the BSI logo links to (opens in new tab).'),
      '#config_target' => 'bsi_csn.settings:bsi_url',
      '#required' => TRUE,
    ];

    $form['csn_dashboard_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('CSN dashboard URL'),
      '#description' => $this->t('URL the CSN logo links to. Leave empty to render the logo without a link.'),
      '#config_target' => 'bsi_csn.settings:csn_dashboard_url',
    ];

    $form['logout_redirect_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Logout redirect URL'),
      '#description' => $this->t('URL to redirect to after logout (public login page on bsi.bund.de).'),
      '#config_target' => 'bsi_csn.settings:logout_redirect_url',
      '#required' => TRUE,
    ];

    $form['copyright_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Copyright text'),
      '#description' => $this->t('Copyright text displayed in the CSN footer.'),
      '#config_target' => 'bsi_csn.settings:copyright_text',
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

}
