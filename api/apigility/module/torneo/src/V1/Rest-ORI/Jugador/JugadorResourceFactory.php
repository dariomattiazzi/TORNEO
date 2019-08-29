<?php
namespace torneo\V1\Rest\Jugador;

class JugadorResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Jugador\JugadorMapper');
      return new JugadorResource($mapper);
    }
}
