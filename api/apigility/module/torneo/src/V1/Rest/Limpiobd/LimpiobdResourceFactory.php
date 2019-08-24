<?php
namespace torneo\V1\Rest\Limpiobd;

class LimpiobdResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Limpiobd\LimpiobdMapper');
      return new LimpiobdResource($mapper);
    }
}
