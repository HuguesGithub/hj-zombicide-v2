<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe TileServices
 * @author Hugues.
 * @since 1.04.07
 * @version 1.04.27
 */
class TileServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var TileDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new TileDaoImpl();
  }

}
