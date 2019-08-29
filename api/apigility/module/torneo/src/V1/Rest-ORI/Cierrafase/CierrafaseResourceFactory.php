<?php
namespace torneo\V1\Rest\Cierrafase;

class CierrafaseResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Cierrafase\CierrafaseMapper');
      return new CierrafaseResource($mapper);
    }
}
