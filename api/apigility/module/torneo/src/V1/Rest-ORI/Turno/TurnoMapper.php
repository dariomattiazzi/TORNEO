<?php
namespace torneo\V1\Rest\turno;

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

class turnoMapper
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
      $select->from('turno');
      $selectString = $sql->getSqlStringForSqlObject($select);
      $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
      $turnos = $results->toArray();

      foreach ($turnos as $key => $row) {
        $arr [] = array(
            'turno_id'               => $row ['turno_id'],
            'turno_descri'           => $row ['turno_descri']
        );
      }
      if (!empty($arr)) {
        $json = new stdClass();
        $json->success = true;
        $json->turnos = $arr;
    	return $json;
      }else {
        $json = new stdClass();
        $json->success = true;
        $json->msg = "No Existen turnos cargados.";
	$json->turnos = [];
    	return $json;
      }
    }

    public function fetchOne($id)
    {
      echo "fetchone"; die;
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
