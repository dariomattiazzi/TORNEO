<?php
namespace torneo\V1\Rest\Encapilla;

class EncapillaResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Encapilla\EncapillaMapper');
      return new EncapillaResource($mapper);
    }
}
