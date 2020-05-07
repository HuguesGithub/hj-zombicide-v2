<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * SurvivorActions
 * @author Hugues
 * @since 1.04.00
 * @version 1.05.07
 */
class SurvivorActions extends LocalActions
{
  /**
   * Constructeur
   */
  public function __construct($post=array())
  {
    parent::__construct();
    $this->post = $post;
    $this->ExpansionServices = new ExpansionServices();
    $this->SurvivorServices  = new SurvivorServices();
    $this->WpPostServices    = new WpPostServices();
  }
  /**
   * Point d'entrée des méthodes statiques.
   * @param array $post
   * @return string
   **/
  public static function dealWithStatic($post)
  {
    $returned = '';
    $Act = new SurvivorActions($post);
    switch ($post[self::CST_AJAXACTION]) {
      case self::AJAX_GETSURVIVORS    :
        $returned = $Act->dealWithGetSurvivors();
      break;
      case self::AJAX_GETRANDOMTEAM :
        $returned = $Act->dealWithGetRandomTeam();
      break;
      case self::AJAX_SURVIVORVERIF  :
        $returned = $Act->dealWithSurvivorVerif(true);
      break;
      default :
        $returned = '';
      break;
    }
    return $returned;
  }

  /**
   * Récupération du contenu de la page via une requête Ajax.
   * @param array $post
   * @return string
   */
  public function dealWithGetSurvivors()
  {
    $Bean = new WpPageSurvivorsBean();
    $Bean->setFilters($this->post);
    return $this->jsonString($Bean->getListContentPage(), self::PAGE_SURVIVOR, true);
  }

  /**
   * @return string;
   */
  public function dealWithGetRandomTeam()
  {
    $Bean = new WpPageSurvivorsBean();
    return $this->jsonString($Bean->getRandomTeam($this->post), self::PAGE_SELECT_SURVIVORS, true);
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  // Bloc de gestion de vérifications des Survivants en Home Admin
  /**
   * @param boolean $isVerif
   * @return string
   */
  public function dealWithSurvivorVerif($isVerif=false)
  {
    // On récupère les articles de survivants
    $args = array(
      self::WP_CAT         => self::WP_CAT_SURVIVOR_ID,
      self::WP_TAXQUERY    => array(),
      self::WP_POSTSTATUS  => self::WP_PUBLISH.', future',
    );
    $this->WpPostSurvivors = $this->WpPostServices->getArticles($args);
    $nbWpPostSurvivors = count($this->WpPostSurvivors);
    // Et les Survivants en base
    $this->Survivors = $this->SurvivorServices->getSurvivorsWithFilters();
    $nbSurvivors = count($this->Survivors);
    if ($isVerif) {
      $this->checkSurvivors();
      $strBilan = $this->jsonString($this->strBilan, self::AJAX_SURVIVORVERIF, true);
    } elseif ($nbWpPostSurvivors!=$nbSurvivors) {
      $strBilan  = "Le nombre d'articles ($nbWpPostSurvivors) ne correspond pas au nombre de Survivants en base ($nbSurvivors).";
      $strBilan .= "<br>Une vérification est vivement conseillée.";
    } else {
      $strBilan = "Le nombre d'articles ($nbWpPostSurvivors) correspond au nombre de Survivants en base.";
    }
    return $strBilan;
  }

  private function insertSurvivor()
  {
    // Si on n'en a pas, on doit créer une entrée correspondante.
    $Survivor = new Survivor();
    $name = $this->WpPost->getPostTitle();
    $Survivor->setName($name);
    $description  = $this->WpPost->getPostContent();
    $description  = substr($description, 25, -27);
    $Survivor->setBackground($description);
    $Survivor->setExpansionId($this->getExpansionId());
    $arrProfiles = unserialize($this->WpPost->getPostMeta('profils'));
    foreach ($arrProfiles as $key=>$value) {
      switch ($value) {
        case 'Standard' :
          $Survivor->setStandard(1);
        break;
        case 'Zombivant' :
          $Survivor->setZombivor(1);
        break;
        case 'Ultimate' :
          $Survivor->setUltimate(1);
        break;
        case 'Ultimate Zombivant' :
          $Survivor->setUltimatez(1);
        break;
        default :
        break;
      }
    }
    $this->SurvivorServices->insertSurvivor($Survivor);
    $this->strBilan .= '<br>Survivant créé en base : '.$name.'.';
  }
  private function checkSurvivors()
  {
    // On regarde les articles créés et on vérifie les données en base, si elles existent et si elles sont cohérentes entre elles.
    while (!empty($this->WpPostSurvivors)) {
      // On récupère le WpPost et ses données
      $this->WpPost = array_shift($this->WpPostSurvivors);
      $survivorId = $this->WpPost->getPostMeta(self::FIELD_SURVIVORID);
      // On recherche un Survivant dans la base de données qui correspond.
      $Survivor = $this->SurvivorServices->selectSurvivor($survivorId);
      if ($Survivor->getId()=='') {
        $this->insertSurvivor();
      } else {
        // Si on en a juste une, c'est tranquille.
        $this->checkSurvivor($Survivor);
      }
    }
    // Puis, on regarde les données en base et on vérifie que des articles ont été créés pour elles.
    while (!empty($this->Survivors)) {
      // On récupère l'extension.
      $Survivor = array_shift($this->Survivors);
      $args = array(
        self::WP_METAKEY      => self::FIELD_SURVIVORID,
        self::WP_METAVALUE    => $Survivor->getId(),
        self::WP_TAXQUERY     => array(),
        self::WP_CAT          => self::WP_CAT_SURVIVOR_ID,
      );
      $WpPost = $this->WpPostServices->getArticles($args);
      if (empty($WpPost)) {
        $this->strBilan .= '<br>Article à créer pour un Survivant : '.$Survivor->getName().' ['.$Survivor->toJson().'].';
      }
    }
    if ($this->strBilan=='') {
      $this->strBilan = 'Il semblerait que tout aille à la perfection. Aucune anomalie remontée.';
    }
  }
  private function getExpansionId()
  {
    $postId        = $this->WpPost->getPostMeta(self::FIELD_EXPANSIONID);
    $Wp_post = get_post($postId);
    $WpPost = WpPost::convertElement($Wp_post);
    $codeExpansion = $WpPost->getPostMeta(self::FIELD_CODE);
    $Expansions = $this->ExpansionServices->getExpansionsWithFilters(array(self::FIELD_CODE=>$codeExpansion));
    $Expansion = array_shift($Expansions);
    return $Expansion->getId();
  }
  private function checkSurvivor($Survivor)
  {
    // On initialise les données
    $doUpdate = false;
    $name          = $this->WpPost->getPostTitle();
    $background    = $this->WpPost->getPostContent();
    $expansionId   = $this->getExpansionId();
    $arrProfils    = unserialize($this->WpPost->getPostMeta('profils'));
    // On vérifie si la donnée en base correspond à l'article.
    if ($Survivor->getName()!=$name) {
      $Survivor->setName($name);
      $doUpdate = true;
    }
    if ($Survivor->getExpansionId()!=$expansionId) {
      $Survivor->setExpansionId($expansionId);
      $this->strBilan .= '<br>Survivant mis à jour au niveau de l extension : '.$Survivor->getExpansionId().' - '.$expansionId.'.';
      $doUpdate = true;
    }
    if ($Survivor->getBackground()!=$background && $background!='' ) {
      $this->strBilan .= '<br>Background KO.';
      $Survivor->setBackground($background);
      $doUpdate = true;
    }
    if (isset($arrProfils)) {
      // TODO
    }
    if ($doUpdate) {
      // Si nécessaire, on update en base.
      $this->SurvivorServices->updateSurvivor($Survivor);
      $this->strBilan .= '<br>Survivant mis à jour : '.$name.'.';
    }
  }
  // Fin du bloc relatif à la vérification des compétences sur la Home Admin.
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////////

}
