<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPostSurvivorBean
 * @author Hugues
 * @since 1.04.00
 * @version 1.07.19
 */
class WpPostSurvivorBean extends WpPostBean
{
  protected $urlTemplate = 'web/pages/public/wppage-survivor.php';
  protected $arrLvls = array(1=>'S', 2=>'Z', 3=>'U', 4=>'UZ');
  protected $tplPortraitAndSkills = '<div class="col-2" style="margin-bottom:5px;">%1$s</div><div class="col-10" style="margin-bottom:5px;">%2$s</div>';

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
    // On va construire les lignes portrait + compétences
    //
    $strPortraitsSkills = '';
    if ($this->Survivor->isStandard()) {
      $arg = array(
        // Le portrait - 1
        $this->Survivor->getBean()->getPortrait(),
        // Les Compétences - 2
        $this->Survivor->getBean()->getSkills(),
      );
      $strPortraitsSkills .= vsprintf($this->tplPortraitAndSkills, $arg);
    }
    if ($this->Survivor->isZombivor()) {
      $arg = array(
        // Le portrait - 1
        $this->Survivor->getBean()->getPortrait('z'),
        // Les Compétences - 2
        $this->Survivor->getBean()->getSkills('z'),
      );
      $strPortraitsSkills .= vsprintf($this->tplPortraitAndSkills, $arg);
    }
    if ($this->Survivor->isUltimate()) {
      $arg = array(
        // Le portrait - 1
        $this->Survivor->getBean()->getPortrait('u'),
        // Les Compétences - 2
        $this->Survivor->getBean()->getSkills('u'),
      );
      $strPortraitsSkills .= vsprintf($this->tplPortraitAndSkills, $arg);
    }
    if ($this->Survivor->isUltimatez()) {
      $arg = array(
        // Le portrait - 1
        $this->Survivor->getBean()->getPortrait('uz'),
        // Les Compétences - 2
        $this->Survivor->getBean()->getSkills('uz'),
      );
      $strPortraitsSkills .= vsprintf($this->tplPortraitAndSkills, $arg);
    }

    //////////////////////////////////////////////////////////////////
    // On enrichit le template puis on le restitue.
    $args = array(
      // Nom du Survivant - 1
      $this->Survivor->getName(),
      // Background du Survivant - 2
      $this->Survivor->getBackground(),
      // Les lignes portrait + compétences - 3
      $strPortraitsSkills,
      // Lien de navigation - 4
      $this->getNavLinks(),
    );
    return $this->getRender($this->urlTemplate, $args);
  }
  private function getNavLinks()
  {
    //////////////////////////////////////////////////////////////////
    // On construit les liens de navigation
    // On récupère tous les Survivants, classées par ordre alphabétique.
    $Survivors = $this->SurvivorServices->getSurvivorsWithFilters();
    $firstSurvivor = null;
    while (!empty($Survivors)) {
      $Survivor = array_shift($Survivors);
      // On les parcourt jusqu'à trouver la courante.
      if ($Survivor->getId()==$this->Survivor->getId()) {
        break;
      }
      if ($firstSurvivor==null) {
        $firstSurvivor = $Survivor;
      }
      $prevSurvivor = $Survivor;
    }
    $nextSurvivor = array_shift($Survivors);
    if (empty($prevSurvivor)) {
      $prevSurvivor = array_pop($Survivors);
    }
    if (empty($nextSurvivor)) {
      $nextSurvivor = $firstSurvivor;
    }

    $nav = '';
    // On exploite la précédente et la suivante.
    if (!empty($prevSurvivor)) {
      $attributes = array(self::ATTR_HREF=>$prevSurvivor->getWpPost()->getPermalink(), self::ATTR_CLASS=>'adjacent-link col-3');
      $nav .= $this->getBalise(self::TAG_A, '&laquo; '.$prevSurvivor->getWpPost()->getPostTitle(), $attributes);
    }
    if (!empty($nextSurvivor)) {
      $attributes = array(self::ATTR_HREF=>$nextSurvivor->getWpPost()->getPermalink(), self::ATTR_CLASS=>'adjacent-link col-3');
      $nav .= $this->getBalise(self::TAG_A, $nextSurvivor->getWpPost()->getPostTitle().' &raquo;', $attributes);
    }
    return $nav;
  }






  /**
   * @return Survivor
   */
  public function getSurvivor()
  { return $this->Survivor; }
}
