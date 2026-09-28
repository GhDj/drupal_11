<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\config_pages\ConfigPagesContextManagerInterface;
use Drupal\config_pages\ConfigPagesInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Language\LanguageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Workarounds for issues with ConfigPages translation handling.
 */
class ConfigPagesHooks {

  public function __construct(
    #[Autowire(service: 'plugin.manager.config_pages_context')]
    protected readonly ConfigPagesContextManagerInterface $contextManager,
  ) {}

  /**
   * Implements hook_ENTITY_TYPE_prepare_form() for config_pages.
   *
   * Used to lazily update existing config_pages entity languages. You can do so
   * by opening the form and simply re-saving the affected entities.
   */
  #[Hook('config_pages_prepare_form')]
  public function prepareForm(ConfigPagesInterface $entity, string $operation, FormStateInterface $form_state) : void {
    $langcode = $this->getLangcodeFromContext($entity->get('context')->value);

    $form_state
      ->set('entity_default_langcode', $langcode)
      ->set('langcode', $langcode);
    $this->alterEntityLanguage($entity, $langcode);

  }

  /**
   * Helper to extract langcode from context.
   *
   * @param string|array|null $context
   *   The (possibly serialized) context array.
   */
  protected function getLangcodeFromContext(mixed $context = NULL): ?string {
    $langcode = NULL;
    if (is_string($context)) {
      $context = unserialize($context, ['allowed_classes' => FALSE]);
    }
    if (is_array($context)) {
      $langcode = array_filter($context, static fn ($context)
        => array_keys($context) == ['language'])[0]['language'] ?? NULL;
    }
    if ($context === NULL) {
      $language_plugin = $this->contextManager->createInstance('language');
      $langcode = $language_plugin->getValue();
    }
    return $langcode;
  }

  /**
   * Rewrite entity and field language codes.
   *
   * This is necessary because many field formatters rely on the entity or field
   * langcode. Paragraphs would be created as 'und' without, causing problems
   * with for example the linkit or entity embed text filters.
   */
  protected function alterEntityLanguage(FieldableEntityInterface $entity, ?string $langcode): void {
    if ($langcode === NULL || $entity->language()->getId() !== LanguageInterface::LANGCODE_NOT_SPECIFIED) {
      // Language unclear or intentionally specified.
      return;
    }

    (new \ReflectionObject($entity))
      ->getProperty('defaultLangcode')
      ->setValue($entity, $langcode);
  }

}
