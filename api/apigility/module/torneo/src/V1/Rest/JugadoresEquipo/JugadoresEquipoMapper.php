<?php
namespace torneo\V1\Rest\JugadoresEquipo;

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

class JugadoresEquipoMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function fetchAll()
  {
  }

  public function fetchOne()
  {
    $id = $_GET["equipo_id"];

    if(!empty($id)){
      $sql = new Sql($this->adapter);
      $select = $sql->select();
      $select->from('jugador');
      $select->where(array('jugador_equipo_id' => $id));
      $selectString = $sql->getSqlStringForSqlObject($select);
      $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
      $jugadores = $results->toArray();

      foreach ($jugadores as $key => $row) {
        $arr_jugador [] = array(
          'text'              => $row ['jugador_nombre'] . ' '.$row ['jugador_apellido'],
          'jugador_id'        => $row ['jugador_id'],
          'jugador_nombre'    => $row ['jugador_nombre'],
          'jugador_apellido'  => $row ['jugador_apellido'],
          'jugador_equipo_id' => $row ['jugador_equipo_id'],
        );
      }

      //print_r($arr_jugador); die;

      foreach ($arr_jugador as $key => $row) {
        $aux_prom[$key] = $row['text'];
      }
      array_multisort(@$aux_prom, SORT_ASC, $arr_jugador);

      if (!empty($arr_jugador)) {
        $arr_jugador [] = array(
          'text'              => "EN CONTRA",
          'jugador_id'        => 0,
          'jugador_nombre'    => '',
          'jugador_apellido'  => '',
          'jugador_equipo_id' => '',
        );



        $json = new stdClass();
        $json->success   = true;
        $json->jugadores = $arr_jugador;
        return $json;
      }else {
        $json = new stdClass();
        $json->success = true;
	       $json->jugadores = [];
        return $json;
      }
    }else{
      $json = new stdClass();
      $json->success = false;
      $json->jugadores = [];
      $json->msg = "Error, el equipo no puede ser vacio.";
      return $json;
    }
  }

  public function create($data)
  {
    // print_r($data); die;
    $id_jugador = $data->jugador_id;
    $id_equipo  = $data->equipo_id;

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('jugador');
    $select->where(array('jugador_id' => $id_jugador));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $r = $results->toArray();

    $jugador = $r['0'];
    //print_r($jugador); die;
    if(!empty($jugador['jugador_equipo_id'])){
      $equipo  = $this->buscoEquipo($jugador['jugador_equipo_id']);
      $json = new stdClass();
      $json->success = true;
      $json->msg = "Error, el jugador ya está inscripto en el equipo: ".$equipo;
      return $json;
    }else {
      $sql = new Sql($this->adapter);
      $update = $sql->update();
      $update->table('jugador');
      $update->set(array("jugador_equipo_id" => $id_equipo));
      $update->where->equalTo("jugador_id", $id_jugador);
      $updateString = $sql->getSqlStringForSqlObject($update);
      $this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);

      $json = new stdClass();
      $json->success   = true;
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

  public function delete($id)
  {
    $sql = new Sql($this->adapter);
    $update = $sql->update();
    $update->table('jugador');
    $update->set(array("jugador_equipo_id" => null));
    $update->where->equalTo("jugador_id", $id);
    $updateString = $sql->getSqlStringForSqlObject($update);
    $this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);

    $oResponse = new Response();
    $response = new stdClass;
    $response->success = true;
    $response->mensaje = "El jugador fue eliminado del Equipo .";
    $oResponse->setContent(json_encode($response));
    return $oResponse;

    $json = new stdClass();
    $json->success   = true;
    return $json;
  }
}
