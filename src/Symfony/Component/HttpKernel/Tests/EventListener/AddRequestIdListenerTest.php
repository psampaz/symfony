<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\EventListener\AddRequestIdListener;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class AddRequestIdListenerTest extends \PHPUnit_Framework_TestCase
{
    private $requestStack;

    protected function setUp()
    {
        $this->requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack', array(), array(), '', false);
        $this->generator = $this->getMock('Symfony\Component\HttpKernel\RequestId\RequestIdGeneratorInterface', array(), array(), '', false);
    }

    public function testAddsRequestIdWithDefaultHeader()
    {
        $request = Request::create('/');

        $this->generator->method('generate')->willReturn('Kdxh2uvDfRZDaFTumYu97m5F');

        $listener = new AddRequestIdListener($this->requestStack, $this->generator);
        $event = $this->getEvent($request);

        $listener->onKernelRequest($event);
        $this->assertEquals('Kdxh2uvDfRZDaFTumYu97m5F', $request->headers->get('X-Request-Id'));
    }

    public function testAddsRequestIdWithCustomHeader()
    {
        $request = Request::create('/');

        $this->generator->method('generate')->willReturn('VPdtbSQRHQdDyh4qtxVKcf7h');

        $listener = new AddRequestIdListener($this->requestStack, $this->generator, 'X-Request-Id-Custom');
        $event = $this->getEvent($request);

        $listener->onKernelRequest($event);
        $this->assertEquals('VPdtbSQRHQdDyh4qtxVKcf7h', $request->headers->get('X-Request-Id-Custom'));
    }

    public function testRequestIdIsNotOverridden()
    {
        $request = Request::create('/');
        $request->headers->set('X-Request-Id', 'exists');

        $listener = new AddRequestIdListener($this->requestStack, $this->generator);
        $event = $this->getEvent($request);

        $listener->onKernelRequest($event);
        $this->assertEquals('exists', $request->headers->get('X-Request-Id'));
    }

    public function testRequestIdInCustomHeader()
    {
        $listener = new AddRequestIdListener($this->requestStack, $this->generator, 'Custom-Request-Id');
        $event = $this->getEvent($request = Request::create('/'));

        $listener->onKernelRequest($event);
        $this->assertTrue($request->headers->has('Custom-Request-Id'));
    }

    private function getEvent(Request $request)
    {
        return new GetResponseEvent($this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface'), $request, HttpKernelInterface::MASTER_REQUEST);
    }
}
