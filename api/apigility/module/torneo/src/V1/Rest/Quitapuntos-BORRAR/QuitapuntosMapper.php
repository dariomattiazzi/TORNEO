<?php
namespace torneo\V1\Rest\Quitapuntos;

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

class quitapuntosMapper
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
    $zona_id      = $_GET["zona_id"];

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('quitapuntos');
    $select->where(array('quitapuntos_torneo_id' => $torneo_id,
                         'quitapuntos_categoria_id' => $categoria_id,
                         'quitapuntos_zona_id' => $zona_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $equipos_con_suspencion = $results->toArray();

//    print_r($equipos_con_suspencion);

    // if (!empty($equipos_con_suspencion)) {
    //   foreach ($equipos_con_suspencion as $key => $row) {
    //     $equipo = $this->buscoEquipo($row ['quitapuntos_equipo_id']);
    //     $arr [] = array(
    //       'equipo'       => $equipo,
    //       "puntos"       =>  $row ['quitapuntos_cant'],
    //     );
    //   }
    //   return $arr;
    // }else {
    //   $arr = array(
    //     'success'  => "true",
    //     'msg'      => "No hay para mostrar."
    //   );
    //   return $arr;
    // }



    if(count($equipos_con_suspencion)==0) {
        $arr = array(
          'success'  => "true",
          'msg'      => "No hay para mostrar."
        );
        return $arr;
    }else{
        foreach ($equipos_con_suspencion as $key => $row) {
          $equipo = $this->buscoEquipo($row ['quitapuntos_equipo_id']);
          $arr [] = array(
            'equipo'       => $equipo,
            "puntos"       =>  $row ['quitapuntos_cant'],
          );
        }
        return $arr;
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

  public function create($data)
  {
    if ( $data->update == 'true') {
      // echo "ACTUALIZA";
      return $this->actualiza($data);
    }else {
      // echo "CREA";
      return $this->crea($data);
    }
  }

  public function crea($data)
	{

		try {
			$dataInsert = array(
				"quitapuntos_torneo_id" => $data->torneo_id,
				"quitapuntos_categoria_id" => $data->categoria_id,
        "quitapuntos_zona_id" => $data->zona_id,
        "quitapuntos_equipo_id" => $data->equipo_id,
        "quitapuntos_cant" => $data->cant,
			);
			$sql = new Sql($this->adapter);
			$insert = $sql->insert();
			$insert->into('quitapuntos');
			$insert->values($dataInsert);
			$insertString = $sql->getSqlStringForSqlObject($insert);
			$results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
			$json = new stdClass();
			$json->success = true;
			return $json;
		} catch (Exception $e) {
			$json = new stdClass();
			$json->success = false;
			$json->msg = "No se pudo ingresar el datos.";
			return $json;
		}
	}
  public function actualiza($data)
  {
    try {
			$id = $data->id;
			$sql = new Sql($this->adapter);
			$update = $sql->update();
			$update->table('quitapuntos');
			$update->set(array(
        "quitapuntos_torneo_id" => $data->torneo_id,
				"quitapuntos_categoria_id" => $data->categoria_id,
        "quitapuntos_zona_id" => $data->zona_id,
        "quitapuntos_equipo_id" => $data->equipo_id,
        "quitapuntos_cant" => $data->cant,
			));
			$update->where->equalTo("quitapuntos_id", $id);
			$updateString = $sql->getSqlStringForSqlObject($update);
      // $updateString; die;
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

  public function delete($id)
  {
    try {
        $sql = new Sql($this->adapter);
        $delete = $sql->delete();
        $delete->from('quitapuntos');
        $delete->where(array(
          'quitapuntos_id' => $id
        ));
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        // echo $deleteString; die;
        $results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
        $oResponse = new Response();
        $response = new stdClass;
        $response->success = true;
        $response->mensaje = "Sancion eliminada.";
        $oResponse->setContent(json_encode($response));
        return $oResponse;
    } catch (Exception $e) {
      $json = new stdClass();
      $json->success = false;
      $json->msg = "No se pudo eliminar la sancion.";

      return $json;

    }
  }
}
