<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SpawnTypeServices
 * @author Hugues.
 * @since 1.04.27
 * @version 1.04.27
 */
class SpawnTypeServices extends LocalServices
{
  /**
   * L'objet Dao pour faire les requêtes
   * @var SpawnTypeDaoImpl $Dao
   */
  protected $Dao;
  /**
   * Class Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->Dao = new SpawnTypeDaoImpl();
  }

}
