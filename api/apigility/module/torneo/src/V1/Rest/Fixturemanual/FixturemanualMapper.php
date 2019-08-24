<?php
namespace torneo\V1\Rest\Fixturemanual;

use ZF\OAuth2\Controller\AuthController;
use ZF\OAuth2\Provider\UserId\UserIdProviderInterface;
use OAuth2\Storage\Pdo;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;
use Zend\Crypt\PublicKey\Rsa\PublicKey;
use Zend\Db\Adapter\Driver;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use stdClass;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Http\Response;
use Zend\Http\Response\Stream;

class FixturemanualMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  function buscoEquipo($id_equipo)
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('equipo');
    $select->where(array('equipo_id' => $id_equipo));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $equipo = $results->toArray();

    if (!empty($equipo[0]['equipo_nombre'])) {
      $eq = $equipo[0]['equipo_nombre'];
      return $eq;
    }else {
      $eq = "LIBRE";
      return $eq;
    }
  }


  public function create($data)
  {
    $torneo_id = $data->torneo_id;
    $categoria_id  = $data->categoria_id;
    //$zona_id   = $data->zona_id;

    $fixture = $this->buscoFixture($data);
    if (!empty($fixture)) {
      $this->eliminoFixture($data);
    }

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('zona');
    $select->where(array('zona_categoria_id' => $categoria_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $zonas = $results->toArray();

    //print_r($zonas); die;

    $insert = "INSERT INTO fixture (fixture_torneo_id, fixture_categoria_id, fixture_zona_id, fixture_fecha, fixture_equipo_id1, fixture_equipo_id2, fixture_fase_id, fixture_cancha_id, fixture_turno_id, fixture_goles_eq1, fixture_goles_eq2, fixture_estado, fixture_penales_eq1, fixture_penales_eq2) VALUES " ."\n";

    foreach ($zonas as $key => $row) {
      $fixture_torneo_id = $torneo_id;
      $fixture_categoria_id = $row['zona_categoria_id'];
      $fixture_zona_id      = $row['zona_id'];

      $sql = new Sql($this->adapter);
      $select = $sql->select();
      $select->from('equipo_zona');
      $select->where(array('zona_id' => $fixture_zona_id));
      $select->order(array('id'));
      $selectString = $sql->getSqlStringForSqlObject($select);
      $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
      $equipos = $results->toArray();

      //print_r($equipos); die;

      $i = 0;
      foreach ($equipos as $key => $row) {
        $arr[$i] = $row ['equipo_id'];
        $i++;
      }

      //print_r($arr); die;
      $e1 = $arr['0'];
      $e2 = $arr['1'];
      $e3 = $arr['2'];
      $e4 = $arr['3'];
      $e5 = $arr['4'];

      for ($i=1 ; $i <= 5 ; $i++ ) {
        switch ($i) {
          case 1:
          $insert .= "('$fixture_torneo_id', '$fixture_categoria_id', '$fixture_zona_id', '$i', '$e5', '888', '1', '1', '1', null, null, '1', null, null),"."\n";
          $insert .= "('$fixture_torneo_id', '$fixture_categoria_id', '$fixture_zona_id', '$i', '$e1', '$e4', '1', '1', '1', null, null, '1', null, null),"."\n";
          $insert .= "('$fixture_torneo_id', '$fixture_categoria_id', '$fixture_zona_id', '$i', '$e2', '$e3', '1', '1', '1', null, null, '1', null, null),"."\n";
          break;

          case 2:
          $insert .= "('$fixture_torneo_id', '$fixture_categoria_id', '$fixture_zona_id', '$i', '$e2', '888', '1', '1', '1', null, null, '1', null, null),"."\n";
          $insert .= "('$fixture_torneo_id', '$fixture_categoria_id', '$fixture_zona_id', '$i', '$e3', '$e1', '1', '1', '1', null, null, '1', null, null),"."\n";
          $insert .= "('$fixture_torneo_id', '$fixture_categoria_id', '$fixture_zona_id', '$i', '$e4', '$e5', '1', '1', '1', null, null, '1', null, null),"."\n";
          break;

          case 3:
          $insert .= "('$fixture_torneo_id', '$fixture_categoria_id', '$fixture_zona_id', '$i', '$e4', '888', '1', '1', '1', null, null, '1', null, null),"."\n";
          $insert .= "('$fixture_torneo_id', '$fixture_categoria_id', '$fixture_zona_id', '$i', '$e5', '$e3', '1', '1', '1', null, null, '1', null, null),"."\n";
          $insert .= "('$fixture_torneo_id', '$fixture_categoria_id', '$fixture_zona_id', '$i', '$e1', '$e2', '1', '1', '1', null, null, '1', null, null),"."\n";
          break;

          case 4:
          $insert .= "('$fixture_torneo_id', '$fixture_categoria_id', '$fixture_zona_id', '$i', '$e1', '888', '1', '1', '1', null, null, '1', null, null),"."\n";
          $insert .= "('$fixture_torneo_id', '$fixture_categoria_id', '$fixture_zona_id', '$i', '$e2', '$e5', '1', '1', '1', null, null, '1', null, null),"."\n";
          $insert .= "('$fixture_torneo_id', '$fixture_categoria_id', '$fixture_zona_id', '$i', '$e3', '$e4', '1', '1', '1', null, null, '1', null, null),"."\n";
          break;

          case 5:
          $insert .= "('$fixture_torneo_id', '$fixture_categoria_id', '$fixture_zona_id', '$i', '$e3', '888', '1', '1', '1', null, null, '1', null, null),"."\n";
          $insert .= "('$fixture_torneo_id', '$fixture_categoria_id', '$fixture_zona_id', '$i', '$e4', '$e2', '1', '1', '1', null, null, '1', null, null),"."\n";
          $insert .= "('$fixture_torneo_id', '$fixture_categoria_id', '$fixture_zona_id', '$i', '$e5', '$e1', '1', '1', '1', null, null, '1', null, null),"."\n";
          break;
        }
      }
    }
    echo $insert; die;
  }

  function buscoFixture($data)
  {
    $torneo_id    = $data->torneo_id;
    $categoria_id = $data->categoria_id;

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('fixture');
    $select->where(array('fixture_torneo_id' => $torneo_id, 'fixture_categoria_id' => $categoria_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $fixture = $results->toArray();
    return $fixture;
  }

  public function eliminoFixture($data)
  {
    $torneo_id    = $data->torneo_id;
    $categoria_id = $data->categoria_id;
    $zona_id      = $data->zona_id;
    try {
      $sql = new Sql($this->adapter);
      $delete = $sql->delete();
      $delete->from('fixture');
      $delete->where(array('fixture_torneo_id' => $torneo_id, 'fixture_categoria_id' => $categoria_id));
      $deleteString = $sql->getSqlStringForSqlObject($delete);
      $results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
    } catch (Exception $e) {
      $json = new stdClass();
      $json->success = false;
      $json->msg = "No se pudo eliminar el fixture.";
      return $json;
    }
  }
}
