<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\RequestId\RequestIdGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Adds unique id to each request.
 */
class AddRequestIdListener implements EventSubscriberInterface
{
    private $requestStack;
    private $generator;
    private $header;

    public function __construct(RequestStack $requestStack = null, RequestIdGeneratorInterface $generator, $header = 'X-Request-Id')
    {
        $this->requestStack = $requestStack;
        $this->generator = $generator;
        $this->header = $header;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->headers->has($this->header)) {
            $request->headers->set($this->header, $this->generator->generate());
        }

    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 2048)),
        );
    }
}
