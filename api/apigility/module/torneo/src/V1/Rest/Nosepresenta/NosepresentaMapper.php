<?php
namespace torneo\V1\Rest\Nosepresenta;

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

class NosepresentaMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function create($data)
  {
    $fixture_id = $data->fixture_id;
    $equipo_id  = $data->equipo_id;
    $fecha_id   = $data->fecha_id;

    //echo $fixture_id.' - '.$fecha_id; die;

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('fixture');
    $select->where(array('fixture_id' => $fixture_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $partido = $results->toArray();

    //Antes debo saber si recibo el equipo1 o equipo2
    $fixture_equipo1 = $partido['0']['fixture_equipo_id1'];
    $fixture_equipo2 = $partido['0']['fixture_equipo_id2'];

    $torneo_id       = $partido['0']['fixture_torneo_id'];
    $categoria_id    = $partido['0']['fixture_categoria_id'];
    $zona_id         = $partido['0']['fixture_zona_id'];

    //---------------
    if ($fixture_equipo1 == $equipo_id) {
      $id = $data->equipo_id;
      $sql = new Sql($this->adapter);
      $update = $sql->update();
      $update->table('fixture');
      $update->set(array(
        "fixture_goles_eq1"   => 0,
        "fixture_goles_eq2"   => 2
      ));
      $update->where->equalTo("fixture_id", $fixture_id);
      $updateString = $sql->getSqlStringForSqlObject($update);
      $this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
      $json = new stdClass();
      $json->success = true;
      return $json;
    }elseif ($fixture_equipo2 == $equipo_id) {
      $id = $data->equipo_id;
      $sql = new Sql($this->adapter);
      $update = $sql->update();
      $update->table('fixture');
      $update->set(array(
        "fixture_goles_eq1"   => '2',
        "fixture_goles_eq2"   => '0'
      ));
      $update->where->equalTo("fixture_id", $fixture_id);
      $updateString = $sql->getSqlStringForSqlObject($update);
      $this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
      $json = new stdClass();
      $json->success = true;
      return $json;
    }


  }

}
