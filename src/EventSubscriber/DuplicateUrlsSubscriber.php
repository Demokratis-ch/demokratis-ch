<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class DuplicateUrlsSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (str_starts_with($event->getRequest()->getRequestUri(), '/index.php')) {
            $event->setResponse(new RedirectResponse(
                str_replace('/index.php/', '/', $event->getRequest()->getUri()),
                Response::HTTP_MOVED_PERMANENTLY
            ));
        }
    }
}
