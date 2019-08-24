<?php
namespace torneo\V1\Rest\Limpiobd;

class LimpiobdResourceFactory
{
    public function __invoke($services)
    {
        return new LimpiobdResource();
    }
}
