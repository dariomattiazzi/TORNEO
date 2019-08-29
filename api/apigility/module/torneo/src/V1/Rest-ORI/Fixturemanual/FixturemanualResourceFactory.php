<?php
namespace torneo\V1\Rest\Fixturemanual;

class FixturemanualResourceFactory
{
    public function __invoke($services)
    {
    //    return new FixturemanualResource();
      $mapper = $services->get('torneo\V1\Rest\Fixturemanual\FixturemanualMapper');
      return new FixturemanualResource($mapper);
    }
}
