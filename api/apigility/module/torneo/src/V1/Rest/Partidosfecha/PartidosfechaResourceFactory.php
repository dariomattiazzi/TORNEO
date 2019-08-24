<?php
namespace torneo\V1\Rest\Partidosfecha;

class PartidosfechaResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Partidosfecha\PartidosfechaMapper');
      return new PartidosfechaResource($mapper);
    }
}
