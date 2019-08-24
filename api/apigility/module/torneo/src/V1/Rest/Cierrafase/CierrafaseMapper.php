<?php
namespace torneo\V1\Rest\Cierrafase;

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

class CierrafaseMapper
{
	protected $adapter;
	public function __construct(AdapterInterface $adapter)
	{
		$this->adapter = $adapter;
	}

	function controloCierreFase($torneo_id, $categoria_id, $zona_id, $fase_id){
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('fixture');
		if($fase_id == 1){
		$select->where(array('fixture_fase_id'      => $fase_id,
												 'fixture_estado'       => true,
												 'fixture_categoria_id' => $categoria_id));
		}else{
		$select->where(array(
			'fixture_categoria_id' => $categoria_id,
			'fixture_zona_id'      =>$zona_id,
			'fixture_fase_id'      => $fase_id,
			'fixture_estado'       => true));
		}
		$selectString = $sql->getSqlStringForSqlObject($select);
		// echo $selectString ."\n"; die;
		$results   = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$f = $results->toArray();
		return $f;

	}

	public function create($data)
	{
		$torneo_id    = $data->torneo_id;
		$categoria_id = $data->categoria_id;
		$zona_id      = $data->zona_id;
		//$fase_id      = $data->fase_id;

		//BUSCO LA FASE ID MAS GRANDE GENERADA EN FIXTURE PARA CERRARLA.
		$sql = new Sql($this->adapter);
		$select = "SELECT MAX(fixture_fase_id) as fase_id FROM fixture";
		$select .= " WHERE fixture_torneo_id = $torneo_id";
		$select .= " AND fixture_categoria_id = $categoria_id ";
		$select .= " AND fixture_zona_id = $zona_id";
		$results = $this->adapter->query($select, Adapter::QUERY_MODE_EXECUTE);
		$MAXfase_id = $results->toArray();
		$MAXfase_id = $MAXfase_id['0']['fase_id'];
		$fase_id    = $MAXfase_id;

		//CONTROLO PARAMETROS RECIBIDOS
		if(empty($torneo_id) || empty($categoria_id) || empty($fase_id) ){
			$json = new stdClass();
			$json->success = false;
			$json->msg = "Ningun Parametro (TORNEO/CATEGORIA/FASE) puede ser NULL ";
			return $json;
		}

		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('categoria');
		$select->where(array('categoria_id' => $categoria_id));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results      = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$categoria    = $results->toArray();
		if(empty($categoria)){
			$json = new stdClass();
			$json->success = false;
			$json->msg = "La CATEGORIA es incorrecta ";
			return $json;
		}

		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('zona');
		$select->where(array('zona_id' => $zona_id));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results      = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$zona    = $results->toArray();
		if(empty($zona)){
			$json = new stdClass();
			$json->success = false;
			$json->msg = "La ZONA es incorrecta ";
			return $json;
		}

		$controlFase = $this->controloCierreFase($torneo_id, $categoria_id, $zona_id, $fase_id);

		if (!empty($controlFase)) {
			$json = new stdClass();
			$json->success = false;
			$json->msg = "No se puede cerrar la fase $fase_id porque existe partido abierto.";
			return $json;
		}

		if ($fase_id == '1') { //FASE INICIAL
			$class = new cierroFase1($this->adapter);
			$arr = $class->getCierroFase1($torneo_id, $categoria_id, $zona_id, $fase_id);
			return $arr;
		}
		if ($fase_id == '2') { //FASE 16vos DE FINAL
			$class = new cierroFase2($this->adapter);
			$arr = $class->getCierroFase2($torneo_id, $categoria_id, $zona_id, $fase_id);
			return $arr;
		}
		if ($fase_id == '3') { //FASE OCTAVOS DE FINAL
			$class = new cierroFase3($this->adapter);
			$arr = $class->getCierroFase3($torneo_id, $categoria_id, $zona_id, $fase_id);
			return $arr;
		}
		if ($fase_id == '4') { //FASE CUARTOS DE FINAL
			$class = new cierroFase4($this->adapter);
			$arr = $class->getCierroFase4($torneo_id, $categoria_id, $zona_id, $fase_id);
			return $arr;
		}
		if ($fase_id == '5') { //FASE SEMI-FINAL
			$class = new cierroFase5($this->adapter);
			$arr = $class->getCierroFase5($torneo_id, $categoria_id, $zona_id, $fase_id);
			return $arr;
		}

		die('CIERRO FASE');
	}
}
