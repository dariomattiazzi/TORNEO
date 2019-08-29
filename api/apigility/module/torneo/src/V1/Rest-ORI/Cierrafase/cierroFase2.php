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


// ARMAR LA FASE 2 (16vos DE FINAL)

class cierroFase3 {
	public function __construct($adapter) {
		$this->adapter = $adapter;
	}

	public function getCierroFase3($torneo_id, $categoria_id, $zona_id, $fase_id){
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
		$select->where(array('zona_categoria_id' => $categoria_id, 'zona_mostrar_posicion' => true));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results   = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$arr_zonas = $results->toArray();

		foreach ($arr_zonas as $key => $row) {
			$zona = $row['zona_id'];
			$class = new armoTabla($this->adapter);
			$arr = $class->getPosiciones($torneo_id, $categoria_id, $zona);

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
				$aux_gf[$key]  = $row['gf'];
			}
			array_multisort($aux_ptos, SORT_DESC, $aux_dif, SORT_DESC, $aux_gf, SORT_DESC, $arr);
			foreach ($arr as $key => $row) {
				$arr_equipo_categoria[] = $row;
			}
		}

		$clasif_ZonaCampeonato = $categoria['0']['categoria_cant_a_copacampeonato'];
		$clasif_CopaRevancha   = $categoria['0']['categoria_cant_a_coparevancha'];

		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('fixture');
		$select->where(array('fixture_zona_id' => $zona_id, 'fixture_fase_id' => $fase_id, 'fixture_estado' => false));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results   = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$arr_equipos = $results->toArray();

		//		print_r($arr_equipos); //die;
		$bandera = 0;
		$arr_ganadoresOctavos = array();
		foreach ($arr_equipos as $key => $row) {
			$eq1       = $row['fixture_equipo_id1'];
			$eq2       = $row['fixture_equipo_id2'];

			$sql = new Sql($this->adapter);
			$select = $sql->select();
			$select->from('fixture');
			$select->where(array('fixture_zona_id' => $zona_id, 'fixture_fase_id' => $fase_id, 'fixture_estado' => false, 'fixture_fase_id' => $fase_id, 'fixture_equipo_id1' => $eq1,'fixture_equipo_id2' => $eq2));
			$selectString = $sql->getSqlStringForSqlObject($select);
			$results   = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
			$arr_resul = $results->toArray();

			//			print_r($arr_resul); die;

			$goles_eq1 = 0;
			$goles_eq2 = 0;

			$penales_eq1 = 0;
			$penales_eq2 = 0;

			foreach ($arr_resul as $k => $r) {
				$goles_eq1 = $goles_eq1 + $r['fixture_goles_eq1'];
				$goles_eq2 = $goles_eq2 + $r['fixture_goles_eq2'];

				$penales_eq1 = $penales_eq1 + $r['fixture_penales_eq1'];
				$penales_eq2 = $penales_eq2 + $r['fixture_penales_eq2'];
			}
			echo " GOLES EQ1: " .$goles_eq1 ." - GOLES EQ2: " .$goles_eq2 ."\n";
			echo " PENALES EQ1: " .$penales_eq1 ." - PENALES EQ2: " .$penales_eq2 ."\n"; //die;



			if($eq2 == '9999'){
				$arr_ganadoresOctavos[$eq1] = array('equipo_id' => $eq1);
			}else{
				if($goles_eq1 > $goles_eq2){
					$arr_ganadoresOctavos[$eq1] = array('equipo_id' => $eq1);
				}elseif($goles_eq2 > $goles_eq1){
					$arr_ganadoresOctavos[$eq2] = array('equipo_id' => $eq2);
				}elseif($goles_eq1 == $goles_eq2){
					//					$penales_eq1 = $row['fixture_penales_eq1'];
					//					$penales_eq2 = $row['fixture_penales_eq2'];
					if($penales_eq1 > $penales_eq2){
						$arr_ganadoresOctavos[$eq1] = array('equipo_id' => $eq1);
					}else{
						$arr_ganadoresOctavos[$eq2] = array('equipo_id' => $eq2);
					}
				}
			}
			//		print_r($arr_ganadoresOctavos); die;

		}

		//		print_r($arr_ganadoresOctavos); die;

		foreach ($arr_equipo_categoria as $clave => $fila) {
			$arr_equipo_categoria[$clave]['pos-gral'] = $clave + 1 ;
		}

		foreach ($arr_ganadoresOctavos as $clave => $fila) {
			$equipoganador = $fila['equipo_id'];
			foreach ($arr_equipo_categoria as $key => $row) {
				if($equipoganador == $row['equipo_id']){
					$arr_cuartos[] = $row;
				}
			}
		}

		foreach ($arr_cuartos as $clave => $fila) {
			$posgral[$clave] = $fila['pos-gral'];
		}

		array_multisort($posgral, SORT_ASC, $arr_cuartos);

		//		print_r($arr_cuartos); die;

		$fase = $this->getfase($categoria_id, $fase_id, $clasif_ZonaCampeonato);

		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('fixture');
		$select->where(array('fixture_zona_id' => $zona_id, 'fixture_fase_id' => "3" ));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results   = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$Zc = $results->toArray();

		if (!empty($Zc)) {
			//			echo "ya existe la FASE 3 para la zona Campeonato";
			$id = $Zc['0']['zona_id'];

			$sql = new Sql($this->adapter);
			$delete = $sql->delete();
			$delete->from('fixture');
			$delete->where(array('fixture_zona_id' => $id, 'fixture_fase_id' => "3"));
			$deleteString = $sql->getSqlStringForSqlObject($delete);
			$results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
		}

		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('fase');
		$select->where(array('fase_id' => $fase));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results   = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$f = $results->toArray();
		$fase_cant_partido = $f['0']['fase_cant_partido'];

		for ($i = 0; $i <= 3; $i++) {
			$fixture_torneo_id =  $torneo_id;
			$fixture_categoria_id =  $categoria_id;
			$fixture_zona_id = $zona_id;
			$fixture_fecha = '1';
			$fixture_equipo_id1 = $arr_cuartos[$i]['equipo_id'];
			$fixture_equipo_id2 = $arr_cuartos[7-$i]['equipo_id'];
			$fixture_fase_id = $fase;
			$fixture_cancha_id = '999';
			$fixture_turno_id = '999';
			$fixture_goles_eq1 = null;
			$fixture_goles_eq2 = null;
			$fixture_estado = '1';

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
