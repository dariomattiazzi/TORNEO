<?php
namespace torneo\V1\Rest\Limpiobd;

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

class LimpiobdMapper
{
  protected $adapter;
  public function __construct(AdapterInterface $adapter)
  {
    $this->adapter = $adapter;
  }

  public function create($data)
  {
    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('equipo');
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $equipo = $results->toArray();

    foreach ($equipo as $key => $row) {
        $equipo_id        = $row ['equipo_id'];
        $equipo_nombre    = $this->sanear_string(utf8_encode($row ['equipo_nombre']));
        $sql = new Sql($this->adapter);
        $update = $sql->update();
        $update->table('equipo');
        $update->set(array("equipo_nombre" => $equipo_nombre));
        $update->where->equalTo("equipo_id", $equipo_id);
        $updateString = $sql->getSqlStringForSqlObject($update);
        $this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
    }

    $sql = new Sql($this->adapter);
    $select = $sql->select();
    $select->from('jugador');
    $selectString = $sql->getSqlStringForSqlObject($select);
    $results = $this->adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
    $jugador = $results->toArray();

    foreach ($jugador as $key => $row) {
        $jugador_id        = $row ['jugador_id'];
        $jugador_nombre    = $this->sanear_string(utf8_encode($row ['jugador_nombre']));
        $jugador_apellido  = $this->sanear_string(utf8_encode($row ['jugador_apellido']));
        $sql = new Sql($this->adapter);
        $update = $sql->update();
        $update->table('jugador');
        $update->set(array("jugador_nombre" => $jugador_nombre, "jugador_apellido" => $jugador_apellido));
        $update->where->equalTo("jugador_id", $jugador_id);
        $updateString = $sql->getSqlStringForSqlObject($update);
        $this->adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
    }
  }

  function sanear_string($string)
  {
      $string = trim($string);
      $s = array('Á', 'É', 'Í', 'Ó', 'Ú');
      $t = array('A', 'E', 'I', 'O', 'U');
      $string = str_replace($s, $t,$string);


      $string = str_replace(
          array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
          array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
          $string
      );

      $string = str_replace(
          array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
          array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
          $string
      );

      $string = str_replace(
          array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
          array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
          $string
      );

      $string = str_replace(
          array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
          array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
          $string
      );

      $string = str_replace(
          array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
          array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
          $string
      );

      $string = str_replace(
          array('ñ', 'Ñ', 'ç', 'Ç'),
          array('n', 'N', 'c', 'C',),
          $string
      );
      return $string;
  }

}
