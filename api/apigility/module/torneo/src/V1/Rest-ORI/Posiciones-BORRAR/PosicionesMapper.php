<?php
namespace torneo\V1\Rest\posiciones;

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
use torneo\V1\Rest\Zona\ZonaMapper;
use torneo\V1\Rest\armoTabla;

class PosicionesMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function fetchAll()
  {
    $torneo_id    = $_GET["torneo_id"];
    $categoria_id = $_GET["categoria_id"];
    $zona_id      = $_GET["zona_id"];

    $class = new armoTabla($this->adapter);
    $arr = $class->getPosiciones($torneo_id, $categoria_id, $zona_id);



    if (!empty($arr)) {
      $json = new stdClass();
      $json->success = true;
      $json->posiciones = $arr;
      return $json;
    }else{
      $json = new stdClass();
      $json->success = false;
      $json->msg = "No hay registros.";
      return $json;
    }
  }

  function buscoEquipo($equipo_id)
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('equipo');
    $select->where(array('equipo_id' => $equipo_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $arr_equipo = $results->toArray();
    $equipo =  $arr_equipo ['0']['equipo_nombre'];
    return $equipo;
  }

  public function create($data)
  {
  }
}
