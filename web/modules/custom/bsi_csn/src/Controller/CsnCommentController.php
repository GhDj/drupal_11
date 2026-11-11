<?php

declare(strict_types=1);

namespace Drupal\bsi_csn\Controller;

use Drupal\bsi_csn\Service\CsnCommentManager;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for CSN comment API endpoints.
 */
final class CsnCommentController extends ControllerBase {

  public function __construct(
    private readonly CsnCommentManager $commentManager,
    protected $entityTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('bsi_csn.comment_manager'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Returns comments for a Vorfallsbericht node.
   */
  public function list(NodeInterface $node): JsonResponse {
    if ($node->bundle() !== 'vorfallsbericht') {
      return new JsonResponse(['error' => 'Invalid content type.'], Response::HTTP_BAD_REQUEST);
    }

    $authorRole = $this->commentManager->determineAuthorRole($node, $this->currentUser());
    if ($authorRole === NULL) {
      return new JsonResponse(['error' => 'Access denied.'], Response::HTTP_FORBIDDEN);
    }

    $comments = $this->commentManager->getComments($node);

    // Enrich comments with author display name.
    $userStorage = $this->entityTypeManager()->getStorage('user');
    foreach ($comments as &$comment) {
      $comment['id'] = (int) $comment['id'];
      $comment['nid'] = (int) $comment['nid'];
      $comment['uid'] = (int) $comment['uid'];
      $comment['created'] = (int) $comment['created'];
      $author = $userStorage->load($comment['uid']);
      $comment['author_name'] = $author?->getDisplayName() ?? '';
    }

    return new JsonResponse($comments);
  }

  /**
   * Creates a comment on a Vorfallsbericht node.
   */
  public function add(NodeInterface $node, Request $request): JsonResponse {
    if ($node->bundle() !== 'vorfallsbericht') {
      return new JsonResponse(['error' => 'Invalid content type.'], Response::HTTP_BAD_REQUEST);
    }

    $authorRole = $this->commentManager->determineAuthorRole($node, $this->currentUser());
    if ($authorRole === NULL) {
      return new JsonResponse(['error' => 'Access denied.'], Response::HTTP_FORBIDDEN);
    }

    $content = json_decode($request->getContent(), TRUE);
    $body = $content['body'] ?? '';

    if (!is_string($body) || trim($body) === '') {
      return new JsonResponse(['error' => 'Comment body is required.'], Response::HTTP_BAD_REQUEST);
    }

    if (mb_strlen($body) > 1000) {
      return new JsonResponse(['error' => 'Comment body must not exceed 1000 characters.'], Response::HTTP_BAD_REQUEST);
    }

    try {
      $comment = $this->commentManager->createComment($node, $this->currentUser(), $authorRole, $body);
    }
    catch (\InvalidArgumentException $e) {
      return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
    }

    $author = $this->entityTypeManager()->getStorage('user')->load($comment['uid']);
    $comment['author_name'] = $author?->getDisplayName() ?? '';

    return new JsonResponse($comment, Response::HTTP_CREATED);
  }

}
