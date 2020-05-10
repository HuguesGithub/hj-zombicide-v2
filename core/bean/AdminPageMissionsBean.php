<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * AdminPageMissionsBean
 * @author Hugues
 * @since 1.05.10
 * @version 1.05.10
 */
class AdminPageMissionsBean extends AdminPageBean
{
  protected $tplHomeCheckCard  = 'web/pages/admin/fragments/home-check-card.php';
  /**
   * Class Constructor
   */
  public function __construct($urlParams='')
  {
    $this->urlParams = $urlParams;
    parent::__construct(self::CST_MISSION);
    $this->title = 'Missions';
  }

  /**
   * @return string
   */
  public function getCheckCard()
  {
    /////////////////////////////////////////////////
    // Gestion des Missions.
    // On récupère la liste des Missions qui ont un Article. Puis les données dans la base. On compare et on effectue un diagnostic.
    $Act = new MissionActions();
    $strBilan  = $Act->dealWithMissionVerif();

    $args = array(
      // Le titre de la carte - 1
      $this->title,
      // L'id du container de retour pour afficher les vérifications - 2
      self::CST_MISSION,
      // Le contenu du container de vérification - 3
      $strBilan,
   );
    return $this->getRender($this->tplHomeCheckCard, $args);
  }
}
