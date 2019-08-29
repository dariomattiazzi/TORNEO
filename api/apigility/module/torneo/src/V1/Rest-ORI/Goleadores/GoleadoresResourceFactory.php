<?php
namespace torneo\V1\Rest\Goleadores;

class GoleadoresResourceFactory
{
  public function __invoke($services)
  {
    $mapper = $services->get('torneo\V1\Rest\Goleadores\GoleadoresMapper');
    return new GoleadoresResource($mapper);
  }
}
