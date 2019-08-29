<?php
namespace torneo\V1\Rest\goleadoresporcategoria;

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

class goleadoresporcategoriaMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function GoleadoresPorCat()
  {
    $categoria_id  = $_GET["categoria_id"];

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('fixture');
    $select->join(array("goles"   => "goles"),'fixture.fixture_id = goles.goles_fixture_id',array('*'),'inner');
    $select->join(array("jugador" => "jugador"),'goles.goles_jugador_id = jugador.jugador_id',array('*'),'inner');
    $select->join(array("equipo"  => "equipo"),'goles.goles_equipo_id = equipo.equipo_id',array('*'),'inner');
    $select->where(array('fixture_categoria_id' => $categoria_id,
                         'fixture_estado'       => "false" //Se lo agrego cansado PROBAR!!!!! solo deberia mostrar los sancionados cuyo partido esté cerrado
    ));
    $select->where->notequalTo("goles.goles_jugador_id", 0);
    $select->columns(array('golesXjug' => new \Zend\Db\Sql\Expression('SUM(goles.goles_cantidad)')));
    $select->group('goles.goles_jugador_id');
    $select->order('golesXjug DESC');
    //$select->limit('10');
    $selectString = $sql->getSqlStringForSqlObject($select);
    //echo $selectString; die;
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $goleadores = $results->toArray();

    //print_r($goleadores); die;

    if (!empty($goleadores)) {
      $json = new stdClass();
      $json->success = true;
      $json->goleadores = $goleadores;
      return $json;
    }else {
      $json = new stdClass();
      $json->success = true;
      $json->msg = "No hay registros para mostrar.";
      return $json;
    }
  }


  function buscoGoleadores($fixture_id, $equipo_id)
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
        'success'  => "true",
        'msg'      => "No Existe goleadores en este partido."
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
}
