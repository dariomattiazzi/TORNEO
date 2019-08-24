<?php
namespace torneo\V1\Rest\goleadores;

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

class goleadoresMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function GoleadoresDeUnPartido()
  {
    $fixture_id = $_GET["fixture_id"];
    $equipo_id  = $_GET["equipo_id"];
    $fecha      = $_GET["fecha_id"];

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('fixture');
    $select->where(array('fixture_id' => $fixture_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $partido = $results->toArray();

    //	print_r($partido); die;

    if (!empty($partido)) {
      $arr_partido = $this->buscoGoleadores($fixture_id, $equipo_id);
      //	  print_r($arr_partido); die;
      if (!empty($arr_partido)) {
        $json = new stdClass();
        $json->success       = true;
        $json->fecha_descri  = "Fecha ".$partido[0]['fixture_fecha'];
        $json->fecha_id      = $partido[0]['fixture_fecha'];
        $json->equipo_descri = $this->buscoEquipo($equipo_id);
        $json->equipo_id     = $equipo_id;
        if ($arr_partido['success'] != 'false'){
          $json->goleadores    = $arr_partido;
        }else {
          $json->goleadores  = [];          
        }
        return $json;
      }else {
        $json = new stdClass();
        $json->success = true;
	      $json->goleadores = [];
        $json->msg = "Los datos ingresados no son correctos.";
        return $json;
      }
    }else {
      $json = new stdClass();
      $json->success = true;
      $json->goleadores = [];
      $json->msg = "No Existe el partido.";
      return $json;
    }
    if (!empty($arr_jugador)) {
      $json = new stdClass();
      $json->success = true;
      $json->torneo  = $arr_jugador;
      return $json;
    }else {
      $json = new stdClass();
      $json->success = true;
      $json->torneo  = [];
      $json->msg = "No Existe el jugador.";
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
      $eq = "LIBRE";
      return $eq;
    }
  }

  function buscoGoleadores($fixture_id, $equipo_id)
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('goles');
    $select->where(array('goles_fixture_id' => $fixture_id, 'goles_equipo_id' => $equipo_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $goles = $results->toArray();

    // print_r($goles); die;//

    if (!empty($goles)) {
      foreach ($goles as $key => $row) {
        $jugador = $this->buscoJugador($row ['goles_jugador_id']);
        $arr [] = array(
          'text'             => $jugador['jugador_nombre'] ." " .$jugador['jugador_apellido'],
          'jugador_id'       => $jugador['jugador_id'],
          'jugador_nombre'   => $jugador['jugador_nombre'],
          'jugador_apellido' => $jugador['jugador_apellido'],
          "cant_goles"       =>  $row ['goles_cantidad'],
        );
      }
      return $arr;
    }else {
      $arr = array(
        'success'  => "false",
        'msg'      => "No Existe goleadores en este partido."
      );
      return $arr;
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
    $j = $jugador[0];
    return $j;

  }

  public function create($data)
  {
    try {
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
        $e = 'fixture_equipo_id1';
        $g = 'fixture_goles_eq1';
        $p = 'fixture_penales_eq1';

      }elseif ($fixture_equipo2 == $equipo_id) {
        $e = 'fixture_equipo_id2';
        $g = 'fixture_goles_eq2';
        $p = 'fixture_penales_eq2';
      }else {
        $json = new stdClass();
        $json->success = false;
        $json->msg = "Error al procesar los datos.";
        return $json;
      }
      //---------------
      if (!is_null($partido['0'][$g])) {
        //echo "EDITAR - borro los datos de goleadores/tarjetas y sigo";
        $sql = new Sql($this->adapter);
        $delete = $sql->delete();
        $delete->from('tarjetas');
        $delete->where(array('tarjetas_fixture_id' => $fixture_id, 'tarjetas_equipo_id' => $equipo_id));
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        $results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);

        $sql = new Sql($this->adapter);
        $delete = $sql->delete();
        $delete->from('sancionados');
        $delete->where(array('sancionados_fixture_id' => $fixture_id, 'sancionados_equipo_id' => $equipo_id));
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        $results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);

        $sql = new Sql($this->adapter);
        $delete = $sql->delete();
        $delete->from('goles');
        $delete->where(array('goles_fixture_id' => $fixture_id, 'goles_equipo_id' => $equipo_id));
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        $results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);

        $sql = new Sql($this->adapter);
        $update = $sql->update();
        $update->table('fixture');
        $update->set(array("$g" => null));
        $update->where->equalTo("fixture_id", $fixture_id);
        $update->where->AND;
        $update->where->equalTo("$e", $equipo_id);
        //$update->where->OR;
        //$update->where->equalTo("fixture_equipo_id2", $equipo_id);
        $updateString = $sql->getSqlStringForSqlObject($update);
        $this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
      }

      $goleadores  = json_decode($data->goleadores);

      // print_r($goleadores); die;

      $amonestados = json_decode($data->amonestados);
      $expulsados  = json_decode($data->expulsados);
      $penales     = json_decode($data->penales);

      $cant_goles = 0;
      $i = 0;
      //GRABO LOS GOLEADORES
      foreach ($goleadores  as $key => $row) {
        // print_r($row->jugador_id); die;
        $jugador_id = $row->jugador_id;

        $i++;
        $dataInsert = array(
          'goles_jugador_id'   => $row->jugador_id,
          'goles_cantidad'     => $row->cant_goles,
          'goles_equipo_id'    => $equipo_id,
          'goles_fixture_id'   => $fixture_id,
          'goles_torneo_id'    => $torneo_id,
          'goles_categoria_id' => $categoria_id,
          'goles_zona_id'      => $zona_id,

        );

        $sql = new Sql($this->adapter);
        $insert = $sql->insert();
        $insert->into('goles');
        $insert->values($dataInsert);
        $insertString = $sql->getSqlStringForSqlObject($insert);
        // echo $insertString; die;
        $results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
        $cant_goles += $row->cant_goles;
      }

      if ($cant_goles == '') {
        $cant_goles = 0;
      }

      //GRABO resultado en la tabla fixture
      $sql = new Sql($this->adapter);
      $update = $sql->update();
      $update->table('fixture');
      $update->set(array(
        "$g" => $cant_goles,
        "$p" => $penales,
      ));
      $update->where->equalTo("fixture_id", $fixture_id);
      $update->where->AND;
      $update->where->equalTo("$e", $equipo_id);
      //$update->where->OR;
      //$update->where->equalTo("fixture_equipo_id2", $equipo_id);
      $updateString = $sql->getSqlStringForSqlObject($update);
      //echo "\n".$updateString; die;
      $this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
      //FIN GRABO resultado en la tabla fixture


      //GRABO LOS AMONESTADOS
      foreach ($amonestados as $key => $row) {
        $dataInsert = array(
          'tarjetas_jugador_id'   => $row->jugador_id,
          'tarjetas_equipo_id'    => $equipo_id,
          'tarjetas_fixture_id'   => $fixture_id,
          'tarjetas_amarilla'     => $row->cant_tarjetas,
          'tarjetas_torneo_id'    => $torneo_id,
          'tarjetas_categoria_id' => $categoria_id,
          'tarjetas_zona_id'      => $zona_id,
        );

        $sql = new Sql($this->adapter);
        $insert = $sql->insert();
        $insert->into('tarjetas');
        $insert->values($dataInsert);
        $insertString = $sql->getSqlStringForSqlObject($insert);
        //echo "$insertString"; die;
        $results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
        // print_r($dataInsert); die;
      }

      //GRABO LOS EXPULSADOS
      foreach ($expulsados as $key => $row) {
        $dataInsert = array(
          'sancionados_jugador_id'   => $row->jugador_id,
          'sancionados_equipo_id'    => $equipo_id,
          'sancionados_fixture_id'   => $fixture_id,
          'sancionados_roja'         => 1,
          'sancionados_sancion'      => $row->cant_fechas,
          'sancionados_torneo_id'    => $torneo_id,
          'sancionados_categoria_id' => $categoria_id,
          'sancionados_zona_id'      => $zona_id,
        );
        $sql = new Sql($this->adapter);
        $insert = $sql->insert();
        $insert->into('sancionados');
        $insert->values($dataInsert);
        $insertString = $sql->getSqlStringForSqlObject($insert);
        //echo "$insertString"; die;
        $results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
      }

      $json = new stdClass();
      $json->success = true;
      return $json;

    } catch (Exception $e) {
      $json = new stdClass();
      $json->success = false;
      $json->msg = "No se guardaron los datos.";
      return $json;
    }
  }
}
