<?php
namespace torneo\V1\Rest\Cancha;

class CanchaResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Cancha\CanchaMapper');
      return new CanchaResource($mapper);
    }
}
