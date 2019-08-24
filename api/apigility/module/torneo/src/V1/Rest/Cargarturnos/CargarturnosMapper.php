<?php
namespace torneo\V1\Rest\cargarturnos;

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

class cargarturnosMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function create($data)
  {
//  	echo $data->fixture_id; die;
    try {
        $sql = new Sql($this->adapter);
        $update = $sql->update();
        $update->table('fixture');
        $update->set(array('fixture_turno_id'  => $data->turno_id, 
						   'fixture_cancha_id' => $data->cancha_id));
        $update->where->equalTo("fixture_id", $data->fixture_id);
        $updateString = $sql->getSqlStringForSqlObject($update);
  //      echo $updateString ."\n"; die;
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


  public function fetchAll()
  {

  }

  public function fetchOne()
  {
  }


}
