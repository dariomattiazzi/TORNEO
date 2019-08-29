<?php
namespace torneo\V1\Rest\Posiciones;

class PosicionesResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Posiciones\PosicionesMapper');
      return new PosicionesResource($mapper);
    }
}
