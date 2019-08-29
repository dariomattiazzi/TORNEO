<?php
namespace torneo\V1\Rest\sancionados;

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

class sancionadosMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function SancionadosPorCat()
  {
    $torneo_id     = $_GET["torneo_id"];
    $categoria_id  = $_GET["categoria_id"];


    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('fixture');
    $select->join(array("sancionados" => "sancionados"),'fixture.fixture_id = sancionados.sancionados_fixture_id',array('*'),'inner');
    $select->join(array("jugador"     => "jugador"),'sancionados.sancionados_jugador_id = jugador.jugador_id',array('*'),'inner');
    $select->join(array("equipo"      => "equipo"),'sancionados.sancionados_equipo_id = equipo.equipo_id',array('*'),'inner');
    $select->where(array('fixture_categoria_id' => $categoria_id,
                         'sancionados_sancion > 0',
                         'fixture_estado'       => "false" //Se lo agrego cansado PROBAR!!!!! solo deberia mostrar los sancionados cuyo partido esté cerrado
                       ));
    $select->where->isNotNull("sancionados_sancion");
    $select->order('sancionados.sancionados_equipo_id, jugador.jugador_apellido, jugador.jugador_nombre, sancionados.sancionados_sancion');
//    $select->limit('10');
    $selectString = $sql->getSqlStringForSqlObject($select);
    //  echo $selectString; die;
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $sancionados = $results->toArray();

    //print_r($sancionados); die;

    foreach ($sancionados as $key => $row) {
      $s = $row['sancionados_sancion'];
      if ($s == '999') {
        $sancion = 'PROVISORIA';
      }else {
        $sancion = $s;
      }
      $arr [] = array(
        'text'                => $row['jugador_nombre'] ." " .$row['jugador_apellido'],
        'jugador_nombre'      => $row['jugador_nombre'] ." " .$row['jugador_apellido'],
        'jugador_apellido'    => $row['jugador_apellido'],
        'sancion'             => $sancion,
        "sancionados_vuelve"  =>  $row ['sancionados_vuelve'],
        "equipo_nombre"       =>  $row ['equipo_nombre'],

      );
    }

    if (!empty($sancionados)) {
      $json = new stdClass();
      $json->success = true;
      $json->sancionados = $arr;
      return $json;
    }else {
      $json = new stdClass();
      $json->success = false;
      $json->msg = "No hay registros para mostrar.";
      return $json;
    }
  }

  public function fetchOne()
  {
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
      $eq = "LIBRE";
      return $eq;
    }
  }

  function buscoSancionados($fixture_id, $equipo_id)
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('goles');
    $select->where(array('goles_fixture_id' => $fixture_id, 'goles_equipo_id' => $equipo_id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $goles = $results->toArray();

    // print_r($goles); die;//

    if (!empty($goles)) {
      foreach ($goles as $key => $row) {
        $jugador = $this->buscoJugador($row ['goles_jugador_id']);
        $arr [] = array(
          'text'             => $jugador['jugador_nombre'] ." " .$jugador['jugador_apellido'],
          'jugador_id'       => $jugador['jugador_id'],
          'jugador_nombre'   => $jugador['jugador_nombre'],
          'jugador_apellido' => $jugador['jugador_apellido'],
          "cant_goles"       =>  $row ['goles_cantidad'],
        );
      }
       return $arr;
    }else {
      $arr = array(
          'success'  => "false",
          'msg'      => "No Existe sancionados en este partido."
        );
      return $arr;
    }
  }

  function buscoJugador($id)
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('jugador');
    $select->where(array('jugador_id' => $id));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $jugador = $results->toArray();
    //print_r($jugador[0]); die;
    $j = $jugador[0];
    return $j;

  }

  public function create($data)
  {
  }

  public function crea($data)
  {

  }

  public function actualiza($data)
  {

  }

  public function delete($id)
  {

  }
}
