<?php
namespace torneo\V1\Rest\fecha;

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

class fechaMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function fetchAll()
  {
    @$zona_id = $_GET['param'];

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('fixture');
    $select->where(array('fixture_zona_id' => $zona_id));

    $selectString = $sql->getSqlStringForSqlObject($select);

    $ban_fecha = 0;

    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $fechas = $results->toArray();

    foreach ($fechas as $key => $row) {
      $r = $row ['fixture_fecha'];
      if ($ban_fecha != $r) {
        $arr [] = array(
          'fecha_id'       => $row ['fixture_fecha'],
          'fecha_descri'   => 'Fecha '.$row ['fixture_fecha']
        );
        $ban_fecha = $r;
      }

    }

    if (!empty($arr)) {
      $json = new stdClass();
      $json->success = true;
      $json->fecha = $arr;
      return $json;
    }else {
      $json = new stdClass();
      $json->success = false;
      $json->fecha = [];
      $json->msg = "No Existen el fechas.";
      return $json;
    }
  }

  public function fetchOne($id)
  {

  }
}
