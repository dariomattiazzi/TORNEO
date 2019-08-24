<?php
namespace torneo\V1\Rest\jugador;

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

class jugadorMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function fetchAll()
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('jugador');
    $select->where('jugador_id > 0');
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $jugadores = $results->toArray();

    foreach ($jugadores as $key => $row) {
      $arr_jugadores [] = array(
        'text'              => $row ['jugador_nombre'] . ' '.$row ['jugador_apellido'],
        'jugador_id'        => $row ['jugador_id'],
        'jugador_nombre'    => $row ['jugador_nombre'],
        'jugador_apellido'  => $row ['jugador_apellido'],
        'jugador_equipo_id' => $row ['jugador_equipo_id'],
        'jugador_dni'       => $row ['jugador_dni'],
        'jugador_fechanac' => $row ['jugador_fechanac'],
        'leaf'              => true
      );
    }

    if (!empty($arr_jugadores)) {
      $json = new stdClass();
      $json->text = "jugadores";
      $json->children = $arr_jugadores;
      return $json;
    }else {
      $json = new stdClass();
      $json->success = true;
      $json->children = [];
      $json->msg = "No Existen jugadores.";
      return $json;
    }
  }

  public function fetchOne($id)
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('jugador');
    $select->where(array('jugador_id' => $id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $jugador = $results->toArray();

    foreach ($jugador as $key => $row) {
      $arr_jugador [] = array(
        'text'              => $row ['jugador_nombre'] . ' '.$row ['jugador_apellido'],
        'jugador_id'        => $row ['jugador_id'],
        'jugador_nombre'    => $row ['jugador_nombre'],
        'jugador_apellido'  => $row ['jugador_apellido'],
        'jugador_equipo_id' => $row ['jugador_equipo_id'],
        'jugador_dni'       => $row ['jugador_dni'],
        'jugador_fechanac' => $row ['jugador_fechanac']
      );
    }

    if (!empty($arr_jugador)) {
      $json = new stdClass();
      $json->success = true;
      $json->torneo = $arr_jugador;
      return $json;
    }else {
      $json = new stdClass();
      $json->success = true;
      $json->torneo = [];
      $json->msg = "No Existe el jugador.";
      return $json;
    }
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
    // controlo que el dni no este vacio
    if (empty($data->jugador_dni)) {
      $json = new stdClass();
      $json->success = false;
      $json->msg = " El numero de dni está vacio.";
      return $json;
    }

    // valido que el dni no esté cargado
    $j = $this->validajugador($data);

    if (!empty($j)) {
      $json = new stdClass();
      $json->success = false;
      $json->msg = " El numero de DNI $data->jugador_dni ya existe.";
      return $json;
    }else{
      try {
        $dataInsert = array(
          "jugador_nombre"    => $data->jugador_nombre,
          "jugador_apellido"  => $data->jugador_apellido,
          "jugador_equipo_id" => $data->jugador_equipo_id,
          "jugador_dni"       => $data->jugador_dni,
          "jugador_fechanac" => $data->jugador_fechanac,
        );
        $sql = new Sql($this->adapter);
        $insert = $sql->insert();
        $insert->into('jugador');
        $insert->values($dataInsert);
        $insertString = $sql->getSqlStringForSqlObject($insert);
        $results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
        $json = new stdClass();
        $json->success = true;
        return $json;
      } catch (Exception $e) {
        $json = new stdClass();
        $json->success = false;
        $json->msg = "No se pudo ingresar el jugador.";
        return $json;
      }
    }
  }

  public function validajugador($data)
  {
        $dni = $data->jugador_dni;

        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('jugador');
        $select->where('jugador_dni = '.$dni);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        $jug_dni = $results->toArray();
        return $jug_dni;
  }

  public function actualiza($data)
  {
    try {
      $id = $data->jugador_id;

      if ($data->jugador_equipo_id == '') {
        $equipo_id = null;
      }else {
          $equipo_id = $data->jugador_equipo_id;
      }

      $sql = new Sql($this->adapter);
      $update = $sql->update();
      $update->table('jugador');
      $update->set(array(
        "jugador_nombre"    => $data->jugador_nombre,
        "jugador_apellido"  => $data->jugador_apellido,
        "jugador_equipo_id" => $equipo_id,
        "jugador_dni"       => $data->jugador_dni,
        "jugador_fechanac"  => $data->jugador_fechanac,
      ));
      $update->where->equalTo("jugador_id", $id);
      $updateString = $sql->getSqlStringForSqlObject($update);
      // echo $updateString; die;
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
      $select->from('jugador');
      $select->where('jugador_id = '.$id);
      $selectString = $sql->getSqlStringForSqlObject($select);
      $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
      $torneo = $results->toArray();

      if (!empty($torneo)) {
        $sql = new Sql($this->adapter);
        $delete = $sql->delete();
        $delete->from('jugador');
        $delete->where(array(
          'jugador_id' => $id
        ));
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        $results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
        $oResponse = new Response();
        $response = new stdClass;
        $response->success = true;
        $response->msg = "Jugador eliminado.";
        $oResponse->setContent(json_encode($response));
        return $oResponse;
      }else{
        $oResponse = new Response();
        $response = new stdClass;
        $response->success = false;
        $response->msg = "El jugador no puede ser eliminado.";
        $oResponse->setContent(json_encode($response));
        return $oResponse;
      }
    } catch (Exception $e) {
      $json = new stdClass();
      $json->success = false;
      $json->msg = "No se pudo eliminar el jugador.";
      return $json;
    }
  }
}
