<?php
namespace torneo\V1\Rest\expulsados;

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

class expulsadosMapper
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
    $fixture_id = $_GET["fixture_id"];
    $equipo_id  = $_GET["equipo_id"];
    $fecha      = $_GET["fecha"];
    //echo $fixture_id ." - ".$equipo_id; die;

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('fixture');
    $select->where(array('fixture_id' => $fixture_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $partido = $results->toArray();

    //print_r($partido); die;

    if (!empty($partido)) {
      $arr = $this->buscoExpulsados($fixture_id, $equipo_id);

      if (!empty($arr)) {
        $json = new stdClass();
        $json->success     = true;
        $json->expulsados = $arr;
        return $json;
      }else {
        $json = new stdClass();
        $json->success = true;
	$json->expulsados = [];
        return $json;
      }
    }else {
      $json = new stdClass();
      $json->success = true;
      $json->expulsados = [];
      $json->msg = "No Existe el partido.";
      return $json;
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

  function buscoExpulsados($fixture_id, $equipo_id)
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('sancionados');
    $select->where(array("sancionados_fixture_id" => $fixture_id,
                         "sancionados_equipo_id"  => $equipo_id));
    $select->where->isNotNull("sancionados_roja");
    $selectString = $sql->getSqlStringForSqlObject($select);

    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $expulsados = $results->toArray();

    $arr = array();

    if (!empty($expulsados)) {
      foreach ($expulsados as $key => $row) {
        $jugador = $this->buscoJugador($row ['sancionados_jugador_id']);
        $arr [] = array(
          'text'             => $jugador['jugador_nombre'] ." " .$jugador['jugador_apellido'],
          'jugador_id'       => $jugador['jugador_id'],
          'jugador_nombre'   => $jugador['jugador_nombre'],
          'jugador_apellido' => $jugador['jugador_apellido'],
          "cant_fechas"      =>  $row ['sancionados_sancion'],
        );
      }
       return $arr;
    }else {
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

  }

  public function delete($id)
  {
  }

}
