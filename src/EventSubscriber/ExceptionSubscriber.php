<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
  public static function getSubscribedEvents()
  {
    // return the subscribed events, their methods and priorities
    return [
      KernelEvents::EXCEPTION => [
        ['onNotFoundException', -10],
      ],
    ];
  }

  public function onNotFoundException(ExceptionEvent $event)
  {
    $exception = $event->getThrowable();
    if ($exception instanceof NotFoundHttpException) {
      $response = new JsonResponse([], 404);
      $event->setResponse($response);
    }
  }
}
