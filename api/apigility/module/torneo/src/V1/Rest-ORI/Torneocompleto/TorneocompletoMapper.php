<?php
namespace torneo\V1\Rest\torneocompleto;

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

class torneocompletoMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function fetchAll()
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('torneo');
    //$select->where(array('torneo_id' => 2));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $torneo = $results->toArray();

    // print_r($cat); die();
    foreach ($torneo as $key => $row) {
      $id_torneo = $row ['torneo_id'];
		if($row ['torneo_estado'] == 0){
		  $arr_torneo [] = array(
		    'text'            => $row ['torneo_descri'],
		    'torneo_id'       => $row ['torneo_id'],
		    'torneo_descri'   => $row ['torneo_descri'],
		    'torneo_estado'   => $row ['torneo_estado'],
		    "nivel"           => 1,
			"cls"             => "erase",
		    'children'        => $this->buscoCategorias($id_torneo)

		  );
		}else{
		  $arr_torneo [] = array(
		    'text'            => $row ['torneo_descri'],
		    'torneo_id'       => $row ['torneo_id'],
		    'torneo_descri'   => $row ['torneo_descri'],
		    'torneo_estado'   => $row ['torneo_estado'],
		    "nivel"           => 1,
		    'children'        => $this->buscoCategorias($id_torneo)

		  );
		}
    }
//    print_r($arr_torneo); die();

    if (!empty($arr_torneo)) {
      $json = new stdClass();
      $json->success = true;
      $json->children = $arr_torneo;
      return $json;
    }else {
      $json = new stdClass();
      $json->success = true;
      $json->msg = "No Existen torneo.";
      $json->children = [];
      return $json;
    }
  }

  function buscoCategorias($id_torneo)
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('categoria');
    $select->where(array('categoria_torneo_id' => $id_torneo));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $categorias = $results->toArray();

    $arr_categorias = array();

    foreach ($categorias as $key => $row) {
      $id_categoria = $row ['categoria_id'];
      $arr_categorias [] = array(
        'text'                            => $row ['categoria_descri'],
        'categoria_id'                    => $row ['categoria_id'],
        'categoria_descri'                => $row ['categoria_descri'],
        'categoria_torneo_id'             => $row ['categoria_torneo_id'],
        'categoria_juega_coparevancha'    => $row ['categoria_juega_coparevancha'],
        'categoria_cant_a_copacampeonato' => $row ['categoria_cant_a_copacampeonato'],
        'categoria_cant_a_coparevancha'   => $row ['categoria_cant_a_coparevancha'],
        "nivel"               => 2,
        'children'            => $this->buscoZonas($id_categoria, $id_torneo)
      );
    }
    return $arr_categorias;
  }

  function buscoZonas($id_categoria, $id_torneo)
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('zona');
    $select->where(array('zona_categoria_id' => $id_categoria));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $zonas = $results->toArray();

    //print_r($zonas); die();
    $arr_zonas = array();
    foreach ($zonas as $key => $row) {
      $id_zona = $row ['zona_id'];
      $arr_zonas [] = array(
        'text'                  => $row ['zona_descri'],
        'torneo_id'             => $id_torneo,
        'zona_id'               => $row ['zona_id'],
        'zona_descri'           => $row ['zona_descri'],
        'zona_categoria_id'     => $row ['zona_categoria_id'],
	      'zona_cantidad_equipos' => $row ['zona_cantidad_equipos'],
        'zona_mostrar_posicion' => $row ['zona_mostrar_posicion'],
        "nivel"                 => 3,
        'children'              => $this->buscoEquipos($id_zona)
      );
    }
    return $arr_zonas;
  }

  function buscoEquipos($id_zona)
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('equipo_zona');
    $select->join(array("equipo" => "equipo"),'equipo_zona.equipo_id = equipo.equipo_id',array('*'),'left');
    $select->where(array('zona_id' => $id_zona));
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $equipos = $results->toArray();

    $arr_equipos = array();
    foreach ($equipos as $key => $row) {
      $arr_equipos [] = array(
        "nivel"            => 4,
        //'text'             => utf8_encode($row ['equipo_nombre']),
        'text'             => $row ['equipo_nombre'],
        'equipo_id'        => $row ['equipo_id'],
        'equipo_nombre'    => utf8_encode($row ['equipo_nombre']),
        'equipo_delegado'  => $row ['equipo_delegado'],
        'leaf'             => true
      );
    }
    return $arr_equipos;
  }

  public function fetchOne($id)
  {
  }

  public function create($data)
  {
  }

  public function delete($id)
  {
  }
}
