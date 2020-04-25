<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe KeywordServices
 * @author Hugues.
 * @version 1.0.00
 * @since 1.0.00
 */
class KeywordServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var KeywordDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new KeywordDaoImpl();
  }
  private function buildFilters($arrFilters)
  {
    $arrParams = array();
    $arrParams[] = (isset($arrFilters[self::CST_NAME]) ? $arrFilters[self::CST_NAME] : '%');
    $arrParams[] = (isset($arrFilters[self::CST_DESCRIPTION]) ? $arrFilters[self::CST_DESCRIPTION] : '%');
    return $arrParams;
  }

  /**
   * @param string $file
   * @param string $line
   * @param array $arrFilters
   * @param string $orderby
   * @param string $order
   * @return array
   */
  public function getKeywordsWithFilters($file, $line, $arrFilters=array(), $orderby='id', $order='asc')
  {
    $arrParams = $this->buildOrderAndLimit($orderby, $order);
    $arrParams[SQL_PARAMS_WHERE] = $this->buildFilters($arrFilters);
    return $this->Dao->selectEntriesWithFilters($file, $line, $arrParams);
  }
  
}
