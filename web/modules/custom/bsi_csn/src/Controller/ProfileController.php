<?php

declare(strict_types=1);

namespace Drupal\bsi_csn\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller for the CSN Profile page.
 *
 * Redirects the current user to their csn_profile node edit form.
 * If no profile node exists yet, redirects to the node add form.
 */
final class ProfileController extends ControllerBase {

  /**
   * Redirect to the user's profile node edit form.
   */
  public function profilePage(): RedirectResponse {
    $uid = $this->currentUser()->id();

    $nids = $this->entityTypeManager()->getStorage('node')->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'csn_profile')
      ->condition('uid', $uid)
      ->range(0, 1)
      ->execute();

    if (!empty($nids)) {
      $nid = reset($nids);
      $url = Url::fromRoute('entity.node.edit_form', ['node' => $nid]);
    }
    else {
      $url = Url::fromRoute('node.add', ['node_type' => 'csn_profile']);
    }

    return new RedirectResponse($url->toString());
  }

  /**
   * Page title callback.
   */
  public function profileTitle(): string {
    return (string) $this->t('Mein Profil');
  }

}
