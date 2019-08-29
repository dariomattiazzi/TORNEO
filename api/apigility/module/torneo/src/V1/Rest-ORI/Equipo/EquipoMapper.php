<?php
namespace torneo\V1\Rest\equipo;

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

class equipoMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function fetchAll()
  {
    $j = $_GET["jug"];

    // print_r($cat); die();

    if($j == "si"){
      //echo $j; die;

      $sql = new Sql($this->adapter);
      $select = $sql->select();
      $select->from('equipo');
      $selectString = $sql->getSqlStringForSqlObject($select);
      $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
      $equipos = $results->toArray();

      foreach ($equipos as $key => $row) {
        $id_equipo = $row ['equipo_id'];
        $jugadores = array();
        $jugadores = $this->buscoJugadores($id_equipo);
        //print_r($jugadores); die;
        $arr_equipos [] = array(
          'text'            => $row ['equipo_nombre'],
          'equipo_id'       => $row ['equipo_id'],
          'equipo_nombre'   => $row ['equipo_nombre'],
          'equipo_delegado' => $row ['equipo_delegado'],
          'equipo_estado'   => $row ['equipo_estado'],
          'children'        => $jugadores
        );
      }
    }else {
      $sql = new Sql($this->adapter);
      $select = $sql->select();
      $select->from('equipo');
      $select->where(array('equipo_estado' => 1));
      $selectString = $sql->getSqlStringForSqlObject($select);
      $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
      $equipos = $results->toArray();

      foreach ($equipos as $key => $row) {
        $id_equipo = $row ['equipo_id'];
        $arr_equipos [] = array(
          'text'            => $row ['equipo_nombre'],
          'equipo_id'       => $row ['equipo_id'],
          'equipo_nombre'   => $row ['equipo_nombre'],
          'equipo_delegado' => $row ['equipo_delegado'],
          'equipo_estado'   => $row ['equipo_estado'],
          'leaf'            => true
        );
      }
    }
    // print_r($arr_equipos); die;
    if (!empty($arr_equipos)) {
      $json = new stdClass();
      $json->success = true;
      $json->children = $arr_equipos;
      return $json;

    }else {
      $json = new stdClass();
      $json->success = true;
      $json->children = [];
      $json->msg = "No Existen equipos.";
      return $json;
    }
  }

  function buscoJugadores($id_equipo)
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('jugador');
    $select->where(array('jugador_equipo_id' => $id_equipo));
    $select->order('jugador_apellido');
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $jugadores = $results->toArray();

    // print_r($jugadores); die;

    $arr_jugadores = array();
    foreach ($jugadores as $key => $row) {
      $arr_jugadores [] = array(
        'text'              => $row ['jugador_nombre'] . ' ' .$row ['jugador_apellido'],
        'jugador_id'        => $row ['jugador_id'],
        'jugador_nombre'    => $row ['jugador_nombre'],
        'jugador_apellido'  => $row ['jugador_apellido'],
        'jugador_equipo_id' => $row ['jugador_equipo_id'],
        "leaf"              => true
      );
    }
    return $arr_jugadores;
    //print_r($arr_jugadores); die;
  }

  public function fetchOne($id)
  {
    echo "fetchone"; die;
  }

  public function create($data)
  {
    if ( $data->update == 'true') {
      // echo "ACTUALIZA";
      return $this->actualiza($data);
    }else {
      // echo "CREA";
      return $this->crea($data);
    }
  }

  public function crea($data)
  {
    try {
      $dataInsert = array(
        "equipo_nombre"     => $data->equipo_nombre,
        "equipo_delegado"   => $data->equipo_delegado,
        'equipo_estado'     => $data->equipo_estado,
      );
      $sql = new Sql($this->adapter);
      $insert = $sql->insert();
      $insert->into('equipo');
      $insert->values($dataInsert);
      $insertString = $sql->getSqlStringForSqlObject($insert);
      $results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
      $json = new stdClass();
      $json->success = true;
      return $json;
    } catch (Exception $e) {
      $json = new stdClass();
      $json->success = false;
      $json->msg = "No se pudo ingresar el equipo.";
      return $json;
    }
  }

  public function actualiza($data)
  {
    try {
      $id = $data->equipo_id;
      $sql = new Sql($this->adapter);
      $update = $sql->update();
      $update->table('equipo');
      $update->set(array(
        "equipo_nombre"       => $data->equipo_nombre,
        "equipo_delegado"     => $data->equipo_delegado,
        "equipo_estado"       => $data->equipo_estado,
        "equipo_id"           => $data->equipo_id
      ));
      $update->where->equalTo("equipo_id", $id);
      $updateString = $sql->getSqlStringForSqlObject($update);
      $this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
      $json = new stdClass();
      $json->success = true;
      return $json;
    } catch (Exception $e) {
      $json = new stdClass();
      $json->success = false;
      $json->msg = "No se pudo actualizar el registro.";
      return $json;
    }
  }

  public function delete($id)
  {
    try {
      $sql = new Sql($this->adapter);
      $select = $sql->select();
      $select->from('equipo');
      $select->where('equipo_id = '.$id);
      $selectString = $sql->getSqlStringForSqlObject($select);
      $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
      $categoria = $results->toArray();

      if (!empty($categoria)) {
        $sql = new Sql($this->adapter);
        $delete = $sql->delete();
        $delete->from('equipo');
        $delete->where(array(
          'equipo_id' => $id
        ));
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        //echo $deleteString; die;
        $results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
        $oResponse = new Response();
        $response = new stdClass;
        $response->success = true;
        $response->mensaje = "Equipo eliminado.";
        $oResponse->setContent(json_encode($response));
        return $oResponse;
      }else{
        $oResponse = new Response();
        $response = new stdClass;
        $response->success = false;
        $response->mensaje = "El equipo no puede ser eliminado.";
        $oResponse->setContent(json_encode($response));
        return $oResponse;
      }
    } catch (Exception $e) {
      $json = new stdClass();
      $json->success = false;
      $json->msg = "No se pudo eliminar el equipo.";
      return $json;
    }
  }

}
