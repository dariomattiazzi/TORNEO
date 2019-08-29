<?php
namespace torneo\V1\Rest\Torneocompleto;

class TorneocompletoResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Torneocompleto\TorneocompletoMapper');
      return new TorneocompletoResource($mapper);
    }
}
