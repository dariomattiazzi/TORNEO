<?php
namespace torneo\V1\Rest\categoria;

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

class categoriaMapper
{
    protected $adapter;
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function fetchAll()
    {
      @$torneo = $_GET["param"];

      $sql = new Sql($this->adapter);
      $select = $sql->select();
      $select->from('categoria');
      $select->where(array('categoria_torneo_id' => $torneo));
      $selectString = $sql->getSqlStringForSqlObject($select);
      $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
      $categoria = $results->toArray();
      // print_r($categoria); die();
      foreach ($categoria as $key => $row) {
        $arr [] = array(
            'categoria_id'                     => $row ['categoria_id'],
            'categoria_descri'                 => $row ['categoria_descri'],
            'categoria_torneo_id'              => $row ['categoria_torneo_id'],
            'categoria_juega_coparevancha'     => $row['categoria_juega_coparevancha'],
            'categoria_cant_a_copacampeonato'  => $row['categoria_cant_a_copacampeonato'],
            'categoria_cant_a_coparevancha'    => $row['categoria_cant_a_coparevancha']
        );
      }

      // print_r($arr); die();

      if (!empty($arr)) {
        $json = new stdClass();
        $json->success = true;
        $json->categorias = $arr;
        return $json;
      }else {
        $json = new stdClass();
        $json->success = false;
	      $json->categorias = [];
        $json->msg = "No Existen categoria.";
    	return $json;
      }
    }

    public function fetchOne($id)
    {
      $sql = new Sql($this->adapter);
      $select = $sql->select();
      $select->from('categoria');
      $select->where(array('categoria_id' => $id));
      $selectString = $sql->getSqlStringForSqlObject($select);
      $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
      $categoria = $results->toArray();

      // print_r($cat); die();
      foreach ($categoria as $key => $row) {
        $arr [] = array(
            'categoria_id'         => $row ['categoria_id'],
            'categoria_descri'     => $row ['categoria_descri'],
            'categoria_torneo_id' => $row ['categoria_torneo_id'],
            'categoria_juega_coparevancha'     => $row['categoria_juega_coparevancha'],
            'categoria_cant_a_copacampeonato'  => $row['categoria_cant_a_copacampeonato'],
            'categoria_cant_a_coparevancha'    => $row['categoria_cant_a_coparevancha']
        );
      }

      if (!empty($arr)) {
        $json = new stdClass();
        $json->success = true;
        $json->categoria = $arr;
    	return $json;
      }else {
        $json = new stdClass();
        $json->success = false;
	      $json->categoria = [];
        $json->msg = "No Existe la categoria.";
    	return $json;
      }
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
              "categoria_descri" => $data->categoria_descri,
              "categoria_torneo_id" => $data->categoria_torneo_id,
              "categoria_juega_coparevancha"     => $data->categoria_juega_coparevancha,
              "categoria_cant_a_copacampeonato"  => $data->categoria_cant_a_copacampeonato,
              "categoria_cant_a_coparevancha"    => $data->categoria_cant_a_coparevancha
          );
          $sql = new Sql($this->adapter);
          $insert = $sql->insert();
          $insert->into('categoria');
          $insert->values($dataInsert);
          $insertString = $sql->getSqlStringForSqlObject($insert);
          $results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
          $json = new stdClass();
          $json->success = true;
          return $json;
        } catch (Exception $e) {
          $json = new stdClass();
          $json->success = false;
          $json->msg = "No se pudo ingresar la categoria.";
          return $json;
        }
    }

    public function actualiza($data)
    {
      try {
          $id = $data->categoria_id;
          $sql = new Sql($this->adapter);
          $update = $sql->update();
          $update->table('categoria');
          $update->set(array(
            "categoria_descri" => $data->categoria_descri,
            "categoria_torneo_id" => $data->categoria_torneo_id,
            "categoria_juega_coparevancha"     => $data->categoria_juega_coparevancha,
            "categoria_cant_a_copacampeonato"  => $data->categoria_cant_a_copacampeonato,
            "categoria_cant_a_coparevancha"    => $data->categoria_cant_a_coparevancha
          ));
          $update->where->equalTo("categoria_id", $id);
          $updateString = $sql->getSqlStringForSqlObject($update);
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
        $select = $sql->select();
        $select->from('categoria');
        $select->where('categoria_id = '.$id);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        $categoria = $results->toArray();

        if (!empty($categoria)) {
          $sql = new Sql($this->adapter);
          $delete = $sql->delete();
          $delete->from('categoria');
          $delete->where(array(
              'categoria_id' => $id
          ));
          $deleteString = $sql->getSqlStringForSqlObject($delete);
          //echo $deleteString; die;
          $results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
          $oResponse = new Response();
          $response = new stdClass;
          $response->success = true;
          $response->mensaje = "Categoria eliminada.";
          $oResponse->setContent(json_encode($response));
          return $oResponse;
        }else{
          $oResponse = new Response();
          $response = new stdClass;
          $response->success = false;
          $response->mensaje = "La categoria no puede ser eliminada.";
          $oResponse->setContent(json_encode($response));
          return $oResponse;
        }
      } catch (Exception $e) {
        $json = new stdClass();
        $json->success = false;
        $json->msg = "No se pudo eliminar el categoria.";
        return $json;
      }
    }

}
