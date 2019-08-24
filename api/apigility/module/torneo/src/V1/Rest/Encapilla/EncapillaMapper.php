<?php
namespace torneo\V1\Rest\encapilla;

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

class encapillaMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function fetchAll()
  {
    $torneo_id = $_GET["torneo_id"];
    $categoria_id  = $_GET["categoria_id"];

    $array_encapilla = array(2, 5, 8, 11, 14, 17, 20);

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('tarjetas');
    $select->join(array("jugador" => "jugador"),'tarjetas.tarjetas_jugador_id = jugador.jugador_id',array('*'),'inner');
    $select->join(array("equipo"  => "equipo"),'tarjetas.tarjetas_equipo_id = equipo.equipo_id',array('*'),'inner');
    $select->where(array('tarjetas_torneo_id' => $torneo_id));
    $select->where(array('tarjetas_categoria_id' => $categoria_id));
    $select->columns(array('tarjXjug' => new \Zend\Db\Sql\Expression('SUM(tarjetas.tarjetas_amarilla)')));
    $select->group('tarjetas.tarjetas_jugador_id');
    $select->order('tarjXjug DESC');
    $selectString = $sql->getSqlStringForSqlObject($select);
    //echo $selectString; die;
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $tarjetasXjugador = $results->toArray();
    // print_r($tarjetasXjugador); die;

    $arr = array();

    if (!empty($tarjetasXjugador)) {
      foreach ($tarjetasXjugador as $key => $row) {
        if (in_array($row['tarjXjug'], $array_encapilla)) {
          $arr [] = array(
            'text'             => $row['jugador_nombre'] ." " .$row ['jugador_apellido'],
            'jugador_id'       => $row['jugador_id'],
            'equipo'           => $row['equipo_nombre'],
            'cantamarillas'    => $row['tarjXjug'],
          );
        }
      }
      // print_r($arr); die;
      if (!empty($arr)) {
        $json = new stdClass();
        $json->success = true;
        $json->encapilla = $arr;
        return $json;
      }else {
        $json = new stdClass();
        $json->success = true;
	$json->encapilla = [];
        $json->msg = "No hay registros para mostrar.";
        return $json;
      }
    }else {
      $json = new stdClass();
      $json->success = true;
      $json->encapilla = [];
      $json->msg = "No hay registros para mostrar.";
      return $json;
    }
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
