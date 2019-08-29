<?php
namespace torneo\V1\Rest\Abropartido;

class AbropartidoResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Abropartido\AbropartidoMapper');
      return new AbropartidoResource($mapper);
    }
}
