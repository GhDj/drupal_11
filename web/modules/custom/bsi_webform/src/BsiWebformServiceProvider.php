<?php

declare(strict_types=1);

namespace Drupal\bsi_webform;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Alters services provided by the bsi_webform module.
 */
final class BsiWebformServiceProvider extends ServiceProviderBase {

  /**
   * Replaces the service with our version.
   */
  public function alter(ContainerBuilder $container): void {

    if (!$container->hasDefinition('plugin.manager.webform.element')) {
      return;
    }

    $definition = $container->getDefinition('plugin.manager.webform.element');

    $definition->setClass(
      'Drupal\bsi_webform\Plugin\BsiWebformElementManager'
    );

    $definition->addMethodCall('setCurrentUser', [
      new Reference('current_user'),
    ]);
  }

}
