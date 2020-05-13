<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPostSurvivorBean
 * @author Hugues
 * @since 1.04.00
 * @version 1.04.27
 */
class WpPostSurvivorBean extends WpPostBean
{
  protected $urlTemplate = 'web/pages/public/wppage-survivor.php';
  protected $arrLvls = array(1=>'S', 2=>'Z', 3=>'U', 4=>'UZ');

  /**
   * Class Constructor
   */
  public function __construct($WpPost)
  {
    parent::__construct();
    $this->SurvivorServices      = new SurvivorServices();
    $this->WpPost = $WpPost;
    $postMetas = $this->WpPost->getPostMetas();
    $survivorId = $postMetas[self::FIELD_SURVIVORID][0];
    $this->Survivor = $this->SurvivorServices->selectSurvivor($survivorId);
  }
  /**
   * @return string
   */
  public function displayWpPost()
  { return $this->Survivor->getBean()->getContentForHome(); }
  /**
   * On retourne la page dédiée au Survivant.
   * @return string
   */
  public function getContentPage()
  {
    //////////////////////////////////////////////////////////////////
    // On enrichit le template puis on le restitue.
    $args = array(
      // Tous les portraits existants - 1
      $this->Survivor->getBean()->getAllPortraits(),
      // Nom du Survivant - 2
      $this->Survivor->getName(),
      // Background du Survivant - 3
      $this->Survivor->getBackground(),
      // Cases à cocher éventuelles pour afficher les différents profils - 4
      $this->Survivor->getBean()->getCheckBoxType(),
      // Liste des compétences du Survivant - 5
      $this->Survivor->getBean()->getAllSkills(),
    );
    return $this->getRender($this->urlTemplate, $args);
  }






  /**
   * @return Survivor
   */
  public function getSurvivor()
  { return $this->Survivor; }
}
