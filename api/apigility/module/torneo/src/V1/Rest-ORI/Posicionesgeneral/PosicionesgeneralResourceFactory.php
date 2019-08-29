<?php
namespace torneo\V1\Rest\Posicionesgeneral;

class PosicionesgeneralResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Posicionesgeneral\PosicionesgeneralMapper');
      return new PosicionesgeneralResource($mapper);      
    }
}
