<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe ExpansionBean
 * @author Hugues
 * @since 1.04.24
 * @version 1.07.21
 */
class ExpansionBean extends LocalBean
{
  protected $urlRowAdmin  = 'web/pages/admin/fragments/expansion-row.php';
  protected $urlRowPublic = 'web/pages/public/fragments/expansion-row.php';
  /**
   * @param Expansion $Expansion
   */
  public function __construct($Expansion=null)
  {
    parent::__construct();
    $this->Expansion = ($Expansion==null ? new Expansion() : $Expansion);
  }

  //////////////////////////////////////////////////////////////////////////
  // Différentes modes de présentation
  /**
   * @return string
   */
  public function getRowForAdminPage()
  {
    ///////////////////////////////////////////////////////////////////////////
    // On enrichit le template
    $args = array(
      // L'identifiant de l'extension - 1
      $this->Expansion->getId(),
      // Le code de l'extension - 2
      $this->Expansion->getCode(),
      // L'url d'édition du WpPost - 3
      $this->Expansion->getWpPostEditUrl(),
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
      // Lien de détail de l'extension - 10
      $this->Expansion->getEditUrl(self::CST_EXPANSION),
    );
    ///////////////////////////////////////////////////////////////
    // Puis on le retourne
    return $this->getRender($this->urlRowAdmin, $args);
  }

  /**
   * @return string
   */
  public function getRowForPublicPage()
  {
    ///////////////////////////////////////////////////////////////
    // On enrichi le template et on le retourne.
    $args = array(
      // Front Url de la Compétence - 1
      $this->Expansion->getWpPostUrl(),
      // Nom de la Compétence - 2
      $this->Expansion->getName(),
      // Identifiant de la Compétence - 3
      $this->Expansion->getId(),
    );
    return $this->getRender($this->urlRowPublic, $args);
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






















  ////////////////////////////////////////////////////////////////////////////



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
