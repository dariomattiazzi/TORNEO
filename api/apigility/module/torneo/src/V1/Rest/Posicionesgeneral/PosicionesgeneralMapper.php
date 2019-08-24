<?php
namespace torneo\V1\Rest\posicionesgeneral;

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

class PosicionesgeneralMapper
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
    $select->from('zona');
    $select->where(array('zona_categoria_id' => $categoria_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results   = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $arr_zonas = $results->toArray();

    $cantidaEquipoXCat = 0;
    $arr_general = array();
    $arr30 = array();

    foreach ($arr_zonas as $key => $row) {
      $zona_id = $row['zona_id'];
      $class = new armoTabla($this->adapter);
      $arr = $class->getPosiciones($torneo_id, $categoria_id, $zona_id);

      foreach ($arr as $clave => $fila) {
        $arr_general[$clave][] = $fila;
      }
      $cantidaEquipoXCat += $row['zona_cantidad_equipos'];
    }
    // count($arr_general) CANTIDAD DE EQUIPOS POR CATEGORIA
    for ($i = 0; $i <= count($arr_general)-1; $i++) {
      $arr = $arr_general[$i];
      foreach ($arr as $key => $row) {
        $aux_ptos[$key] = $row['ptos'];
        $aux_dif[$key]  = $row['dif'];
        $aux_gf[$key]  = $row['gf'];
      }
      array_multisort($aux_ptos, SORT_DESC, $aux_dif, SORT_DESC, $aux_gf, SORT_DESC, $arr);
      foreach ($arr as $key => $row) {
        $arr30[] = $row;
      }
    }

    foreach ($arr30 as $clave => $fila) {
      $arr30[$clave]['pos-gral'] = $clave + 1 ;
    }
    //print_r($arr30); die;
    if (!empty($arr30)) {
      $json = new stdClass();
      $json->success = true;
      $json->posiciones = $arr30;
      return $json;
    }else{
      $json = new stdClass();
      $json->success = true;
      $json->posiciones = [];
      $json->msg = "No hay registros.";
      return $json;
    }
  }

}
