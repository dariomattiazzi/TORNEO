<?php
namespace torneo\V1\Rest\Abropartido;

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

class AbropartidoMapper
{
	protected $adapter;
	public function __construct(AdapterInterface $adapter)
	{
		$this->adapter = $adapter;
	}

public function create($data)
{
	$fixture_id = $data->fixture_id;

	$sql = new Sql($this->adapter);
	$select = $sql->select();
	$select->from('fixture');
	$select->where(array('fixture_id' => $fixture_id, 'fixture_estado' => false));
	$selectString = $sql->getSqlStringForSqlObject($select);
	$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
	$partido = $results->toArray();

	try {
		//RESTO 1 FECHA DE LOS JUG SANCIONADOS DE LOS EQUIPOS DEL PARTIDO.
		$this->SumoFecha_Sancionados($fixture_id, $partido);

		//GRABO LOS JUG QUE FUERON SANCIONADOS DE LOS EQUIPOS DEL PARTIDO.
		$this->Borro_Sancionados($fixture_id);

		//CAMBIO EL ESTADO DEL PARTIDO.
		$this->CambioEstado_Partido($fixture_id);

	} catch (Exception $e) {
		$json = new stdClass();
		$json->success = false;
		$json->msg = "No se guardaron los datos.";
		return $json;
	}

	$json = new stdClass();
	$json->success = true;
	return $json;

}

function CambioEstado_Partido($fixture_id){
	$sql = new Sql($this->adapter);
	$update = $sql->update();
	$update->table('fixture');
	$update->set(array("fixture_estado" => '1'));
	$update->where->equalTo("fixture_id", $fixture_id);
	$updateString = $sql->getSqlStringForSqlObject($update);
	$this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
}

function Borro_Sancionados($fixture_id, $partido)
{
	$sql = new Sql($this->adapter);
	$delete = $sql->delete();
	$delete->from('sancionados');
	$delete->where(array('sancionados_fixture_id' => $fixture_id));
	$deleteString = $sql->getSqlStringForSqlObject($delete);
	$results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
}

function SumoFecha_Sancionados($fixture_id, $partido)
{
	$fixture_equipo1 = $partido['0']['fixture_equipo_id1'];
	$fixture_equipo2 = $partido['0']['fixture_equipo_id2'];
	$torneo_id       = $partido['0']['fixture_torneo_id'];
	$categoria_id    = $partido['0']['fixture_categoria_id'];
	$zona_id         = $partido['0']['fixture_zona_id'];
	for ($i = 1; $i <= 2; $i++) {
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('sancionados');

		if($i == 1){
			$select->where(array('sancionados_equipo_id' => $fixture_equipo1));
		}else{
			$select->where(array('sancionados_equipo_id' => $fixture_equipo2));
		}

		$select->where->notequalTo("sancionados_sancion", 0);
		$select->where->OR;
		$select->where->equalTo("sancionados_vuelve", 1);
		$select->where->equalTo("sancionados_torneo_id", $torneo_id );
		$select->where->equalTo("sancionados_categoria_id", $categoria_id );
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$sancionados = $results->toArray();

//		print_r($sancionados);
		if(!empty($sancionados)){
			foreach ($sancionados as $key => $row) {
				$sancionados_id = $row['sancionados_id'];
				$s = $row['sancionados_sancion'];
				$s = $s + 1;

				if ($s == 1) {
					$vuelve = '0';
				}

				$sql = new Sql($this->adapter);
				$update = $sql->update();
				$update->table('sancionados');
				$update->set(array('sancionados_sancion' => $s, 'sancionados_vuelve'  => $vuelve));
				$update->where->equalTo("sancionados_id", $sancionados_id);
				$updateString = $sql->getSqlStringForSqlObject($update);
				//echo $updateString; //die;
				$this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
			}
		}
	}
}
}
