<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe KeywordServices
 * @author Hugues.
 * @since 1.0.00
 * @version 1.04.28
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
  /**
   * @param int $id
   * @return Keyword
   */
  public function selectKeyword($id)
  { return $this->select(__FILE__, __LINE__, $id); }

}
