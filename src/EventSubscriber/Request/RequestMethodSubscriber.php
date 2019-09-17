<?php

namespace Mooon\Rest\EventSubscriber\Request;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class RequestMethodSubscriber
 */
class RequestMethodSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents():array
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 9999],
            ],
            KernelEvents::RESPONSE => [
                ['onKernelResponse', 9999],
            ],
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event):void
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();
        $method  = $request->getRealMethod();

        if ('OPTIONS' == $method) {
            $response = new Response();
            $event->setResponse($response);
        }
    }

    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event):void
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $response = $event->getResponse();
        //TODO: add configuration for this
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE,OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type,X-Api-Authorization');
    }
}