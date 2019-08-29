<?php
namespace torneo\V1\Rest\Cierropartido;

class CierropartidoResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Cierropartido\CierropartidoMapper');
      return new CierropartidoResource($mapper);
    }
}
