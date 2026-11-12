<?php

declare(strict_types=1);

namespace Drupal\bsi_csn\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for Vorfallsbericht intro API endpoint.
 */
final class VorfallsberichtIntroController extends ControllerBase {

  /**
   * Returns the intro (headline + text) for a Vorfallsbericht node.
   */
  public function get(NodeInterface $node): JsonResponse {
    if ($node->bundle() !== 'vorfallsbericht') {
      return new JsonResponse(['error' => 'Invalid content type.'], Response::HTTP_BAD_REQUEST);
    }

    if (!$node->hasField('field_paragraphs')) {
      return new JsonResponse(['error' => 'Field not configured.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Find the first text_section paragraph.
    foreach ($node->get('field_paragraphs')->referencedEntities() as $paragraph) {
      if ($paragraph->bundle() === 'text_section') {
        return new JsonResponse([
          'headline' => $paragraph->get('field_section_title')->value ?? '',
          'text' => $paragraph->get('field_section_text')->value ?? '',
          'text_format' => $paragraph->get('field_section_text')->format ?? 'plain_text',
        ]);
      }
    }

    return new JsonResponse([
      'headline' => '',
      'text' => '',
      'text_format' => 'plain_text',
    ]);
  }

}
