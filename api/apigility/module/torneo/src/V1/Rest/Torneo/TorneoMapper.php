<?php
namespace torneo\V1\Rest\torneo;

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

class torneoMapper
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
		$select->from('torneo');
		$select->where(array('torneo_estado is true'));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$torneo = $results->toArray();
		
		// print_r($cat); die();
		foreach ($torneo as $key => $row) {
			$id_torneo = $row ['torneo_id'];
			$arr_torneo [] = array(
				'torneo_id'       => $row ['torneo_id'],
				'torneo_descri'   => $row ['torneo_descri'],
				'torneo_estado'   => $row ['torneo_estado'],
				'torneo_deporte_id'   => $row ['torneo_deporte_id']
			);
		}
		
		if (!empty($arr_torneo)) {
			$json = new stdClass();
			$json->success = true;
			$json->torneos = $arr_torneo;
			return $json;
		}else {
			$json = new stdClass();
			$json->success = true;
			$json->torneos = [];
			$json->msg = "No Existen torneo.";
			return $json;
		}
	}
	
	public function fetchOne($id)
	{
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('torneo');
		$select->where(array('torneo_id' => $id));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$torneo = $results->toArray();
		
		// print_r($cat); die();
		foreach ($torneo as $key => $row) {
			$arr [] = array(
				'torneo_id'       => $row ['torneo_id'],
				'torneo_descri'   => $row ['torneo_descri'],
				'torneo_estado'   => $row ['torneo_estado']
			);
		}
		
		// print_r($arr); die();
		
		if (!empty($arr)) {
			$json = new stdClass();
			$json->success = true;
			$json->torneo = $arr;
			return $json;
		}else {
			$json = new stdClass();
			$json->success = true;
			$json->torneo = [];
			$json->msg = "No Existe el torneo.";
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
			
			$estado = $data->torneo_estado;
			
			if($estado == 'true'){
				$estado = '1';
			}else{
				$estado = '0';
			}
			$dataInsert = array(
				"torneo_descri" => $data->torneo_descri,
				"torneo_estado" => $estado
			);
			$sql = new Sql($this->adapter);
			$insert = $sql->insert();
			$insert->into('torneo');
			$insert->values($dataInsert);
			$insertString = $sql->getSqlStringForSqlObject($insert);
			$results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
			$json = new stdClass();
			$json->success = true;
			return $json;
		} catch (Exception $e) {
			$json = new stdClass();
			$json->success = false;
			$json->msg = "No se pudo ingresar el torneo.";
			return $json;
		}
	}
	
	public function actualiza($data)
	{
		try {
			$id = $data->torneo_id;
			$sql = new Sql($this->adapter);
			$update = $sql->update();
			$update->table('torneo');
			$update->set(array(
				"torneo_descri" => $data->torneo_descri,
				"torneo_estado" => $data->torneo_estado
			));
			$update->where->equalTo("torneo_id", $id);
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
			$select->from('torneo');
			$select->where('torneo_id = '.$id);
			$selectString = $sql->getSqlStringForSqlObject($select);
			$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
			$torneo = $results->toArray();
			
			if (!empty($torneo)) {
				$sql = new Sql($this->adapter);
				$delete = $sql->delete();
				$delete->from('torneo');
				$delete->where(array(
					'torneo_id' => $id
				));
				$deleteString = $sql->getSqlStringForSqlObject($delete);
				//echo $deleteString; die;
				$results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
				$oResponse = new Response();
				$response = new stdClass;
				$response->success = true;
				$response->mensaje = "Torneo eliminado.";
				$oResponse->setContent(json_encode($response));
				return $oResponse;
			}else{
				$oResponse = new Response();
				$response = new stdClass;
				$response->success = false;
				$response->mensaje = "El Torneo no puede ser eliminado.";
				$oResponse->setContent(json_encode($response));
				return $oResponse;
			}
		} catch (Exception $e) {
			$json = new stdClass();
			$json->success = false;
			$json->msg = "No se pudo eliminar el Torneo.";
			return $json;
		}
	}
}
