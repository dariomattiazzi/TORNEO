<?php
namespace torneo\V1\Rest\reglas;

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

class reglasMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function fetchAll()
  {
    $cant_torneosACT        = $this->torneoactivos();
    $cant_categoriasXtorneo = '3';

    
  }

  public function torneoactivos()
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('torneo');
    $select->where(array('torneo_estado is true'));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $torneo = $results->toArray();

    $torneoactivos = count($torneo);
    return $torneoactivos;
  }




  public function fetchOne($id)
  {
  }

  public function create($data)
  {
  }

  public function delete($id)
  {
  }
}
