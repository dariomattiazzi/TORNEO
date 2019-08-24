<?php
namespace torneo\V1\Rest\fixture;

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

class fixtureMapper
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
    $select->from('fixture');
    $select->where(array('fixture_torneo_id' => $torneo_id, 'fixture_categoria_id' => $categoria_id, 'fixture_zona_id' => $zona_id));
    $select->order(array('fixture_torneo_id', 'fixture_categoria_id', 'fixture_zona_id', 'fixture_fecha', 'fixture_fase_id DESC', 'fixture_turno_id ', 'fixture_cancha_id'));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $fixture = $results->toArray();

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('equipo_zona');
    $select->where(array('zona_id' => $zona_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    //echo "$selectString"; die;
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $cant_equipos = $results->toArray();

    $ban = 0;
    $ban2 = 0;
    foreach ($fixture as $key => $row) {
      $arr_fixt = $this->PartidosXfechaXzona($torneo_id, $categoria_id, $zona_id);
    }

    // foreach ($arr_fixt as $clave => $fila) {
    //   $fx[$clave] = $fila['fixture_fase_id'];
    // }
    // array_multisort($fx, SORT_ASC, $arr_fixt);


    foreach ($arr_fixt as $clave => $fila) {
      //print_r($fila); die;
      $fx[$clave] = $fila['fixture_fase_id'];
      $fx2[$clave] = $fila['fecha'];
      $fx3[$clave] = $fila['turno'];
    }
    array_multisort($fx, SORT_ASC, $fx2, SORT_ASC, $fx3, SORT_ASC, $arr_fixt);

    /*
    foreach ($arr as $key => $row) {
      $aux_ptos[$key] = $row['ptos'];
      $aux_dif[$key]  = $row['dif'];
      $aux_gf[$key]  = $row['gf'];
    }

    array_multisort(@$aux_ptos, SORT_DESC, @$aux_dif, SORT_DESC, $aux_gf, SORT_DESC, $arr);
    */

    if(!empty($arr_fixt)){
      $json = new stdClass();
      $json->success = true;
      $json->fixture = $arr_fixt;
      return $json;
    }else{
      $json = new stdClass();
      $json->success = true;
      $json->fixture = [];
      $json->msg = "La zona no tiene Fixture generado";
      return $json;
    }
  }

  function PartidosXfechaXzona($torneo_id, $categoria_id, $zona_id){
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('fixture');
    $select->where(array(
      'fixture_torneo_id' => $torneo_id,
      'fixture_categoria_id' => $categoria_id,
      'fixture_zona_id' => $zona_id
    ));
    $select->order(array('fixture_torneo_id', 'fixture_categoria_id', 'fixture_zona_id', 'fixture_fecha', 'fixture_turno_id', 'fixture_cancha_id'));
    $selectString = $sql->getSqlStringForSqlObject($select);
    //echo "$selectString"; die;
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $fixture = $results->toArray();

    foreach ($fixture as $key => $row) {
      if($row ['fixture_fase_id'] > 1){
        $fase = $this->nombre_fase($row ['fixture_fase_id']);

        if (!empty($fase[0]['fase_descri'])) {
          if($fase[0]['fase_cant_partido'] > 1) {
            if($row ['fixture_fecha'] == 1){
              $serie = 'IDA';
            }else{
              $serie = 'VUELTA';
            }
            $fecha = $fase[0]['fase_descri']. ' - '. $serie;
          }else{
            $fecha = $fase[0]['fase_descri'];
          }
          //$fecha = $fase[0]['fase_descri'];
        }else {
          $fecha = "A CONFIRMAR";

        }
        //		$fecha = $this->nombre_fase($row ['fixture_fase_id']);
      }else{
        $fecha = $row ['fixture_fecha'];
      }
      //print_r($row); die;
      if(!empty($row ['fixture_penales_eq1'])){
        $pen_eq1 = ' ('.$row ['fixture_penales_eq1'].')';
      }else{
        $pen_eq1 = '';
      }

      if(!empty($row ['fixture_penales_eq2'])){
        $pen_eq2 = ' ('.$row ['fixture_penales_eq2'].')';
      }else{
        $pen_eq2 = '';
      }
      if(is_int($fecha)){
	$orden = $fecha;
      }else{
$order = 1;
switch($fecha){
case 'FINAL':
$order = 9;
break;
case 'Semi Final':
$order = 8;
break;
case 'Cuartos de final':
$order = 7;
break;
}
	}
      $arr_fixture [] = array(
        'fecha'                => $fecha,
	'orden'                => $order,
        'fixture_id'           => $row ['fixture_id'],
        'fixture_torneo_id'    => $row ['fixture_torneo_id'],
        'fixture_categoria_id' => $row ['fixture_categoria_id'],
        'fixture_zona_id'      => $row ['fixture_zona_id'],
        'fixture_fecha'        => $row ['fixture_fecha'],
        'equipo_id1'           => $row ['fixture_equipo_id1'],
        'equipo1'              => $this->nombre_equipo($row ['fixture_equipo_id1']),
        'fixture_goles_eq1'    => $row ['fixture_goles_eq1'].$pen_eq1,
        'imagen1'              => "",
        'equipo_id2'           => $row ['fixture_equipo_id2'],
        'equipo2'              => $this->nombre_equipo($row ['fixture_equipo_id2']),
        'fixture_goles_eq2'    => $row ['fixture_goles_eq2'].$pen_eq2,
        'imagen2'              => "",
        'fixture_fase_id'      => $row ['fixture_fase_id'],
        'cancha'               => $row ['fixture_cancha_id'],
        'cancha_descri'        => $this->nombre_cancha($row ['fixture_cancha_id']),
        "turno"                => $row ['fixture_turno_id'],
        'turno_descri'         => $this->nombre_turno($row ['fixture_turno_id']),
        'fixture_estado'       => $row ['fixture_estado'],
      );
    }

    return $arr_fixture;
  }


  function nombre_fase($id){
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('fase');
    $select->where(array('fase_id' => $id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $fase = $results->toArray();

    return $fase;

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

  public function create($data)
  {
    //controlo cantidad de equipos x zonas
    $zona_id = $data->zona_id;
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('zona');
    $select->where(array('zona_id' => $zona_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $zona = $results->toArray();

    //print_r($zona[0][zona_cantidad_equipos]); die;
    $zona_cantidad_equipos = $zona[0]['zona_cantidad_equipos'];

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('equipo_zona');
    $select->where(array('zona_id' => $zona_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $equipo_zona = $results->toArray();

    $count_equipo_zona = count($equipo_zona);

    //si cantidad de equipos x zonas ingresados es igual a zona_cantidad_equipos de la tabla zonas
    // genero el fixture, sino devuelvo mensaje de error.
    if ($zona_cantidad_equipos == $count_equipo_zona) {
      $fixture = $this->buscoFixture($data);
      if (!empty($fixture)) {
        $this->eliminoFixture($data);
      }
      $zona_id = $data->zona_id;
      $arr_equipos = $this->buscoEquipos($zona_id);
      $this->main($arr_equipos, $data);

      //      $cancha = $this->cancha($data->torneo_id, $data->categoria_id, $data->zona_id);
      //      $cantidad_canchas = $this->cantidad_canchas();
      //      echo $cancha ."---" .$cantidad_canchas; die;

      $json = new stdClass();
      $json->success = true;
      return $json;

    }else {
      $json = new stdClass();
      $json->success = true;
      $json->msg = "La cantidad de equipos ingresados en la zona no es correcta.";
      return $json;
    }
  }

  public function eliminoFixture($data)
  {
    $torneo_id    = $data->torneo_id;
    $categoria_id = $data->categoria_id;
    $zona_id      = $data->zona_id;
    try {
      $sql = new Sql($this->adapter);
      $delete = $sql->delete();
      $delete->from('fixture');
      $delete->where(array('fixture_torneo_id' => $torneo_id, 'fixture_categoria_id' => $categoria_id, 'fixture_zona_id' => $zona_id));
      $deleteString = $sql->getSqlStringForSqlObject($delete);
      $results = $this->adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
    } catch (Exception $e) {
      $json = new stdClass();
      $json->success = false;
      $json->msg = "No se pudo eliminar el fixture.";
      return $json;
    }
  }

  function buscoFixture($data)
  {
    $torneo_id    = $data->torneo_id;
    $categoria_id = $data->categoria_id;
    $zona_id      = $data->zona_id;

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('fixture');
    $select->where(array('fixture_torneo_id' => $torneo_id, 'fixture_categoria_id' => $categoria_id, 'fixture_zona_id' => $zona_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $fixture = $results->toArray();
    return $fixture;
  }

  function main($arr_equipos, $data) {
    $e = count($arr_equipos);
    $this->show_fixtures($this->nums(intval($e)), $arr_equipos, $data);
    //die;
  }

  function nums($n) {
    $ns = array();
    for ($i = 1; $i <= $n; $i++) {
      $ns[] = $i;
    }
    return $ns;
  }

  function show_fixtures($names, $arr_equipos, $data) {
    $teams = sizeof($names);

    // Si el número impar de equipos agrega un "ghost"(libre).
    $ghost = false;
    if ($teams % 2 == 1) {
      $teams++;
      $ghost = true;
    }

    // Genera las variables usada el algoritmo cíclico.
    $totalRounds = $teams - 1;
    $matchesPerRound = $teams / 2;
    $rounds = array();
    for ($i = 0; $i < $totalRounds; $i++) {
      $rounds[$i] = array();
    }
    //    echo "\n 2";
    //    print_r($rounds);

    for ($round = 0; $round < $totalRounds; $round++) {
      for ($match = 0; $match < $matchesPerRound; $match++) {
        $home = ($round + $match) % ($teams - 1);
        $away = ($teams - 1 - $match + $round) % ($teams - 1);

        // El último equipo se queda en el mismo lugar mientras los otros giran a su alrededor.
        if ($match == 0) {
          $away = $teams - 1;
        }

        if(!empty($this->team_name($away + 1, $names, $arr_equipos, $data))){
          $t = " vs " .$this->team_name($away + 1, $names, $arr_equipos, $data);
        }else{
          $t = '';
        }
        $rounds[$round][$match] = $this->team_name($home + 1, $names, $arr_equipos, $data) . $t;
      }
    }

    $cancha = $this->cancha($data->torneo_id, $data->categoria_id, $data->zona_id);
    $cantidad_canchas = $this->cantidad_canchas();

    // Muestro el fixture
    for ($i = 0; $i < sizeof($rounds); $i++ ) {
      $cancha++;
      if($cancha > $cantidad_canchas){
        $cancha = 1;
      }
      foreach ($rounds[$i] as $r) {
        $ee = explode("vs", $r);

        try {
          $dataInsert = array(
            "fixture_torneo_id" => $data->torneo_id,
            "fixture_categoria_id" => $data->categoria_id,
            "fixture_zona_id" => $data->zona_id,
            "fixture_fecha" => ($i+1),
            "fixture_equipo_id1" => $ee[0],
            "fixture_equipo_id2" => $ee[1],
            "fixture_fase_id" => "1",
            "fixture_cancha_id" => "$cancha",
            "fixture_turno_id" => "999"
          );

          $sql = new Sql($this->adapter);
          $insert = $sql->insert();
          $insert->into('fixture');
          $insert->values($dataInsert);
          $insertString = $sql->getSqlStringForSqlObject($insert);
          // echo $insertString; die;
          $results = $this->adapter->query($insertString, Adapter::QUERY_MODE_EXECUTE);

        }catch (Exception $e) {
          $json = new stdClass();
          $json->success = false;
          $json->msg = "No se pudo generar el fixture.";
          return $json;
        }

      }
    }

    if ($ghost) {
      print "Matches against team " . $teams . " are byes.";
    }
  }

  function cantidad_canchas() {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('cancha');
    $select->where->notequalTo("cancha_id", 999);
    $selectString = $sql->getSqlStringForSqlObject($select);
    //echo $selectString; die;
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $canchas = $results->toArray();

    $cant = count($canchas);

    return $cant;
  }

  function cancha($torneo, $categoria, $zona) {
    // echo $torneo ." - ". $categoria ." - ". $zona ."\n";

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->columns(array('zona.zona_id','zona.zona_descri'));
    $select->from('torneo');
    $select->join(array("categoria" => "categoria"),'categoria.categoria_torneo_id = torneo.torneo_id',array('*'),'INNER');
    $select->join(array("zona" => "zona"),'zona.zona_categoria_id = categoria.categoria_id',array('*'),'INNER');

    $select->where(array('torneo_id' => $torneo));
    $selectString = $sql->getSqlStringForSqlObject($select);
    //echo $selectString; die;
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $zonas = $results->toArray();

    $i = 1;
    foreach ($zonas as $key => $row) {
      //$arr_equipos [$i] = $row ['zona_id'];
      //$i++;
      if ($row ['zona_id'] == $zona){
        $pos = $i - 1;
      }else{
        $i++;
      }
    }
    return $pos;
  }

  function team_name($num, $names, $arr_equipos, $data) {
    //    print_r($arr_equipos); die;

    $i = $num - 1;
    if (sizeof($names) > $i && strlen(trim($names[$i])) > 0) {
      //        return trim($names[$i]);
      return $arr_equipos[trim($names[$i])];
    } else {
      if(isset($arr_equipos[$num])){
        $p = $arr_equipos[$num];
      }else{
        $p = "9999";
      }
      return $p;
    }
  }

  function buscoEquipos($zona_id)
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('equipo_zona');
    $select->join(array("equipo" => "equipo"),'equipo_zona.equipo_id = equipo.equipo_id',array('*'),'left');
    $select->where(array('zona_id' => $zona_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $equipos = $results->toArray();

    $i = 1;
    foreach ($equipos as $key => $row) {
      $arr_equipos [$i] = $row ['equipo_id'];
      $i++;
    }
    return $arr_equipos;
  }

  public function delete($id)
  {
  }
}
