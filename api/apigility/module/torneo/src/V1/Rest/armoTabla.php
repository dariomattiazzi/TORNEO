<?php
namespace torneo\V1\Rest;

use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use torneo\V1\Rest\Posiciones\PosicionesMapper;


class armoTabla {
	public function __construct($adapter) {
		$this->adapter = $adapter;
	}

	public function getPosiciones($torneo_id, $categoria_id, $zona_id){
		// echo $torneo_id. ' - '. $categoria_id. ' - '. $zona_id; die;
		//function armoTabla($torneo_id, $categoria_id, $zona_id){
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('fixture');
		$select->where(array('fixture_torneo_id' => $torneo_id,'fixture_categoria_id' => $categoria_id,'fixture_zona_id' => $zona_id, 'fixture_estado' => "false"));
		$selectString = $sql->getSqlStringForSqlObject($select);
		$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
		$fix = $results->toArray();

		$arr = array();
		//print_r($fix[0]['fixture_fase_id']); die;
		foreach ($fix as $key => $row) {
			$eq1 = $row['fixture_equipo_id1'];
			$eq2 = $row['fixture_equipo_id2'];

			// Equipo LIBRE no se tiene en cuenta para armar la tabla de posiciones
			if($eq2 != '9999'){
				$Posmapper = new PosicionesMapper($this->adapter);
				$equipo1_nombre = $Posmapper->buscoEquipo($eq1);
				$arr[$eq1] = array('pos' => '', 'ptos' => 0,'pj' => 0,'pg' => 0,'pe' => 0,'pp' => 0,'gf' => 0,'gc' => 0,'dif' => 0,'equipo_id' => $eq1,'equipo_nombre' => $equipo1_nombre);
				$equipo2_nombre = $Posmapper->buscoEquipo($eq2);
				$arr[$eq2] = array('pos' => '', 'ptos' => 0,'pj' => 0,'pg' => 0,'pe' => 0,'pp' => 0,'gf' => 0,'gc' => 0,'dif' => 0,'equipo_id' => $eq2,'equipo_nombre' => $equipo2_nombre);
			}
		}

		foreach ($fix as $key => $row) {
			$eq1       = $row['fixture_equipo_id1'];
			$eq2       = $row['fixture_equipo_id2'];
			$goles_eq1 = $row['fixture_goles_eq1'];
			$goles_eq2 = $row['fixture_goles_eq2'];


			if($eq1 != '9999' && $eq2 != '9999'){
				if($goles_eq1 > $goles_eq2){
					//ARMO GANADOR
					$arr[$eq1]['ptos']  = $arr[$eq1]['ptos'] + 3;
					$arr[$eq1]['pj']    = $arr[$eq1]['pj'] + 1;
					$arr[$eq1]['pg']    = $arr[$eq1]['pg'] + 1;
					$arr[$eq1]['gf']    = $arr[$eq1]['gf'] + $goles_eq1;
					$arr[$eq1]['gc']    = $arr[$eq1]['gc'] + $goles_eq2;
					$arr[$eq1]['dif']   = $arr[$eq1]['gf'] - $arr[$eq1]['gc'];
					//ARMO PERDEDOR
					$arr[$eq2]['pj']  = $arr[$eq2]['pj'] + 1;
					$arr[$eq2]['pp']  = $arr[$eq2]['pp'] + 1;
					$arr[$eq2]['gf']  = $arr[$eq2]['gf'] + $goles_eq2;
					$arr[$eq2]['gc']  = $arr[$eq2]['gc'] + $goles_eq1;
					$arr[$eq2]['dif'] = $arr[$eq2]['gf'] - $arr[$eq2]['gc'];
				}elseif($goles_eq1 < $goles_eq2){
					//ARMO GANADOR
					$arr[$eq2]['ptos']  = $arr[$eq2]['ptos'] + 3;
					$arr[$eq2]['pj']    = $arr[$eq2]['pj'] + 1;
					$arr[$eq2]['pg']    = $arr[$eq2]['pg'] + 1;
					$arr[$eq2]['gf']    = $arr[$eq2]['gf'] + $goles_eq2;
					$arr[$eq2]['gc']    = $arr[$eq2]['gc'] + $goles_eq1;
					$arr[$eq2]['dif']   = $arr[$eq2]['gf'] - $arr[$eq2]['gc'];

					//ARMO PERDEDOR
					$arr[$eq1]['pj']    = $arr[$eq1]['pj'] + 1;
					$arr[$eq1]['pp']    = $arr[$eq1]['pp'] + 1;
					$arr[$eq1]['gf']    = $arr[$eq1]['gf'] + $goles_eq1;
					$arr[$eq1]['gc']    = $arr[$eq1]['gc'] + $goles_eq2;
					$arr[$eq1]['dif']   = $arr[$eq1]['gf'] - $arr[$eq1]['gc'];
				}else{
					$arr[$eq1]['ptos']  = $arr[$eq1]['ptos'] + 1;
					$arr[$eq2]['ptos']  = $arr[$eq2]['ptos'] + 1;

					$arr[$eq1]['pe']    = $arr[$eq1]['pe'] + 1;
					$arr[$eq2]['pe']    = $arr[$eq2]['pe'] + 1;

					$arr[$eq1]['pj']    = $arr[$eq1]['pj'] + 1;
					$arr[$eq2]['pj']    = $arr[$eq2]['pj'] + 1;

					$arr[$eq1]['gf']    = $arr[$eq1]['gf'] + $goles_eq1;
					$arr[$eq2]['gf']    = $arr[$eq2]['gf'] + $goles_eq2;

					$arr[$eq1]['gc']    = $arr[$eq1]['gc'] + $goles_eq2;
					$arr[$eq2]['gc']    = $arr[$eq2]['gc'] + $goles_eq1;

					$arr[$eq1]['dif']   = $arr[$eq1]['gf'] - $arr[$eq1]['gc'];
					$arr[$eq2]['dif']   = $arr[$eq2]['gf'] - $arr[$eq2]['gc'];
				}
			}
			// print_r($arr); die;

			//quitar este codigoooooooooooooooooooooooooooooooooooooooooooooo
			// if ($eq1 == 13) {
			// 	$arr[$eq1]['ptos']  = 0;
			// 	$arr[$eq1]['equipo_nombre']  = 'ISOTOPOS F.C. <b style="color:red"> Descendido<b>';
			// }
			//
			// if ($eq2 == 13) {
			// 	$arr[$eq2]['ptos']  = 0;
			// 	$arr[$eq2]['equipo_nombre']  = 'ISOTOPOS F.C. <b style="color:red"> Descendido<b>';
			// }
			//quitar este codigoooooooooooooooooooooooooooooooooooooooooooooo
		}
		//
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from('quitapuntos');
		$select->join(array("equipo"  => "equipo"),'quitapuntos.quitapuntos_equipo_id = equipo.equipo_id',array('*'),'inner');
		$select->where(array(
		'quitapuntos_torneo_id' => $torneo_id,
		'quitapuntos_categoria_id' => $categoria_id,
		'quitapuntos_zona_id' => $zona_id
	));
	$select->columns(array('restarPuntos' => new \Zend\Db\Sql\Expression('SUM(quitapuntos_cant)')));
	$select->group('quitapuntos_equipo_id');
	$select->order('restarPuntos DESC');
	$selectString = $sql->getSqlStringForSqlObject($select);
	//    echo $selectString; die;
	$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
	$restarPtos = $results->toArray();

	// print_r($restarPtos); die;
	//print_r($arr); die;
	if (count($restarPtos)>=1) {
		foreach ($arr as $key => $row) {
			if(array_search($row['equipo_id'], array_column($restarPtos, 'equipo_id')) !== false) {
				$clave = array_search($row['equipo_id'], array_column($restarPtos, 'equipo_id'));
				//print_r($clave); ///die;

				// echo "PUNTOS: ".$row['ptos']."\n";
				// echo "Restar: ".$restarPtos[$clave]['restarPuntos']."\n";

				$puntos = $row['ptos'] + $restarPtos[$clave]['restarPuntos'];

				//print_r($arr[$row['equipo_id']['ptos']]); die;

				// echo $arr[$row['equipo_id']]['ptos']; die;
				$arr[$row['equipo_id']]['ptos'] = $puntos;

				$arr[$row['equipo_id']]['equipo_nombre'] = "* ".$arr[$row['equipo_id']]['equipo_nombre'];
			}
		}
	}
	//print_r($arr); die;

	foreach ($arr as $key => $row) {
		$aux_ptos[$key] = $row['ptos'];
		$aux_dif[$key]  = $row['dif'];
		$aux_gf[$key]  = $row['gf'];
	}
//	print_r($arr); die;

	array_multisort(@$aux_ptos, SORT_DESC, @$aux_dif, SORT_DESC, $aux_gf, SORT_DESC, $arr);

	foreach ($arr as $clave => $fila) {
		$arr[$clave]['pos'] = $clave + 1 ;
	}
	return $arr;
}
}
?>
