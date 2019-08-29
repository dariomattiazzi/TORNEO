<?php
namespace torneo\V1\Rest\Sancionados;

class SancionadosResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Sancionados\SancionadosMapper');
      return new SancionadosResource($mapper);
    }
}
