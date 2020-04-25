<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe WpPageTagBean
 * @author Hugues
 * @since 1.04.16
 * @version 1.04.16
 */
class WpPageTagBean extends WpPageBean
{
  protected $urlTemplate = 'web/pages/public/wppage-spawncards.php';
  /**
   * Class Constructor
   * @param WpPage $WpPage
   */
  public function __construct($scriptUrl='')
  {
    $arr = explode('/', $scriptUrl);
    $tag = $arr[2];
    // On défini les Services
    $this->WpTagServices    = new WpTagServices();
    $this->DurationServices = new DurationServices();
    $this->LevelServices    = new LevelServices();
    // On initialise le Tag
    $this->WpTag = $this->WpTagServices->getTagBySlug($tag);
  }
  /**
   * @return string
   */
  public function getContentPage()
  {
    /////////////////////////////////////////////////////////////////////////////
    // On récupère le slug
    $slug = $this->WpTag->getSlug();
    /////////////////////////////////////////////////////////////////////////////
    // Slugs relatifs à la Durée des Missions
    if (strpos($slug, '-minutes')!==false) {
      $arr = explode('-', $slug);
      $minDuration = $arr[0];
      $Durations = $this->DurationServices->getDurationsWithFilters(array(self::FIELD_MINDURATION=>$minDuration));
      $Duration = array_shift($Durations);
      $WpPage = new WpPageMissionsBean();
      $WpPage->setFilters(array(self::CST_FILTERS=>self::FIELD_DURATIONID.'='.$Duration->getId()));
      $returned = $WpPage->getListContentPage();
    } else {
      switch ($slug) {
        /////////////////////////////////////////////////////////////////////////////
        // Slugs relatifs à la Difficulté des Missions
        case 'tutoriel'   :
        case 'facile'     :
        case 'moyen'      :
        case 'difficile'  :
        case 'hardcore'   :
        case 'competitif' :
          $Levels = $this->LevelServices->getLevelsWithFilters(array(self::FIELD_NAME=>$slug));
          $Level = array_shift($Levels);
          $WpPage = new WpPageMissionsBean();
          $WpPage->setFilters(array(self::CST_FILTERS=>self::FIELD_LEVELID.'='.$Level->getId()));
          $returned = $WpPage->getListContentPage();
        break;
        default :
          $returned = "WIP : Gestion des Tags [[$slug]].";
        break;
      }
    }
    return $returned;
  }
}
