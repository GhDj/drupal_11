<?php

declare(strict_types=1);

namespace Drupal\bsi_csn\Hook;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;

/**
 * Form alter hooks for the CSN Profile form.
 */
class ProfileFormHooks {

  use StringTranslationTrait;

  /**
   * Fields that become read-only after initial profile approval.
   */
  private const LOCK_AFTER_APPROVAL = [
    'field_public_title',
    'field_public_first_name',
    'field_public_last_name',
    'field_public_email',
    'field_legal_consent',
  ];

  public function __construct(
    protected readonly AccountProxyInterface $currentUser,
    protected readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Implements hook_form_node_form_alter() for csn_profile.
   */
  #[Hook('form_node_form_alter')]
  public function alterProfileForm(array &$form, FormStateInterface $form_state, string $form_id): void {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $form_state->getFormObject()->getEntity();
    if ($node->bundle() !== 'csn_profile') {
      return;
    }

    $is_approved = $this->isProfileApproved($node);

    // Hide admin-only fields from Ersthelfer.
    $form['field_profile_approved']['#access'] = $this->currentUser->hasPermission('administer nodes');

    // Hide the node title — we auto-generate it from name fields.
    $form['title']['#access'] = FALSE;

    // Hide standard node form elements.
    $form['status']['#access'] = FALSE;

    // Add info banners.
    $this->addInfoBanners($form, $is_approved);

    // Add login data section (read-only display of user account data).
    $this->addLoginDataSection($form);

    // Add info banner to the public contact section.
    $this->addPublicContactBanner($form);

    // Lock fields after approval.
    if ($is_approved) {
      $this->lockApprovedFields($form);
    }

    // Absence date fields: visible only when absence checkbox is checked.
    $form['field_absence_start']['#states'] = [
      'visible' => [
        ':input[name="field_absence[value]"]' => ['checked' => TRUE],
      ],
      'required' => [
        ':input[name="field_absence[value]"]' => ['checked' => TRUE],
      ],
    ];
    $form['field_absence_end']['#states'] = [
      'visible' => [
        ':input[name="field_absence[value]"]' => ['checked' => TRUE],
      ],
      'required' => [
        ':input[name="field_absence[value]"]' => ['checked' => TRUE],
      ],
    ];

    // Add mandatory field hint at the bottom.
    $form['mandatory_hint'] = [
      '#type' => 'markup',
      '#markup' => '<p class="csn-profile-mandatory-hint">' . $this->t('* Pflichtfeld') . '</p>',
      '#weight' => 90,
    ];

    // Customize submit buttons.
    $this->customizeSubmitButtons($form, $form_state, $is_approved);

    // Auto-generate title on submit.
    $form['#entity_builders'][] = [static::class, 'buildProfileTitle'];

    // Add validation.
    $form['#validate'][] = [static::class, 'validateProfileForm'];

    // Attach CSS.
    $form['#attached']['library'][] = 'bsi_csn/profile_form';
  }

  /**
   * Check if the profile has been approved by CSN Admin.
   */
  private function isProfileApproved(NodeInterface $node): bool {
    if ($node->isNew()) {
      return FALSE;
    }
    return (bool) $node->get('field_profile_approved')->value;
  }

  /**
   * Add info banners for initial and approved states.
   */
  private function addInfoBanners(array &$form, bool $is_approved): void {
    if (!$is_approved) {
      $form['initial_banner'] = [
        '#type' => 'markup',
        '#markup' => '<div class="csn-profile-banner csn-profile-banner--info">'
          . '<p>' . $this->t('Bitte vervollständigen Sie Ihre Daten, um Ihr Profil zu finalisieren. Erst nach Vervollständigung und Freigabe können Sie als Ersthelfende:r kontaktiert werden.') . '</p>'
          . '</div>',
        '#weight' => -100,
      ];
    }
  }

  /**
   * Add read-only login data section.
   */
  private function addLoginDataSection(array &$form): void {
    $account = $this->entityTypeManager->getStorage('user')->load($this->currentUser->id());
    if (!$account) {
      return;
    }

    $email = $account->getEmail();
    $display_value = Html::escape($email ?: $account->getAccountName());

    $form['login_data_display'] = [
      '#type' => 'details',
      '#title' => $this->t('Daten zum Login'),
      '#open' => TRUE,
      '#weight' => -50,
      '#attributes' => [
        'class' => ['csn-profile-section', 'csn-profile-section--login'],
      ],
    ];

    $form['login_data_display']['password_hint'] = [
      '#type' => 'markup',
      '#markup' => '<div class="csn-profile-banner csn-profile-banner--hint">'
        . '<p>' . $this->t('Wenn Sie Ihr Passwort ändern möchten, können Sie dies auf der Login-Seite tun.') . '</p>'
        . '</div>',
    ];

    $form['login_data_display']['username_display'] = [
      '#type' => 'item',
      '#title' => $this->t('Benutzername (E-Mail)'),
      '#markup' => '<span class="csn-profile-readonly-value">' . $display_value . '</span>',
    ];

    $form['login_data_display']['password_display'] = [
      '#type' => 'item',
      '#title' => $this->t('Passwort'),
      '#markup' => '<span class="csn-profile-readonly-value">••••••••</span>',
    ];
  }

  /**
   * Add info banner to the public contact section.
   *
   * Injects a styled Hinweisbanner into the group_public_contact fieldset.
   * Field_group creates the wrapper element keyed by group name.
   */
  private function addPublicContactBanner(array &$form): void {
    $form['public_contact_banner'] = [
      '#type' => 'markup',
      '#markup' => '<div class="csn-profile-banner csn-profile-banner--hint">'
        . '<p>' . $this->t('Die folgenden Daten werden auf dem Profil des DEH auf der CSN Webseite veröffentlicht. Sie können Titel, Vorname, Nachname und E-Mail nur bei der initialen Befüllung angeben. Änderungen können Sie beim CSN Team beantragen.') . '</p>'
        . '</div>',
      '#weight' => -10,
      '#group' => 'group_public_contact',
    ];
  }

  /**
   * Lock fields that should not be editable after approval.
   */
  private function lockApprovedFields(array &$form): void {
    foreach (self::LOCK_AFTER_APPROVAL as $field_name) {
      if (isset($form[$field_name])) {
        $form[$field_name]['#disabled'] = TRUE;
        $form[$field_name]['#attributes']['class'][] = 'csn-profile-field--locked';
      }
    }
  }

  /**
   * Customize submit buttons based on profile state.
   */
  private function customizeSubmitButtons(array &$form, FormStateInterface $form_state, bool $is_approved): void {
    if (isset($form['actions']['submit'])) {
      $form['actions']['submit']['#value'] = $this->t('Profil speichern');
    }

    // On initial fill, add "Freigabe anfordern" button.
    if (!$is_approved) {
      $form['actions']['request_approval'] = [
        '#type' => 'submit',
        '#value' => $this->t('Freigabe des Accounts anfordern'),
        '#weight' => 10,
        '#submit' => $form['actions']['submit']['#submit'] ?? ['::submitForm', '::save'],
        '#attributes' => [
          'class' => ['csn-profile-btn--request-approval'],
        ],
      ];
    }

    // Remove preview/delete if present.
    unset($form['actions']['preview'], $form['actions']['delete']);
  }

  /**
   * Entity builder: auto-generate profile title from name fields.
   */
  public static function buildProfileTitle(string $entity_type, NodeInterface $node, array &$form, FormStateInterface $form_state): void {
    $first_name = $form_state->getValue('field_public_first_name')[0]['value'] ?? '';
    $last_name = $form_state->getValue('field_public_last_name')[0]['value'] ?? '';
    $title = trim($first_name . ' ' . $last_name);
    if (empty($title)) {
      $title = 'CSN Profile';
    }
    $node->setTitle($title);
  }

  /**
   * Validate the profile form.
   */
  public static function validateProfileForm(array &$form, FormStateInterface $form_state): void {
    // When absence is checked, require start and end dates.
    $absence = $form_state->getValue('field_absence');
    $absence_checked = !empty($absence['value']);

    if ($absence_checked) {
      $start = $form_state->getValue('field_absence_start');
      $end = $form_state->getValue('field_absence_end');

      $start_value = $start[0]['value'] ?? NULL;
      $end_value = $end[0]['value'] ?? NULL;

      if (empty($start_value)) {
        $form_state->setErrorByName('field_absence_start', t('Bitte geben Sie ein Startdatum für die Abwesenheit an.'));
      }
      if (empty($end_value)) {
        $form_state->setErrorByName('field_absence_end', t('Bitte geben Sie ein Enddatum für die Abwesenheit an.'));
      }
    }
  }

}
