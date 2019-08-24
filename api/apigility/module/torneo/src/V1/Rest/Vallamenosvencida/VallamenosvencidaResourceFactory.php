<?php
namespace torneo\V1\Rest\Vallamenosvencida;

class VallamenosvencidaResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Vallamenosvencida\VallamenosvencidaMapper');
      return new VallamenosvencidaResource($mapper);
    }
}
