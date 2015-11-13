<?php
namespace Symfony\Component\HttpKernel\RequestId;

class RequestIdGenerator implements RequestIdGeneratorInterface
{
    public function generate()
    {
        return hash('sha256', uniqid(mt_rand(), true));
    }

}
