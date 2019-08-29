<?php
namespace torneo\V1\Rest\Equipozona;

class EquipozonaResourceFactory
{
    public function __invoke($services)
    {
      $mapper = $services->get('torneo\V1\Rest\Equipozona\EquipozonaMapper');
      return new EquipozonaResource($mapper);
    }
}
