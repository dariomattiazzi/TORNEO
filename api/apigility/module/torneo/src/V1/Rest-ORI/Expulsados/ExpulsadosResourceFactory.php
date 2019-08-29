<?php
namespace torneo\V1\Rest\Expulsados;

class ExpulsadosResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Expulsados\ExpulsadosMapper');
      return new ExpulsadosResource($mapper);
    }
}
