<?php
namespace torneo;

use ZF\Apigility\Provider\ApigilityProviderInterface;

class Module implements ApigilityProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'ZF\Apigility\Autoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src',
                ],
            ],
        ];
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'torneo\V1\Rest\Torneo\TorneoMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Torneo\TorneoMapper($adapter2);
              },
                'torneo\V1\Rest\Categoria\CategoriaMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Categoria\CategoriaMapper($adapter2);
              },
                'torneo\V1\Rest\Zona\ZonaMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Zona\ZonaMapper($adapter2);
              },
                'torneo\V1\Rest\Equipo\EquipoMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Equipo\EquipoMapper($adapter2);
              },
                'torneo\V1\Rest\Equipozona\EquipozonaMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Equipozona\EquipozonaMapper($adapter2);
              },
                'torneo\V1\Rest\Fixture\FixtureMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Fixture\FixtureMapper($adapter2);
                },
                'torneo\V1\Rest\Torneocompleto\TorneocompletoMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Torneocompleto\TorneocompletoMapper($adapter2);
                },
                'torneo\V1\Rest\Reglas\ReglasMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Reglas\ReglasMapper($adapter2);
                },
                'torneo\V1\Rest\Jugador\JugadorMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Jugador\JugadorMapper($adapter2);
                },
                'torneo\V1\Rest\Fecha\FechaMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Fecha\FechaMapper($adapter2);
                },
                'torneo\V1\Rest\Partidosfecha\PartidosfechaMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Partidosfecha\PartidosfechaMapper($adapter2);
                },
                'torneo\V1\Rest\Goleadores\GoleadoresMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Goleadores\GoleadoresMapper($adapter2);
                },
                'torneo\V1\Rest\Amonestados\AmonestadosMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Amonestados\AmonestadosMapper($adapter2);
                },
                'torneo\V1\Rest\Expulsados\ExpulsadosMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Expulsados\ExpulsadosMapper($adapter2);
                },
                'torneo\V1\Rest\Cargarturnos\CargarturnosMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Cargarturnos\CargarturnosMapper($adapter2);
                },
                'torneo\V1\Rest\Posiciones\PosicionesMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Posiciones\PosicionesMapper($adapter2);
                },
                'torneo\V1\Rest\Turno\TurnoMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Turno\TurnoMapper($adapter2);
                },
                'torneo\V1\Rest\Cancha\CanchaMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Cancha\CanchaMapper($adapter2);
                },
                'torneo\V1\Rest\JugadoresEquipo\JugadoresEquipoMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\JugadoresEquipo\JugadoresEquipoMapper($adapter2);
                },
                'torneo\V1\Rest\Posicionesgeneral\PosicionesgeneralMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Posicionesgeneral\PosicionesgeneralMapper($adapter2);
                },
                'torneo\V1\Rest\Cierrafase\CierrafaseMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Cierrafase\CierrafaseMapper($adapter2);
              },
                'torneo\V1\Rest\Sancionados\SancionadosMapper' => function ($sm2) {
                $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                return new \torneo\V1\Rest\Sancionados\SancionadosMapper($adapter2);
                },
                  'torneo\V1\Rest\Cierropartido\CierropartidoMapper' => function ($sm2) {
                  $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                  return new \torneo\V1\Rest\Cierropartido\CierropartidoMapper($adapter2);
                  },
                  'torneo\V1\Rest\Abropartido\AbropartidoMapper' => function ($sm2) {
                  $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                  return new \torneo\V1\Rest\Abropartido\AbropartidoMapper($adapter2);
                  },
                  'torneo\V1\Rest\Sancionadosvuelven\SancionadosvuelvenMapper' => function ($sm2) {
                  $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                  return new \torneo\V1\Rest\Sancionadosvuelven\SancionadosvuelvenMapper($adapter2);
                  },
                  'torneo\V1\Rest\Vallamenosvencida\VallamenosvencidaMapper' => function ($sm2) {
                  $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                  return new \torneo\V1\Rest\Vallamenosvencida\VallamenosvencidaMapper($adapter2);
                  },
                  'torneo\V1\Rest\Encapilla\EncapillaMapper' => function ($sm2) {
                  $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                  return new \torneo\V1\Rest\Encapilla\EncapillaMapper($adapter2);
                  },
                  'torneo\V1\Rest\Reporte\ReporteMapper' => function ($sm2) {
                  $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                  return new \torneo\V1\Rest\Reporte\ReporteMapper($adapter2);
                  },
                  'torneo\V1\Rest\Goleadoresporcategoria\GoleadoresporcategoriaMapper' => function ($sm2) {
                  $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                  return new \torneo\V1\Rest\Goleadoresporcategoria\GoleadoresporcategoriaMapper($adapter2);
                  },
                  'torneo\V1\Rest\Cargarlistas\CargarlistasMapper' => function ($sm2) {
                  $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                  return new \torneo\V1\Rest\Cargarlistas\CargarlistasMapper($adapter2);
                  },
                  'torneo\V1\Rest\Nosepresenta\NosepresentaMapper' => function ($sm2) {
                  $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                  return new \torneo\V1\Rest\Nosepresenta\NosepresentaMapper($adapter2);
                  },
                  'torneo\V1\Rest\Quitapuntos\QuitapuntosMapper' => function ($sm2) {
                  $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                  return new \torneo\V1\Rest\Quitapuntos\QuitapuntosMapper($adapter2);
                  },
                  'torneo\V1\Rest\Limpiobd\LimpiobdMapper' => function ($sm2) {
                  $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                  return new \torneo\V1\Rest\Limpiobd\LimpiobdMapper($adapter2);
                },
                  'torneo\V1\Rest\Fixturemanual\FixturemanualMapper' => function ($sm2) {
                  $adapter2 = $sm2->get('Zend\Db\Adapter\Adapter');
                  return new \torneo\V1\Rest\Fixturemanual\FixturemanualMapper($adapter2);
                  }

              )
            );
    }
}
