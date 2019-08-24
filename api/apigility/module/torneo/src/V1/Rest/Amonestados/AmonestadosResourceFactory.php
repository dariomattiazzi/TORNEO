<?php
namespace torneo\V1\Rest\Amonestados;

class AmonestadosResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Amonestados\AmonestadosMapper');
      return new AmonestadosResource($mapper);
    }
}
