<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe PlayerServices
 * @author Hugues.
 * @since 1.04.27
 * @version 1.04.27
 */
class PlayerServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var PlayerDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new PlayerDaoImpl();
  }

  /**
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getPlayersWithFilters($arrFilters=array(), $orderby=self::FIELD_ID, $order=self::ORDER_ASC)
  {
    $this->arrParams = $this->buildOrderAndLimit($orderby, $order);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $this->arrParams);
  }
  /**
   * @param int $id
   * @return Player
   */
  public function selectPlayer($id)
  { return $this->select(__FILE__, __LINE__, $id); }
}
