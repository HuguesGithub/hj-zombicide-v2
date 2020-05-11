<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe ExpansionBean
 * @author Hugues
 * @since 1.04.24
 * @version 1.05.11
 */
class ExpansionBean extends LocalBean
{
  protected $urlRowAdmin  = 'web/pages/admin/fragments/expansion-row.php';
  /**
   * Class Constructor
   * @param Expansion $Expansion
   */
  public function __construct($Expansion='')
  {
    parent::__construct();
    $this->Expansion = ($Expansion=='' ? new Expansion() : $Expansion);
  }

  /**
   * @return string
   */
  public function getButton($extraClass='btn-dark')
  {
    $str  = '<div type="button" class="btn btn-expansion'.$extraClass.'" data-expansion-id="'.$this->Expansion->getId();
    $str .= '" data-nb-survivants="'.$this->Expansion->getNbSurvivants();
    return $str. '"><span><i class="far fa-square"></i></span> '.$this->Expansion->getName().'</div>';
  }

  /**
   * @return string
   */
  public function getRowForAdminPage()
  {
    $args = array(
      // L'identifiant de l'extension - 1
      $this->Expansion->getId(),
      // Le code de l'extension - 2
      $this->Expansion->getCode(),
      // L'url d'édition du WpPost - 3
      $this->Expansion->getEditUrl(),
      // L'url publique de l'extension - 4
      $this->Expansion->getWpPostUrl(),
      // Son nom - 5
      $this->Expansion->getName(),
      // Son rang d'affichage - 6
      $this->Expansion->getDisplayRank(),
      // Le nombre de Survivants - 7
      $this->Expansion->getNbSurvivants(),
      // Le nombre de Missions - 8
      $this->Expansion->getNbMissions(),
      // Est une Mission officielle - 9
      ($this->Expansion->isOfficial() ? 'Oui' : 'Non'),
    );
    return $this->getRender($this->urlRowAdmin, $args);
  }
























  /**
   * @param int $id
   * @return string
   *
  public function getMenuButtonLive($id)
  {
    $Expansion = $this->Expansion;
    $str  = '<div type="button" class="btn btn-dark btn-expansion" data-expansion-id="'.$id.'"><span class="';
    return $str.'"><i class="far fa-square"></i></span> '.$Expansion->getName().'</div>';
  }
  /**
   * @param string $id
   * @param string $spawnSpan
   * @return string
   *
  public function getSpawnMenuButtonLive($id, $spawnSpan)
  {
    $Expansion = $this->Expansion;
    $str  = '<div type="button" class="btn btn-dark btn-expansion" data-expansion-id="'.$id.'"><span data-spawnspan="'.$spawnSpan;
    return $str.'"><i class="far fa-square"></i></span> '.$Expansion->getName().$spawnSpan.'</div>';
  }
  * */
}
