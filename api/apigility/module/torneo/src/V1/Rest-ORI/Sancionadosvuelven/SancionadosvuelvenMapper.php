<?php
namespace torneo\V1\Rest\sancionadosvuelven;

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

class sancionadosvuelvenMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function fetchAll()
  {
    $torneo_id = $_GET["torneo_id"];
    $categoria_id  = $_GET["categoria_id"];

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('sancionados');
    $select->where->equalTo("sancionados_torneo_id", $torneo_id);
    $select->where->equalTo("sancionados_categoria_id", $categoria_id);
    $select->where->equalTo("sancionados_vuelve", "1");
    $select->order(array('sancionados_equipo_id'));
    $selectString = $sql->getSqlStringForSqlObject($select);
    // echo $selectString; die;
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $jug_vuelven = $results->toArray();

    //print_r($jug_vuelven); die;

    $arr = array();

    if (!empty($jug_vuelven)) {
      foreach ($jug_vuelven as $key => $row) {
        $jugador = $this->buscoJugador($row ['sancionados_jugador_id']);
        $equipo  = $this->buscoEquipo($row ['sancionados_equipo_id']);
        // print_r($jugador); die;
        // print_r($equipo); die;
        $arr [] = array(
          'text'             => $jugador['jugador_nombre'] ." " .$jugador['jugador_apellido'],
          'jugador_id'       => $jugador['jugador_id'],
          'jugador_nombre'   => $jugador['jugador_nombre'],
          'jugador_apellido' => $jugador['jugador_apellido'],
          "equipo"           => $equipo,
        );
      }
      //print_r($arr); die;
      if (!empty($arr)) {
        $json = new stdClass();
        $json->success = true;
        $json->sancionadosvuelven = $arr;
        return $json;
      }else {
        $json = new stdClass();
        $json->success = true;
        $json->sancionadosvuelven = [];
        $json->msg = "No hay registros para mostrar.";
        return $json;
      }
    }
    else {
      $json = new stdClass();
      $json->success = false;
      $json->sancionadosvuelven = [];
      $json->msg = "No hay registros para mostrar.";
      return $json;
    }
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
      $eq = "Sin Equipo";
      return $eq;
    }
  }

  function buscoJugador($id)
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('jugador');
    $select->where(array('jugador_id' => $id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $jugador = $results->toArray();
    //print_r($jugador[0]); die;
    $j = $jugador['0'];
    return $j;

  }

  public function create($data)
  {
  }

  public function crea($data)
  {

  }

  public function actualiza($data)
  {

  }

  public function delete($id)
  {

  }
}
