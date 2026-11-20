<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\PathElement;

/**
 * Configure CSN settings for this site.
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'csn_custom_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['csn_custom.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['dashboard'] = [
      '#type' => 'path',
      '#title' => $this->t('Dashboard'),
      '#description' => $this->t('CSN dashboard path, used in the CSN branding.'),
      '#config_target' => 'csn_custom.settings:dashboard',
      '#convert_path' => PathElement::CONVERT_NONE,
      '#element_validate' => [
        '::validatePath',
      ],
    ];

    $form['logout_redirect'] = [
      '#type' => 'path',
      '#title' => $this->t('Logout redirect'),
      '#description' => $this->t('Redirect target path after logout, defaults to <code>@default</code>.', [
        '@default' => '<front>',
      ]),
      '#config_target' => 'csn_custom.settings:logout_redirect',
      '#convert_path' => PathElement::CONVERT_NONE,
      '#element_validate' => [
        '::validatePath',
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Validate handler to ensure path is compatible with Url::fromUserInput.
   */
  public function validatePath(array &$element, FormStateInterface $form_state): void {
    $value = $form_state->getValue($element['#parents']);
    if ($value[0] !== '/') {
      $form_state->setError($element, $this->t("The path '%path' has to start with a slash.", [
        '%path' => $value,
      ]));
    }
  }

}
