<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\csn_custom\FeedbackServiceInterface;
use Drupal\node\Entity\NodeType;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a node:incident_report form using its own form mode.
 *
 * @todo Base on \Drupal\node\Form\NodeForm?
 */
final class FirstResponderContentForm extends ContentEntityForm {

  public function __construct(
    EntityRepositoryInterface $entity_repository,
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    TimeInterface $time,
    private readonly FeedbackServiceInterface $feedback,
    private readonly ImmutableConfig $config,
  ) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('csn_custom.feedback'),
      $container->get('config.factory')->get('csn_custom.settings'),
    );
  }

  /**
   * Page title callback for csn_custom.incident_report.add route.
   */
  public function nodeTitle(?NodeType $node_type = NULL, ?NodeInterface $node = NULL): MarkupInterface|string {
    return match (TRUE) {
      $node !== NULL => $node->getBundleEntity()->label(),
      $node_type !== NULL => $this->t('Create @name', ['@name' => $node_type->label()]),
      default => $this->t('Content'),
    };
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    if (!$form_state->has('entity_form_initialized')) {
      $this->init($form_state);
      // Do not expose revision log message.
      $this->getFormDisplay($form_state)
        ->removeComponent('revision_log');
    }

    $form = parent::buildForm($form, $form_state);
    if (!isset($form['advanced'])) {
      // This gets added by default and renders weirdly if empty.
      $form['advanced']['#access'] = FALSE;
    }

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  protected function showRevisionUi(): bool {
    return FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $label_key = $this->entity->getEntityType()->getKey('label');
    if ($this->entity->isNew() && $this->entity->get($label_key)->isEmpty()) {
      // Ensure label is set, or we can't save.
      $this->entity->{$label_key} = $this->entity->getBundleEntity()->label();
    }
    $result = parent::save($form, $form_state);

    $is_report = $this->entity->getEntityTypeId() === 'node'
      && $this->entity->bundle() === 'incident_report';
    if ($result === SAVED_NEW && $is_report) {
      // @todo Consider triggering events for this, if it gets too much.
      $content = [];
      if ($path = $this->config->get('dashboard')) {
        $content['button'] = [
          '#type' => 'link',
          '#url' => Url::fromUserInput($path),
          '#title' => $this->t('Go to dashboard'),
        ];
      }
      $form_state->setRedirectUrl($this->feedback->getFeedbackUrl(
        $this->t('Thank you for your help!'),
        [
          $this->t('We have received your incident report.'),
          $this->t('You can find information about the status of the incident report on your dashboard.'),
        ],
        $content,
      ));
    }

    return $result;
  }

}
