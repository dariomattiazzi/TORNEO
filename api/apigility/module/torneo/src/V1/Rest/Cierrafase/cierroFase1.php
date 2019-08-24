<?php
namespace torneo\V1\Rest\Cierrafase;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use stdClass;
use torneo\V1\Rest\Posiciones\PosicionesMapper;
use torneo\V1\Rest\Zona\ZonaMapper;
use torneo\V1\Rest\armoTabla;


class cierroFase1 {
	public function __construct($adapter) {
		$this->adapter = $adapter;
	}

	public function getCierroFase1($torneo_id, $categoria_id, $zona_id, $fase_id){

		$zonamapper = new ZonaMapper($this->adapter);

		$cantidaEquipoXCat = 0;
		$arr_general = array();
		$arr_equipo_categoria = array();

		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('categoria');
		$select->where(array('categoria_id' => $categoria_id));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results   = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$categoria = $results->toArray();

		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('zona');
		$select->where(array('zona_categoria_id' => $categoria_id));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results   = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$arr_zonas = $results->toArray();

		// cuento la cantidad de zonas de la categoria
		$cantidad_zonas = $arr_zonas;

		// SI $cantidad_zonas = 2 armo los cruces de octavos 1a-8b / 2a-7b /..../8a-1b /
		/*if ($cantidad_zonas = 2) {
			//CIERRO FASE 1 CATEGORIA C
			$class = new cierroFase1CATC($this->adapter);
			$arr = $class->getCierroFase1CATC($torneo_id, $categoria_id, $zona_id, $fase_id);
			return $arr;
		}*/
		//

		//echo "123123123123123123"; die;
		foreach ($arr_zonas as $key => $row) {
			$zona_id = $row['zona_id'];
			//echo $zona_id; die;
			$class = new armoTabla($this->adapter);
			$arr = $class->getPosiciones($torneo_id, $categoria_id, $zona_id);

			foreach ($arr as $clave => $fila) {
				$arr_general[$clave][] = $fila;
			}
			$cantidaEquipoXCat += $row['zona_cantidad_equipos'];
		}

		// count($arr_general) CANTIDAD DE EQUIPOS POR CATEGORIA
		for ($i = 0; $i <= count($arr_general)-1; $i++) {
			$arr = $arr_general[$i];
			foreach ($arr as $key => $row) {
				$aux_ptos[$key] = $row['ptos'];
				$aux_dif[$key]  = $row['dif'];
				$aux_gf[$key]   = $row['gf'];
			}
			array_multisort($aux_ptos, SORT_DESC, $aux_dif, SORT_DESC, $aux_gf, SORT_DESC, $arr);

			foreach ($arr as $key => $row) {
				$arr_equipo_categoria[] = $row;
			}
		}

		$clasif_ZonaCampeonato = $categoria['0']['categoria_cant_a_copacampeonato'];
		$clasif_CopaRevancha   = $categoria['0']['categoria_cant_a_coparevancha'];

		//		print_r($arr_equipo_categoria);

		$arr_CopaCampeonato = array_slice($arr_equipo_categoria, 0, $clasif_ZonaCampeonato);

		$arr_CopaRevancha   = array_slice($arr_equipo_categoria, $clasif_ZonaCampeonato, $clasif_CopaRevancha);
		//		print_r($arr_CopaRevancha); die;


		$fase = $this->getfase($categoria_id, $fase_id, $clasif_ZonaCampeonato);


		// CREO LA NUEVA ZONA (Zona Campeonato) para la categoria
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('zona');
		$select->where(array('zona_categoria_id' => $categoria_id, 'zona_descri' => "Zona Campeonato" ));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results   = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$Zc = $results->toArray();

		if (!empty($Zc)) { //ZONA CAMPEONATA YA ESTÁ CREADA -- Se borra!
			$id = $Zc['0']['zona_id'];

			$zonaCamp = $zonamapper->delete($id);

			$sql = new Sql($this->adapter);
			$delete = $sql->delete();
			$delete->from('fixture');
			$delete->where(array('fixture_zona_id' => $id));
			$deleteString = $sql->getSqlStringForSqlObject($delete);
			$results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
		}

		$datos = new stdClass();
		$datos->zona_descri           = "Zona Campeonato";
		$datos->zona_categoria_id     = $categoria_id;
		$datos->zona_cantidad_equipos = $clasif_ZonaCampeonato; //$clasif_ZonaCampeonato valor INGRESEDA EN la tabla categoria
		$datos->zona_mostrar_posicion = "0";
		$zonaCamp = $zonamapper->create($datos);

		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('fase');
		$select->where(array('fase_id' => $fase));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results   = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$f = $results->toArray();
		$fase_cant_partido = $f['0']['fase_cant_partido'];

		for ($i = 0; $i <= $clasif_ZonaCampeonato/2-1; $i++) {
			$fixture_torneo_id    = $torneo_id;
			$fixture_categoria_id = $categoria_id;
			$fixture_zona_id      = $zonaCamp->id;
			$fixture_fecha        = '1';
			$fixture_equipo_id1   = $arr_CopaCampeonato[$i]['equipo_id'];
			$fixture_equipo_id2   = $arr_CopaCampeonato[$clasif_ZonaCampeonato-1-$i]['equipo_id'];
			$fixture_fase_id      = $fase;
			$fixture_cancha_id    = '999';
			$fixture_turno_id     = '999';
			$fixture_goles_eq1    = null;
			$fixture_goles_eq2    = null;
			$fixture_estado       = '1';

			for ($j = 1; $j <= $fase_cant_partido; $j++) {
				$dataInsert = array(
					"fixture_torneo_id"     => $fixture_torneo_id,
					"fixture_categoria_id"  => $fixture_categoria_id,
					"fixture_zona_id"       => $fixture_zona_id,
					"fixture_fecha"         => $j,
					"fixture_equipo_id1"    => $fixture_equipo_id1,
					"fixture_equipo_id2"    => $fixture_equipo_id2,
					"fixture_fase_id"       => $fixture_fase_id,
					"fixture_cancha_id"     => $fixture_cancha_id,
					"fixture_turno_id"      => $fixture_turno_id,
					"fixture_goles_eq1"     => $fixture_goles_eq1,
					"fixture_goles_eq2"     => $fixture_goles_eq2,
					"fixture_estado"        => $fixture_estado
				);
				//print_r($dataInsert);

				try {
					$sql = new Sql($this->adapter);
					$insert = $sql->insert();
					$insert->into('fixture');
					$insert->values($dataInsert);
					$insertString = $sql->getSqlStringForSqlObject($insert);
					//echo $insertString;
					$results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
				} catch (Exception $e) {
					$json = new stdClass();
					$json->success = false;
					$json->msg = "No se pudo ingresar el registro.";
					return $json;
				}
			}

		}

		//--------------------------------------------------------------
		// echo "COPA REVANCHA"."\n";
		if ($categoria['0']['categoria_juega_coparevancha'] == '1'){
			$sql = new Sql($this->adapter);
			$select = $sql->select();
			$select->from('zona');
			$select->where(array('zona_categoria_id' => $categoria_id, 'zona_descri' => "Copa Revancha" ));
			$selectString = $sql->getSqlStringForSqlObject($select);
			$results   = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
			$Zr = $results->toArray();

			if (!empty($Zr)) { // SI ESTÁ CREADA LA COPA REVANCHA - Se borra.
				$id = $Zr['0']['zona_id'];

				$zonaCamp = $zonamapper->delete($id);

				$sql = new Sql($this->adapter);
				$delete = $sql->delete();
				$delete->from('fixture');
				$delete->where(array('fixture_zona_id' => $id));
				$deleteString = $sql->getSqlStringForSqlObject($delete);
				$results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
			}

			$equipos_CopaRevancha = count($arr_CopaRevancha);

//			echo $equipos_CopaRevancha ." - " .$clasif_CopaRevancha; 

			if($equipos_CopaRevancha < $clasif_CopaRevancha){
				//SI LA CANTIDAD DE EQUIPOS QUE QUEDAN PARA JUGAR LA COPA REVANCHA ES MENOR LA LA INGRESADA EN LA TABLA DE CATEGORIA, LOS PRIMEROS "X" QUEDAN LIBRE.
				$stop = $clasif_CopaRevancha - $equipos_CopaRevancha;
				for ($i = 1; $i <= $stop; $i++) {
					$indice = $i + $equipos_CopaRevancha;
					$arr[$indice] = array('ptos' => 0,'pj' => 0,'pg' => 0,'pe' => 0,'pp' => 0,'gf' => 0,'gc' => 0,'dif' => 0,'equipo_id' => 9999,'equipo_nombre' => 'LIBRE');
					array_push($arr_CopaRevancha, $arr[$indice]);
				}
			}

//			print_r($arr_CopaRevancha); die;

			//CREO LA NUEVA ZONA (Copaa Revancha) para la categoria
			$datos = new stdClass();
			$datos->zona_descri           = "Copa Revancha";
			$datos->zona_categoria_id     = $categoria_id;
			$datos->zona_cantidad_equipos = $clasif_CopaRevancha; //$clasif_CopaRevancha valor INGRESEDA EN la tabla categoria
			$datos->zona_mostrar_posicion = "0";
			$zonaRev = $zonamapper->create($datos);

			$fase = $this->getfase($categoria_id, $fase_id, $clasif_CopaRevancha);

			for ($i = 0; $i <= $clasif_CopaRevancha/2-1; $i++) {
				$fixture_torneo_id     =  $torneo_id;
				$fixture_categoria_id  =  $categoria_id;
				$fixture_zona_id       = $zonaRev->id;
				$fixture_fecha         = '1';
				$fixture_equipo_id1    = $arr_CopaRevancha[$i]['equipo_id'];
				$fixture_equipo_id2    = $arr_CopaRevancha[$clasif_CopaRevancha-1-$i]['equipo_id'];
				$fixture_fase_id       = $fase;
				$fixture_cancha_id     = '999';
				$fixture_turno_id      = '999';
				$fixture_goles_eq1     = null;
				$fixture_goles_eq2     = null;
				$fixture_estado        = '1';

				for ($j = 1; $j <= $fase_cant_partido; $j++) {
					$dataInsert = array(
						"fixture_torneo_id"     => $fixture_torneo_id,
						"fixture_categoria_id"  => $fixture_categoria_id,
						"fixture_zona_id"       => $fixture_zona_id,
						"fixture_fecha"         => $j,
						"fixture_equipo_id1"    => $fixture_equipo_id1,
						"fixture_equipo_id2"    => $fixture_equipo_id2,
						"fixture_fase_id"       => $fixture_fase_id,
						"fixture_cancha_id"     => $fixture_cancha_id,
						"fixture_turno_id"      => $fixture_turno_id,
						"fixture_goles_eq1"     => $fixture_goles_eq1,
						"fixture_goles_eq2"     => $fixture_goles_eq2,
						"fixture_estado"        => $fixture_estado
					);

					try {
						$sql = new Sql($this->adapter);
						$insert = $sql->insert();
						$insert->into('fixture');
						$insert->values($dataInsert);
						$insertString = $sql->getSqlStringForSqlObject($insert);
						$results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
					} catch (Exception $e) {
						$json = new stdClass();
						$json->success = false;
						$json->msg = "No se pudo ingresar el registro.";
						return $json;
					}
				}
			}
		}
		$json = new stdClass();
		$json->success = true;
		$json->msg = "Fase cerrada correctamente.";
		return $json;
	}

	function getfase($categoria_id, $fase, $cantequipos){
		if ($fase == '1') {
			if ($cantequipos == '32') {
				$f = 2;
				return $f;
			}
			if ($cantequipos == '16') {
				$f = 3;
				return $f;
			}
			if ($cantequipos == '8') {
				$f = 4;
				return $f;
			}
			if ($cantequipos == '4') {
				$f = 5;
				return $f;
			}
		}else {
			$f = $fase + 1;
			return $f;
		}
	}
}
?>
