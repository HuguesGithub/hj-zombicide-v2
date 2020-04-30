<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * AdminPageExpansionsBean
 * @author Hugues
 * @since 1.04.30
 * @version 1.04.30
 */
class AdminPageExpansionsBean extends AdminPageBean
{
  protected $tplHomeCheckCard  = 'web/pages/admin/fragments/home-check-card.php';
  /**
   * Class Constructor
   */
  public function __construct($urlParams='')
  {
    $this->urlParams = $urlParams;
    parent::__construct(self::CST_EXPANSION);
    $this->title = 'Extensions';
    $this->ExpansionServices  = new ExpansionServices();
  }

  /**
   * @return string
   */
  public function getCheckCard()
  {
    /////////////////////////////////////////////////
    // Gestion des Extensions.
    // On récupère la liste des Extensions qui ont un Article. Puis les données dans la base. On compare et on effectue un diagnostic.
    $Act = new ExpansionActions();
    $strBilan  = $Act->dealWithExpansionVerif();

    $args = array(
      // Le titre de la carte - 1
      $this->title,
      // L'id du container de retour pour afficher les vérifications - 2
      self::CST_EXPANSION.'-verif',
      // Le contenu du container de vérification - 3
      $strBilan,
   );
    return $this->getRender($this->tplHomeCheckCard, $args);
  }
}
