<?php
namespace torneo\V1\Rest\Reporte;

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

class reporteMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function fetchAll()
  {
  }

  public function fetchOne($id)
  {
    echo "fetchone"; die;
  }

  public function create($data)
  {
    $reporte      = $data->reporte;

    if ($reporte == 'sancionados') {
      return $this->sancionados($data);
    }
    if ($reporte == 'horarios') {
      if ($data->fecha_id == 0) {
        return $this->fixturecompleto($data);
      }else {
        return $this->horarios($data);
      }
    }
    if ($reporte == 'resumenFecha') {
      return $this->resumenFecha($data);
    }
  }

  public function sancionados($data)
  {
    $torneo_id    = $data->torneo_id;
    $categoria_id = $data->categoria_id;
    $zona_id      = $data->zona_id;
    $reporte      = $data->reporte;

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('fixture');
    $select->join(array("sancionados" => "sancionados"),'fixture.fixture_id = sancionados.sancionados_fixture_id',array('*'),'inner');
    $select->join(array("jugador"     => "jugador"),'sancionados.sancionados_jugador_id = jugador.jugador_id',array('*'),'inner');
    $select->join(array("equipo"      => "equipo"),'sancionados.sancionados_equipo_id = equipo.equipo_id',array('*'),'inner');
    $select->where(array('fixture_torneo_id' => $torneo_id, 'sancionados_sancion > 0'));
    $select->where->isNotNull("sancionados_sancion");
    $select->order('fixture_categoria_id, equipo.equipo_nombre, jugador.jugador_apellido, jugador.jugador_nombre, sancionados.sancionados_sancion');
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $sancionados = $results->toArray();

  foreach ($sancionados as $key => $row) {
    if ($row['sancionados_sancion'] == '999') {
      $sancion = 'PROVISORIA';
    }else {
      $sancion = $row['sancionados_sancion'];
    }
    $arr [] = array(
      'torneo'              => $this->nombre_torneo($row['fixture_torneo_id']),
      'categoria'           => $this->nombre_categoria($row['fixture_categoria_id']),
      'text'                => $row['jugador_nombre'] ." " .$row['jugador_apellido'],
      'sancion'             => $row['sancionados_sancion'],
      "sancionados_vuelve"  =>  $row ['sancionados_vuelve'],
      "equipo_nombre"       =>  $row ['equipo_nombre'],
    );
  }

  //print_r($sancionados); die;
  if (!empty($arr)) {
    $json_data = json_encode($arr);
    $j = base64_encode($json_data);

    $dataInsert = array(
      "reporte_json" => $json_data,
      "reporte_date" => $hoy,
    );
    $sql = new Sql($this->adapter);
    $insert = $sql->insert();
    $insert->into('reporte');
    $insert->values($dataInsert);
    $insertString = $sql->getSqlStringForSqlObject($insert);
    $results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
    $nrorep = $results->getGeneratedValue();
    $j = $nrorep;

    $url = 'http://dario-casa.sytes.net/api/tcpdf/gclpdf/'.$reporte.'.php?param='.$j;

    $json = new stdClass();
    $json->success = true;
    $json->url = "$url";
    return $json;

  }else {
    $json = new stdClass();
    $json->success = false;
    $json->url = [];
    $json->msg = "No hay registros para mostrar.";
    return $json;
  }
}

public function fixturecompleto($data)
{
  // print_r($data); die;
  $torneo_id    = $data->torneo_id;
  $categoria_id = $data->categoria_id;
  $zona_id      = $data->zona_id;
  //$fecha        = $data->fecha_id;
  $reporte      = $data->reporte;

  $sql = new Sql($this->adapter);
  $select = $sql->select();
  $select->from('fixture');
  $select->where(array('fixture_torneo_id' => $torneo_id,
  'fixture_categoria_id' => $categoria_id,
  'fixture_zona_id' => $zona_id,
  //'fixture_fecha' => $fecha,
));
$select->order(array('fixture_torneo_id', 'fixture_categoria_id', 'fixture_zona_id', 'fixture_fecha', 'fixture_fase_id DESC', 'fixture_cancha_id', 'fixture_turno_id'));
$selectString = $sql->getSqlStringForSqlObject($select);
// echo $selectString; die;
$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
$partidos = $results->toArray();

// print_r($partidos); die;
foreach ($partidos as $key => $row) {
  $arr['Fecha '.$row ['fixture_fecha']][$this->nombre_categoria($row ['fixture_categoria_id'])][$this->nombre_zona($row ['fixture_zona_id'])][] = array(
    'turno_descri'         => $this->nombre_turno($row ['fixture_turno_id']),
    'fecha'                => $row ['fixture_fecha'],
    'cancha_descri'        => $this->nombre_cancha($row ['fixture_cancha_id']),
    'equipo1'              => $this->nombre_equipo($row ['fixture_equipo_id1']),
    'equipo2'              => $this->nombre_equipo($row ['fixture_equipo_id2'])
  );
}

if(!empty($arr)){
  $json_data = json_encode($arr);
  $hoy = date('Y-m-d H:i:s');
  $antes_ayer = strtotime ( '-2 day' , strtotime ($hoy)) ;
  $antes_ayer = date ( 'Y-m-d H:i:s' , $antes_ayer );

  // BORRO LOS REPORTES GENERADOS HACE MAS DE DOS DIAS
  $sql = new Sql($this->adapter);
  $delete = $sql->delete();
  $delete->from('reporte');
  $delete->where("reporte_date < '$antes_ayer'");
  $deleteString = $sql->getSqlStringForSqlObject($delete);
  $results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);

  $dataInsert = array(
    "reporte_json" => $json_data,
    "reporte_date" => $hoy,
  );
  $sql = new Sql($this->adapter);
  $insert = $sql->insert();
  $insert->into('reporte');
  $insert->values($dataInsert);
  $insertString = $sql->getSqlStringForSqlObject($insert);
  $results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
  $nrorep = $results->getGeneratedValue();
  $j = base64_encode($nrorep);
  $url = 'http://dario-casa.sytes.net/api/tcpdf/gclpdf/fixturecompleto.php?param='.$j;
  $json = new stdClass();
  $json->success = true;
  $json->url = "$url";
  return $json;
}else{
  $json = new stdClass();
  $json->success = true;
  $json->msg = "La zona no tiene Fixture generado";
  return $json;
}
}

public function horarios($data)
{
  // print_r($data); die;
  $torneo_id    = $data->torneo_id;
  $categoria_id = $data->categoria_id;
  $zona_id      = $data->zona_id;
  $fecha        = $data->fecha_id;
  $reporte      = $data->reporte;

  $sql = new Sql($this->adapter);
  $select = $sql->select();
  $select->from('fixture');
  $select->where(array('fixture_torneo_id' => $torneo_id,
  'fixture_categoria_id' => $categoria_id,
  'fixture_zona_id' => $zona_id,
  'fixture_fecha' => $fecha,
));
$select->order(array('fixture_torneo_id', 'fixture_categoria_id', 'fixture_zona_id', 'fixture_fecha', 'fixture_fase_id DESC', 'fixture_cancha_id', 'fixture_turno_id'));
$selectString = $sql->getSqlStringForSqlObject($select);
// echo $selectString; die;
$results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
$partidos = $results->toArray();

// print_r($partidos); die;
foreach ($partidos as $key => $row) {
  $arr[$this->nombre_cancha($row ['fixture_cancha_id'])][$this->nombre_categoria($row ['fixture_categoria_id'])][$this->nombre_zona($row ['fixture_zona_id'])][] = array(
    'turno_descri'         => $this->nombre_turno($row ['fixture_turno_id']),
    'cancha_id'            => $row ['fixture_cancha_id'],
    'cancha_descri'        => $this->nombre_cancha($row ['fixture_cancha_id']),
    'equipo1'              => $this->nombre_equipo($row ['fixture_equipo_id1']),
    'equipo2'              => $this->nombre_equipo($row ['fixture_equipo_id2'])
  );
}

if(!empty($arr)){
  $json_data = json_encode($arr);
  $j = base64_encode($json_data);
  $url = 'http://dario-casa.sytes.net/api/tcpdf/gclpdf/'.$reporte.'.php?param='.$j;
  $json = new stdClass();
  $json->success = true;
  $json->url = "$url";
  return $json;
}else{
  $json = new stdClass();
  $json->success = true;
  $json->msg = "La zona no tiene Fixture generado";
  return $json;
}
}

function nombre_zona($zona_id){
  $sql = new Sql($this->adapter);
  $select = $sql->select();
  $select->from('zona');
  $select->where(array('zona_id' => $zona_id));
  $selectString = $sql->getSqlStringForSqlObject($select);
  $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
  $zona = $results->toArray();

  if (!empty($zona['0']['zona_descri'])) {
    $zona = $zona['0']['zona_descri'];
    return $zona;
  }else {
    $zona = "A CONFIRMAR";
    return $zona;
  }
}

function nombre_cancha($cancha_id){
  $sql = new Sql($this->adapter);
  $select = $sql->select();
  $select->from('cancha');
  $select->where(array('cancha_id' => $cancha_id));
  $selectString = $sql->getSqlStringForSqlObject($select);
  $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
  $cancha = $results->toArray();

  if (!empty($cancha[0]['cancha_descri'])) {
    $cancha = $cancha[0]['cancha_descri'];
    return $cancha;
  }else {
    $cancha = "A CONFIRMAR";
    return $cancha;
  }
}

function nombre_torneo($torneo_id){
  $sql = new Sql($this->adapter);
  $select = $sql->select();
  $select->from('torneo');
  $select->where(array('torneo_id' => $torneo_id));
  $selectString = $sql->getSqlStringForSqlObject($select);
  $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
  $torneo = $results->toArray();

  if (!empty($torneo[0]['torneo_descri'])) {
    $torneo = $torneo[0]['torneo_descri'];
    return $torneo;
  }else {
    $torneo = "A CONFIRMAR";
    return $torneo;
  }
}

function nombre_categoria($categoria_id){
  $sql = new Sql($this->adapter);
  $select = $sql->select();
  $select->from('categoria');
  $select->where(array('categoria_id' => $categoria_id));
  $selectString = $sql->getSqlStringForSqlObject($select);
  $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
  $categoria = $results->toArray();

  if (!empty($categoria[0]['categoria_descri'])) {
    $categoria = $categoria[0]['categoria_descri'];
    return $categoria;
  }else {
    $categoria = "A CONFIRMAR";
    return $categoria;
  }
}

function nombre_turno($turno_id){
  $sql = new Sql($this->adapter);
  $select = $sql->select();
  $select->from('turno');
  $select->where(array('turno_id' => $turno_id));
  $selectString = $sql->getSqlStringForSqlObject($select);
  $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
  $turno = $results->toArray();

  if (!empty($turno[0]['turno_descri'])) {
    $turno = $turno[0]['turno_descri'];
    return $turno;
  }else {
    $turno = "A CONFIRMAR";
    return $turno;
  }
}

function nombre_equipo($equipo_id){
  $sql = new Sql($this->adapter);
  $select = $sql->select();
  $select->from('equipo');
  $select->where(array('equipo_id' => $equipo_id));
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

  public function resumenFecha($data)
  {
    $torneo_id    = $data->torneo_id;
    $categoria_id = $data->categoria_id;
    $zona_id      = $data->zona_id;
    $fecha_id     = $data->fecha_id;
    $reporte      = $data->reporte;

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('fixture');
    $select->where(array('fixture_torneo_id'    => $torneo_id,
                         'fixture_categoria_id' => $categoria_id,
    				             //'fixture_zona_id'      => $zona_id,
    				             'fixture_fecha'        => $fecha_id  ));
  $select->order('fixture_zona_id, fixture.fixture_turno_id');
  $selectString = $sql->getSqlStringForSqlObject($select);
  // echo $selectString; die;
  $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
  $partidos = $results->toArray();

  foreach ($partidos as $key => $row) {
    $arr [] = array(
      'torneo'              => $this->nombre_torneo($row['fixture_torneo_id']),
      'categoria'           => $this->nombre_categoria($row['fixture_categoria_id']),
      'zona'                => $this->nombre_zona($row['fixture_zona_id']),
      'equipo1'             => $this->nombre_equipo($row ['fixture_equipo_id1']),
      'goles_eq1'           => $row ['fixture_goles_eq1'],
	    'goleadores_eq1'      => $this->busco_goleadores($row ['fixture_id'], $row ['fixture_equipo_id1']),
	  'amonestados_eq1'     => $this->busco_amonestados($row ['fixture_id'], $row ['fixture_equipo_id1']),
	  'expulsados_eq1'      => $this->busco_expulsados($row ['fixture_id'], $row ['fixture_equipo_id1']),

      'equipo2'             => $this->nombre_equipo($row ['fixture_equipo_id2']),
      'goles_eq2'           => $row ['fixture_goles_eq2'],
	  'goleadores_eq2'      => $this->busco_goleadores($row ['fixture_id'], $row ['fixture_equipo_id2']),
	  'amonestados_eq2'     => $this->busco_amonestados($row ['fixture_id'], $row ['fixture_equipo_id2']),
	  'expulsados_eq2'      => $this->busco_expulsados($row ['fixture_id'], $row ['fixture_equipo_id2']),
    );
  }

  //print_r($arr); die;

if(!empty($arr)){
  $json_data = json_encode($arr);
  $hoy = date('Y-m-d H:i:s');
  $antes_ayer = strtotime ( '-2 day' , strtotime ($hoy)) ;
  $antes_ayer = date ( 'Y-m-d H:i:s' , $antes_ayer );

  // BORRO LOS REPORTES GENERADOS HACE MAS DE DOS DIAS
  $sql = new Sql($this->adapter);
  $delete = $sql->delete();
  $delete->from('reporte');
  $delete->where("reporte_date < '$antes_ayer'");
  $deleteString = $sql->getSqlStringForSqlObject($delete);
  $results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);

  $dataInsert = array(
    "reporte_json" => $json_data,
    "reporte_date" => $hoy,
  );
  $sql = new Sql($this->adapter);
  $insert = $sql->insert();
  $insert->into('reporte');
  $insert->values($dataInsert);
  $insertString = $sql->getSqlStringForSqlObject($insert);
  $results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);
  $nrorep = $results->getGeneratedValue();

  echo $nrorep; die;

  $j = base64_encode($nrorep);
  $url = 'http://dario-casa.sytes.net/api/tcpdf/gclpdf/fixturecompleto.php?param='.$j;
  $json = new stdClass();
  $json->success = true;
  $json->url = "$url";
  return $json;
}else{
  $json = new stdClass();
  $json->success = true;
  $json->msg = "La zona no tiene Resultados para la fecha ingresada.";
  return $json;
}

}

function busco_expulsados($fixture_id, $equipo_id){
  $sql = new Sql($this->adapter);
  $select = $sql->select();
  $select->from('sancionados');
  $select->join(array("jugador" => "jugador"),'jugador.jugador_id = sancionados.sancionados_jugador_id',array('*'),'inner');
  $select->where(array('sancionados_fixture_id' => $fixture_id,
                       'sancionados_equipo_id' => $equipo_id,));
  $selectString = $sql->getSqlStringForSqlObject($select);
  $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
  $amonestados = $results->toArray();

  if (!empty($amonestados)) {
    return $amonestados;
  }else {
    $amonestados = "No hubo expulsados";
    return $amonestados;
  }
}

function busco_amonestados($fixture_id, $equipo_id){
  $sql = new Sql($this->adapter);
  $select = $sql->select();
  $select->from('tarjetas');
  $select->join(array("jugador" => "jugador"),'jugador.jugador_id = tarjetas.tarjetas_jugador_id',array('*'),'inner');
  $select->where(array('tarjetas_fixture_id' => $fixture_id,
                       'tarjetas_equipo_id' => $equipo_id,));
  $selectString = $sql->getSqlStringForSqlObject($select);
  $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
  $amonestados = $results->toArray();

  if (!empty($amonestados)) {
    return $amonestados;
  }else {
    $amonestados = "No hubo amonestados";
    return $amonestados;
  }
}
function busco_goleadores($fixture_id, $equipo_id){
  $sql = new Sql($this->adapter);
  $select = $sql->select();
  $select->from('goles');
  $select->join(array("jugador" => "jugador"),'jugador.jugador_id = goles.goles_jugador_id',array('*'),'inner');
  $select->where(array('goles_fixture_id' => $fixture_id,
                       'goles_equipo_id' => $equipo_id,));
  $selectString = $sql->getSqlStringForSqlObject($select);
  $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
  $goleadores = $results->toArray();

  if (!empty($goleadores)) {
    return $goleadores;
  }else {
    $goleadores = "No hizo goles";
    return $goleadores;
  }
}

}
