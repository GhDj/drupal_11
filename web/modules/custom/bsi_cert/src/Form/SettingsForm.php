<?php

namespace Drupal\bsi_cert\Form;

use Drupal\bsi_cert\Service\BsiCertFeedClient;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configuration form for the CERT teaser lists.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * Source types managed by this form.
   */
  const array SOURCE_KEYS = [
    BsiCertFeedClient::TYPE_CITIZEN,
    BsiCertFeedClient::TYPE_FEDERAL,
  ];

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    protected readonly BsiCertFeedClient $feedClient,
    protected readonly StateInterface $state,
    protected readonly DateFormatterInterface $dateFormatter,
  ) {
    parent::__construct($config_factory, $typed_config_manager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('bsi_cert.feed_client'),
      $container->get('state'),
      $container->get('date.formatter'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['bsi_cert.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'bsi_cert_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('bsi_cert.settings');

    $form['intro'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('The Computer Emergency Response Team (short: CERT) provides security announcements that are published on this website. The feeds are fetched by cron and stored; the website only ever displays the stored announcements.'),
    ];

    foreach (self::SOURCE_KEYS as $key) {
      $config_prefix = 'sources.' . $key;
      $form['sources'][$key] = [
        '#type' => 'details',
        '#tree' => TRUE,
        '#title' => $this->t('Configure @section', [
          '@section' => $key,
        ]),
        '#open' => $key === BsiCertFeedClient::TYPE_CITIZEN,
      ];
      $form['sources'][$key]['label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Label'),
        '#config_target' => 'bsi_cert.settings:' . $config_prefix . '.label',
      ];

      $form['sources'][$key]['source_format'] = [
        '#type' => 'select',
        '#title' => $this->t('Format'),
        '#options' => [
          'rss' => $this->t('RSS/XML'),
          'json' => $this->t('JSON'),
        ],
        '#config_target' => 'bsi_cert.settings:' . $config_prefix . '.source_format',
      ];
      $form['sources'][$key]['source_url'] = [
        '#type' => 'url',
        '#title' => $this->t('URL'),
        '#description' => $this->t('URL of the RSS feed or API endpoint.'),
        '#config_target' => 'bsi_cert.settings:' . $config_prefix . '.source_url',
      ];

      $form['sources'][$key]['teaser_count'] = [
        '#type' => 'number',
        '#title' => $this->t('Number of results'),
        '#min' => 1,
        '#max' => 30,
        '#default_value' => $config->get($config_prefix . '.teaser_count'),
      ];

      // Show when this source was last fetched, for operator confidence.
      if ($last = $this->feedClient->getLastUpdated($key)) {
        $form['sources'][$key]['last_updated'] = [
          '#type' => 'item',
          '#title' => $this->t('Last fetched'),
          '#markup' => $this->dateFormatter->format($last, 'medium'),
        ];
      }

      $form['sources'][$key]['cta'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('More link'),
        '#tree' => TRUE,
      ];
      $form['sources'][$key]['cta']['title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Link title'),
        '#config_target' => 'bsi_cert.settings:sources.' . $key . '.cta_text',
      ];
      $form['sources'][$key]['cta']['url'] = [
        '#type' => 'url',
        '#title' => $this->t('Link URL'),
        '#config_target' => 'bsi_cert.settings:sources.' . $key . '.cta_url',
      ];
    }

    $form['cron'] = [
      '#type' => 'details',
      '#title' => $this->t('Fetch settings'),
      '#open' => TRUE,
    ];
    $form['cron']['cron_interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Fetch interval'),
      '#description' => $this->t('Minimum time between two feed fetches. Cron itself must be triggered at least this often (on the OKD deployment via a CronJob running <code>drush cron</code>).'),
      '#field_suffix' => $this->t('Seconds'),
      '#min' => 60,
      '#config_target' => 'bsi_cert.settings:cron_interval',
    ];
    $form['cron']['request_timeout'] = [
      '#type' => 'number',
      '#title' => $this->t('Query timeout'),
      '#field_suffix' => $this->t('Seconds'),
      '#min' => 2,
      '#max' => 30,
      '#config_target' => 'bsi_cert.settings:request_timeout',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    parent::submitForm($form, $form_state);

    // Config may have changed (URL, format, timeout). Fetch immediately so the
    // admin sees the effect at once, and reset the throttle so the next cron
    // run is not skipped. refresh() invalidates the block cache tag on change.
    foreach (self::SOURCE_KEYS as $source) {
      $this->feedClient->refresh($source);
    }
    $this->state->set('bsi_cert.next_run', 0);
  }

}
