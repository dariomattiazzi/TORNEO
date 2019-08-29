<?php
namespace torneo\V1\Rest\Cierropartido;

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

class CierropartidoMapper
{
	protected $adapter;
	public function __construct(AdapterInterface $adapter)
	{
		$this->adapter = $adapter;
	}

	public function create($data)
	{
		$fixture_id = $data->fixture_id;

		// echo $fixture_id; die;

		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('fixture');
		$select->where(array('fixture_id' => $fixture_id, 'fixture_estado' => '1'));
		$selectString = $sql->getSqlStringForSqlObject($select);

		//echo $selectString; die;
		$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$partido = $results->toArray();

		//print_r($partido); die;

		//Controlo que los datos de los equipos estén cargados para el partido.
		$eg1 = $partido['0']['fixture_goles_eq1'];
		$eg2 = $partido['0']['fixture_goles_eq2'];

		//echo $eg1; echo $eg2;	die;

		if ( $eg1 == "" || $eg2 == "") {
			$e1 = $this->buscoEquipo($partido['0']['fixture_equipo_id1']);
			$e2 = $this->buscoEquipo($partido['0']['fixture_equipo_id2']);

			$json = new stdClass();
			$json->success = true;
			$json->msg = "Guardar los resultados del partido ".$e1 ." vs. ".$e2;
			return $json;
		}//FIN Controlo que los datos de los equipos estén cargados para el partido.

		try {
			//echo $partido['0']['fixture_fecha']; die;
			if($partido['0']['fixture_fecha'] > 1){
				//PONGO EN FALSE LOS QUE HABIAN VUELTO DE UNA SANCION
				$this->Borro_Sancionados_VUELVEN($fixture_id, $partido);

				//RESTO 1 FECHA DE LOS JUG SANCIONADOS DE LOS EQUIPOS DEL PARTIDO.
				$this->RestoFecha_Sancionados($fixture_id, $partido);

				//GRABO LOS JUG QUE FUERON SANCIONADOS DE LOS EQUIPOS DEL PARTIDO.
				$this->Grabo_Sancionados($fixture_id, $partido);

			}
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
		$json->msg = "Partido cerrado.";
		return $json;

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
      $eq = "";
      return $eq;
    }
  }
	function CambioEstado_Partido($fixture_id){
		$sql = new Sql($this->adapter);
		$update = $sql->update();
		$update->table('fixture');
		$update->set(array("fixture_estado" => '0'));
		$update->where->equalTo("fixture_id", $fixture_id);
		$updateString = $sql->getSqlStringForSqlObject($update);
		$this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
	}

	function Grabo_Sancionados($fixture_id, $partido)
	{
		$fixture_equipo1 = $partido['0']['fixture_equipo_id1'];
		$fixture_equipo2 = $partido['0']['fixture_equipo_id2'];
		$torneo_id       = $partido['0']['fixture_torneo_id'];
		$categoria_id    = $partido['0']['fixture_categoria_id'];
		$zona_id         = $partido['0']['fixture_zona_id'];
		for ($i = 1; $i <= 2; $i++) {
			$sql = new Sql($this->adapter);
			$select = $sql->select();
			$select->from('tarjetas');
			if($i == 1){
				$select->where(array('tarjetas_equipo_id' => $fixture_equipo1));
			}else{
				$select->where(array('tarjetas_equipo_id' => $fixture_equipo2));
			}
			$select->where->equalTo("tarjetas_torneo_id",    $torneo_id );
			$select->where->equalTo("tarjetas_categoria_id", $categoria_id );
			$selectString = $sql->getSqlStringForSqlObject($select);
			$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
			$tarjetas = $results->toArray();

			$tarXjug = array();

			//print_r($tarjetas); die;
			foreach ($tarjetas as $key => $row) {
				$tarjetas_jugador_id = $row['tarjetas_jugador_id'];
				$tarXjug[$tarjetas_jugador_id] [] = $row;
			}

			// print_r($tarXjug);
			foreach ($tarXjug as $key => $row) {
				$cant_amarillas = count($row);

				if(( $cant_amarillas % 3 ) == 0){
					$cant_fechas_suspendido = $cant_amarillas/3;

					$dataInsert = array(
						'sancionados_jugador_id'   => $row ['0']['tarjetas_jugador_id'],
						'sancionados_fixture_id'   => $fixture_id,
						'sancionados_equipo_id'    => $row ['0']['tarjetas_equipo_id'],
						'sancionados_amarilla'     => $row ['0']['tarjetas_amarilla'],
						'sancionados_torneo_id'    => $row ['0']['tarjetas_torneo_id'],
						'sancionados_categoria_id' => $row ['0']['tarjetas_categoria_id'],
						'sancionados_zona_id'      => $row ['0']['tarjetas_zona_id'],
						'sancionados_sancion'      => $cant_fechas_suspendido,
						'sancionados_vuelve'       => 0,
					);

					$sql = new Sql($this->adapter);
					$insert = $sql->insert();
					$insert->into('sancionados');
					$insert->values($dataInsert);
					$insertString = $sql->getSqlStringForSqlObject($insert);
					//echo "$insertString"; die;
					$results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);

					//Limpio la tabla TARJETAS para el jugador suspendido por acumulacion de amarillas
					$sql = new Sql($this->adapter);
					$delete = $sql->delete();
					$delete->from('tarjetas');
					$delete->where(array(
						'tarjetas_jugador_id'   => $row ['0']['tarjetas_jugador_id'],
						'tarjetas_torneo_id'    => $row ['0']['tarjetas_torneo_id'],
						'tarjetas_categoria_id' => $row ['0']['tarjetas_categoria_id']
					));
					$deleteString = $sql->getSqlStringForSqlObject($delete);
					// echo $deleteString; die;
					$results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
				}
			}
		}
	}

	function Borro_Sancionados_VUELVEN($fixture_id, $partido)
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

			$select->where->equalTo("sancionados_sancion", 0);
			$select->where->equalTo("sancionados_vuelve", '1' );
			$select->where->equalTo("sancionados_torneo_id", $torneo_id );
			$select->where->equalTo("sancionados_categoria_id", $categoria_id );
			$selectString = $sql->getSqlStringForSqlObject($select);
			// echo $selectString."\n";
			$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
			$sancionados = $results->toArray();

			// print_r($sancionados); die;
			if(!empty($sancionados)){
				foreach ($sancionados as $key => $row) {
					$sancionados_id = $row['sancionados_id'];
					$vuelve = '0';

					$sql = new Sql($this->adapter);
					$update = $sql->update();
					$update->table('sancionados');
					$update->set(array('sancionados_vuelve'  => $vuelve));
					$update->where->equalTo("sancionados_id", $sancionados_id);
					$updateString = $sql->getSqlStringForSqlObject($update);
					//echo $updateString; //die;
					$this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
				}
			}
		}
	}

	function RestoFecha_Sancionados($fixture_id, $partido)
	{
		//echo "fixture_id ".$fixture_id."\n";
		//print_r($partido);
		$fixture_equipo1 = $partido['0']['fixture_equipo_id1'];
		$fixture_equipo2 = $partido['0']['fixture_equipo_id2'];
		$torneo_id       = $partido['0']['fixture_torneo_id'];
		$categoria_id    = $partido['0']['fixture_categoria_id'];
		$zona_id         = $partido['0']['fixture_zona_id'];
		$fecha           = $partido['0']['fixture_fecha'];

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
			$select->where->notequalTo("sancionados_sancion", 999);
			$select->where->equalTo("sancionados_torneo_id", $torneo_id );
			$select->where->equalTo("sancionados_categoria_id", $categoria_id );
			$selectString = $sql->getSqlStringForSqlObject($select);
			//echo $selectString."\n";
			$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
			$sancionados = $results->toArray();

			//print_r($sancionados); echo "\n";
			if(!empty($sancionados)){
				foreach ($sancionados as $key => $row) {

					$f = $this->fecha_de_sancion($row['sancionados_fixture_id']);
					if ($fecha > $f) {
						$sancionados_id = $row['sancionados_id'];
						$s = $row['sancionados_sancion'];
						$s = $s - 1;

						if ($s == 0) {
							$vuelve = '1';
						}else {
							$vuelve = '0';
						}

						$sql = new Sql($this->adapter);
						$update = $sql->update();
						$update->table('sancionados');
						$update->set(array('sancionados_sancion' => $s, 'sancionados_vuelve'  => $vuelve));
						$update->where->equalTo("sancionados_id", $sancionados_id);
						$updateString = $sql->getSqlStringForSqlObject($update);
						//echo "\n";
						//echo $updateString; //die;
						$this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
						$s = 0;
					}
				}
			}
		}
	}

	function fecha_de_sancion($fixture_id){

		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('fixture');
		$select->where(array('fixture_id' => $fixture_id));
		$selectString = $sql->getSqlStringForSqlObject($select);
		//echo $selectString; die;
		$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$partido = $results->toArray();

		//print_r($partido); die;
		return $partido['0']['fixture_fecha'];

	}
}
