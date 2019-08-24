<?php
namespace torneo\V1\Rest\vallamenosvencida;

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

class vallamenosvencidaMapper
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

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('fixture');
    $select->where(array('fixture_torneo_id'    => $torneo_id,
    'fixture_categoria_id' => $categoria_id,
    'fixture_estado'       => "false"));
    $select->where->notequalTo("fixture_equipo_id2", 9999);
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $fix = $results->toArray();

    $arr = array();

    foreach ($fix as $key => $row) {
      $eq1 = $row['fixture_equipo_id1'];
      $eq2 = $row['fixture_equipo_id2'];

      $equipo1_nombre = $this->buscoEquipo($eq1);
      //		$equipo1_nombre = $this->buscoEquipo($eq1);
      $arr[$eq1] = array('pj' => 0, 'gc' => 0, 'prom_gol_rec' => 0, 'equipo_id' => $eq1,'equipo_nombre' => $equipo1_nombre);

      $equipo2_nombre = $this->buscoEquipo($eq2);
      //		$equipo2_nombre = $this->buscoEquipo($eq2);
      $arr[$eq2] = array('pj' => 0, 'gc' => 0, 'prom_gol_rec' => 0, 'equipo_id' => $eq2,'equipo_nombre' => $equipo2_nombre);
    }

    // print_r($arr); die;

    foreach ($fix as $key => $row) {
      $eq1       = $row['fixture_equipo_id1'];
      $eq2       = $row['fixture_equipo_id2'];
      $goles_eq1 = $row['fixture_goles_eq1'];
      $goles_eq2 = $row['fixture_goles_eq2'];

      $gc1 = $arr[$eq1]['gc'] + $goles_eq2;
      $pj1 = $arr[$eq1]['pj'] + 1;
      $arr[$eq1]['pj']    = $pj1;
      $arr[$eq1]['gc']    = $gc1;
      $prom = $gc1/$pj1;
      $arr[$eq1]['prom_gol_rec'] = round($prom, 2);

      $pj2 = $arr[$eq2]['pj'] + 1;
      $gc2 = $arr[$eq2]['gc'] + $goles_eq1;
      $arr[$eq2]['pj']    = $pj2;
      $arr[$eq2]['gc']    = $gc2;
      $prom = $gc2/$pj2;
      $arr[$eq2]['prom_gol_rec']   = round($prom, 2);
    }

    foreach ($arr as $key => $row) {
      $aux_prom[$key] = $row['prom_gol_rec'];
      $aux_pj[$key]  = $row['pj'];
    }

    array_multisort(@$aux_prom, SORT_ASC, @$aux_pj, SORT_DESC, $arr);

    if (!empty($arr)) {
      $json = new stdClass();
      $json->success = true;
      $json->valla = $arr;
      return $json;
    }else{
      $json = new stdClass();
      $json->success = true;
      $json->valla = [];
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
  public function fetchOne()
  {
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
