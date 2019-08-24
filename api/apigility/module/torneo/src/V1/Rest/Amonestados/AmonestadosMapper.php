<?php
namespace torneo\V1\Rest\amonestados;

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

class amonestadosMapper
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

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('fixture');
    $select->where(array('fixture_id' => $fixture_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $partido = $results->toArray();

    if (!empty($partido)) {
      $arr = $this->buscoAmonestados($fixture_id, $equipo_id);

      //print_r($arr); die;

      if (!empty($arr)) {
        $json = new stdClass();
        $json->success     = true;
        $json->amonestados = $arr;
        return $json;
      }else {
        $json = new stdClass();
        $json->success = true;
        //$json->msg = "Los datos ingresados no son correctos.";
	$json->amonestados = [];
        return $json;
      }
    }else {
      $json = new stdClass();
      $json->success = true;
      $json->amonestados = [];
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

  function buscoAmonestados($fixture_id, $equipo_id)
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('tarjetas');
    //$select->where->equalTo("tarjetas_fixture_id", $fixture_id);
    $select->where(array('tarjetas_fixture_id' => $fixture_id,
                         'tarjetas_equipo_id'  => $equipo_id));    
    $select->where->isNotNull("tarjetas_amarilla");
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $amonestados = $results->toArray();

    $arr = array();

    if (!empty($amonestados)) {
      foreach ($amonestados as $key => $row) {
        $jugador = $this->buscoJugador($row ['tarjetas_jugador_id']);
        $arr [] = array(
          'text'             => $jugador['jugador_nombre'] ." " .$jugador['jugador_apellido'],
          'jugador_id'       => $jugador['jugador_id'],
          'jugador_nombre'   => $jugador['jugador_nombre'],
          'jugador_apellido' => $jugador['jugador_apellido'],
          "cant_tarjetas"    =>  $row ['tarjetas_amarilla'],
        );
      }
      return $arr;
    }
    else {
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
