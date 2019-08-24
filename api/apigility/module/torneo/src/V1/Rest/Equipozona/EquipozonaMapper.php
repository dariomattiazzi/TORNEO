<?php
namespace torneo\V1\Rest\equipozona;

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

class equipozonaMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function fetchAll()
  {
    echo "fetchALL"; die;
  }

  public function fetchOne($id)
  {
    $zona_id = $id;

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('equipo_zona');
    $select->where(array('zona_id' => $zona_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $equipos = $results->toArray();


    $i = 0;
    foreach ($equipos as $key => $row) {
      $equipo_id = $row['equipo_id'];
      $equipo_nombre = $this->buscoEquipo($equipo_id);

      $equipo_zona[$i]['equipo_id'] = $equipo_id;
      $equipo_zona[$i]['equipo_nombre'] = $equipo_nombre;
      $i++;
    }
    foreach ($equipo_zona as $key => $row) {
      $aux_prom[$key] = $row['equipo_nombre'];
    }
    array_multisort(@$aux_prom, SORT_ASC, $equipo_zona);

    if (!empty($equipo_zona)) {
      $json = new stdClass();
      $json->success = true;
      $json->equipo_zona = $equipo_zona;
      return $json;
    }else{
      $json = new stdClass();
      $json->success = true;
      $json->equipo_zona = [];
      $json->msg = "La zona elegida no tiene equipos cargados.";
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

  public function create($data)
  {
    $zona_id   = $data->zona_id;
    $equipo_id = $data->equipo_id;
    $torneo_id = $data->torneo_id;

    //BUSCO CANTIDAD DE EQUIPOS DE LA ZONA
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('zona');
    $select->where(array('zona_id' => $zona_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $zona = $results->toArray();
    $cant_equipo_x_zona = $zona['0']['zona_cantidad_equipos'];

    //BUSCO CANTIDAD DE EQUIPO QUE LA ZONA YA TIENE ASIGNADOS
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('equipo_zona');
    $select->where(array('zona_id' => $zona_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $equipos = $results->toArray();
    $equipos_en_la_zona = count($equipos);

    //SI LA CANT. DE EQUIPOS ASIGNADOS A LA ZONA ES MENOR A LA CANTIDAD MAX. DE EQUIPOS PERMITIDOS EN LA ZONA
    if($equipos_en_la_zona < $cant_equipo_x_zona){
      //BUSCO LAS CATEGORIAS DEL TORNEO
      $sql = new Sql($this->adapter);
      $select = $sql->select();
      $select->from('categoria');
      $select->where(array('categoria_torneo_id' => $torneo_id));
      $selectString = $sql->getSqlStringForSqlObject($select);
      $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
      $categorias = $results->toArray();

      foreach ($categorias as $key => $row) {
        //BUSCO LAS ZONAS DE CADA CATEGORIAS DEL TORNEO.
        $categoria_id = $row['categoria_id'];
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('zona');
        $select->where(array('zona_categoria_id' => $categoria_id));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        $zonas = $results->toArray();

        //RECORRO TODAS LAS ZONAS DE LAS CATEGORIAS PARA SABER SI EL EQUIPO YA ESTÁ O NO EN OTRA ZONA.
        foreach ($zonas as $key => $row) {
          $zona = $row['zona_id'];
          $sql = new Sql($this->adapter);
          $select = $sql->select();
          $select->from('equipo_zona');
          $select->where(array('zona_id' => $zona, 'equipo_id' => $equipo_id, ));
          $selectString = $sql->getSqlStringForSqlObject($select);
          //echo $selectString ."\n";
          $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
          $equipo_zona = $results->toArray();

          if(!empty($equipo_zona)){
            $json = new stdClass();
            $json->success = false;
            $json->msg = "El equipo ya se encuntra en otra zona.";
            return $json;
          }
        }
      }
      try {
        $dataInsert = array(
          "equipo_id"  => $data->equipo_id,
          "zona_id"    => $data->zona_id
        );
        $sql = new Sql($this->adapter);
        $insert = $sql->insert();
        $insert->into('equipo_zona');
        $insert->values($dataInsert);
        $insertString = $sql->getSqlStringForSqlObject($insert);

        $results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
        $json = new stdClass();
        $json->success = true;
        return $json;
      } catch (Exception $e) {
        $json = new stdClass();
        $json->success = false;
        $json->msg = "No se pudo realizar la operacion.";
        return $json;
      }
    }else {
      $json = new stdClass();
      $json->success = false;
      $json->msg = "La zona ya tiene la cantidad maxima de equipos.";
      return $json;
    }
  }


  public function delete($id)
  {
    $e = explode("-", $id);

    //print_r($e); die;
    $zona_id   = $e['0'];
    $equipo_id = $e['1'];

    //print_r($e);
    // echo $equipo_id."\n";
    // echo $zona_id."\n";
    // die;
    try {
      $sql = new Sql($this->adapter);
      $select = $sql->select();
      $select->from('equipo_zona');
      $select->where(array('equipo_id ' => $equipo_id, 'zona_id ' => $zona_id));
      $selectString = $sql->getSqlStringForSqlObject($select);

      $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
      $equipo = $results->toArray();

      if (!empty($equipo)) {
        $sql = new Sql($this->adapter);
        $delete = $sql->delete();
        $delete->from('equipo_zona');
        $delete->where(array('equipo_id ' => $equipo_id, 'zona_id ' => $zona_id));
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        //echo $deleteString; die;
        $results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
        $oResponse = new Response();
        $response = new stdClass;
        $response->success = true;
        $response->mensaje = "Equipo eliminado.";
        $oResponse->setContent(json_encode($response));
        return $oResponse;
      }else{
        $oResponse = new Response();
        $response = new stdClass;
        $response->success = false;
        $response->mensaje = "El equipo no puede ser eliminado.";
        $oResponse->setContent(json_encode($response));
        return $oResponse;
      }
    } catch (Exception $e) {
      $json = new stdClass();
      $json->success = false;
      $json->msg = "No se pudo eliminar el equipo.";
      return $json;
    }
  }
}
