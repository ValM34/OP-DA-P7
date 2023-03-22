<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use JMS\Serializer\SerializerInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
  public function __construct(private SerializerInterface $serializer)
  {}

  public static function getSubscribedEvents(): array
  {
    // return the subscribed events, their methods and priorities
    return [
      KernelEvents::EXCEPTION => [
        ['uniqueConstraintViolationException', 0],
        ['onNotFoundException', -10],
      ],
    ];
  }

  public function onNotFoundException(ExceptionEvent $event): void
  {
    $exception = $event->getThrowable();
    if ($exception instanceof NotFoundHttpException) {
      $response = new JsonResponse([], Response::HTTP_NOT_FOUND);
      $event->setResponse($response);
    }
  }

  public function uniqueConstraintViolationException(ExceptionEvent $event): void
  {
    $exception = $event->getThrowable();
    if ($exception instanceof UniqueConstraintViolationException) {
      $jsonErrorMessage = $this->serializer->serialize(['message' => 'L\'utilisateur existe déjà'], 'json');

      $response = new JsonResponse($jsonErrorMessage, Response::HTTP_FORBIDDEN, [], true);
      $event->setResponse($response);
    }
  }
}
