<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe PlayerServices
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
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
  public function getPlayersWithFilters($arrFilters=array(), $orderby='id', $order='asc')
  {
    $arrParams = $this->buildOrderAndLimit($orderby, $order);
    return $this->Dao->selectEntriesWithFilters(__FILE__, __LINE__, $arrParams);
  }
  /**
   * @param string $file
   * @param string $line
   * @param string $value
   * @param string $prefix
   * @param string $classe
   * @param bool $multiple
   * @param string $defaultValue
   * @return string
   */
  public function getNbPlayersSelect($file, $line, $value='', $prefix='', $classe='form-control', $multiple=false, $defaultValue='')
  {
    $Players = $this->getPlayersWithFilters($file, $line, array(), 'name', 'asc');
    $arrSetLabels = array();
    foreach ($Players as $Player) {
      $arrSetLabels[$Player->getId()] = $Player->getNbJoueurs();
    }
    $this->labelDefault = $defaultValue;
    $this->classe = $classe;
    $this->multiple = $multiple;
    return $this->getSetSelect($file, $line, $arrSetLabels, $prefix.'playerId', $value);
  }
}
