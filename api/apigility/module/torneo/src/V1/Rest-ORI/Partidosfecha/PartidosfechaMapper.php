<?php
namespace torneo\V1\Rest\partidosfecha;

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

class partidosfechaMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function fetchAll($data)
  {
    @$torneo_id    = $data->torneo_id;
    @$categoria_id = $data->categoria_id;
    @$zona_id      = $data->zona_id;
    @$fecha_id     = $data->fecha_id;

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('fixture');
    $select->where(array('fixture_torneo_id'    => $torneo_id,
    'fixture_categoria_id' => $categoria_id,
    'fixture_zona_id'      => $zona_id,
    'fixture_fecha'        => $fecha_id,
    //'fixture_estado'     => '1',
  ));
  $selectString = $sql->getSqlStringForSqlObject($select);
  $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
  $partidos = $results->toArray();

  // print_r($partidos); die;

  foreach ($partidos as $key => $row) {
    $arr_partidos [] = array(
      'imagen1'      => "",
      'equipo1'      => $this->buscoEquipo($row ['fixture_equipo_id1']),
      'equipo2'      => $this->buscoEquipo($row ['fixture_equipo_id2']),
      'equipo1_id'   => $row ['fixture_equipo_id1'],
      'equipo2_id'   => $row ['fixture_equipo_id2'],
      'fecha_descri' => "Fecha ".$row ['fixture_fecha'],
      'fecha_id'     => $row ['fixture_fecha'],
      'fixture_id'   => $row ['fixture_id'],
      'imagen2'      => "",
    );
  }

  if (!empty($arr_partidos)) {
    $json = new stdClass();
    $json->success = true;
    $json->data = $arr_partidos;
    return $json;
  }else {
    $json = new stdClass();
    $json->success = true;
    $json->data = [];
    $json->msg = "No Existen partidos.";
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
public function fetchOne($id)
{
}
}
