<?php
namespace torneo\V1\Rest\Fecha;

class FechaResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Fecha\FechaMapper');
      return new FechaResource($mapper);
    }
}
