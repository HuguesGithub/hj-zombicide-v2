<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SurvivorBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.04.26
 */
class SurvivorBean extends LocalBean
{
  protected $urlRowPublic = 'web/pages/public/fragments/survivor-row.php';
  protected $urlArticle   = 'web/pages/public/fragments/survivor-article.php';
  protected $urlCardVisit = 'web/pages/public/fragments/survivor-cardvisit.php';
  protected $tplSkillBadge = '<a class="badge badge-%1$s-skill" href="%2$s">%3$s</a>';
  protected $tplDisabledSkillBadge = '<span class="badge badge-%1$s-skill">%3$s</span>';
  private $strPortraitSurvivant = 'portrait-survivant';
  private $strPortraitZombivant = 'portrait-zombivant';
  private $strPortraitUltimate  = ' portrait-ultimate';

  /**
   * @param Survivor $Survivor
   */
  public function __construct($Survivor=null)
  {
    parent::__construct();
    $this->Survivor = ($Survivor==null ? new Survivor() : $Survivor);
    $this->ExpansionServices = new ExpansionServices();
    $this->SurvivorServices  = new SurvivorServices();
  }
  /**
   * @return string
   */
  public function getRowForSurvivorsPage()
  {
    ///////////////////////////////////////////////////////////////
    // On enrichi le template et on le retourne.
    $args = array(
      // Id du Survivant - 1
      $this->Survivor->getId(),
      // Les portraits du Survivants - 2
      $this->getAllPortraits(),
      // Url du WpPost associé, s'il existe - 3
      $this->Survivor->getWpPostUrl(),
      // Nom du Survivant - 4
      $this->Survivor->getName(),
      // Si on a un profil de Zombivant, on donne la possibilité de l'afficher - 5
      ($this->Survivor->isZombivor()?self::CST_CHANGEPROFILE:''),
      // Si on a un profil de Zombivant, on veut une case à cocher - 6
      ($this->Survivor->isZombivor()?self::CST_SQUAREPOINTER:self::CST_WINDOWCLOSE),
      // Si on a un profil d'Ultimate, on donne la possibilité de l'afficher - 7
      ($this->Survivor->isUltimate()?self::CST_CHANGEPROFILE:''),
      // Si on a un profil d'Ultimate, on veut une case à cocher - 8
      ($this->Survivor->isUltimate()?self::CST_SQUAREPOINTER:self::CST_WINDOWCLOSE),
      // Extension à laquelle est rattaché le Survivant - 9
      $this->Survivor->getExpansionName(),
      // Liste des Compétences du Survivant - 10
      $this->getAllSkills(),
      // Background du Survivant - 11
      $this->Survivor->getBackground(),
    );
    return $this->getRender($this->urlRowPublic, $args);
  }
  /**
   * @return string
   */
  public function getContentForHome()
  {
    $args = array(
      // Url de l'article - 1
      $this->Survivor->getWpPostUrl(),
      // Url du portrait du Survivant - 2
      $this->Survivor->getPortraitUrl(),
      // Url vers la page Survivants - 3
      '/'.self::PAGE_SURVIVOR,
      // Nom du Survivant - 4
      $this->Survivor->getName(),
      // Les Compétences du Survivant - 5
      $this->Survivor->getUlSkills(),
      // Le Survivant a-t-il une version Zombivant ? - 6
      $this->Survivor->isZombivor() ? 'Oui' : 'Non',
      // Le Survivant a-t-il une version Ultimate ?  - 7
      $this->Survivor->isUltimate() ? 'Oui' : 'Non',
      // Background du Survivant - 8
      $this->Survivor->getBackground(),
      // Classe additionnelle de l'article - 9
      $this->Survivor->getStrClassFilters($isHome).' '.$this->Survivor->getExpansion()->getCode(),
      // Le Nom de l'extension - 10
      $this->Survivor->getExpansionName(),
    );
    return $this->getRender($this->urlArticle, $args);
  }

  public function getButton()
  {
    $str  = '<button type="button" class="btn btn-light btn-survivor hidden" data-expansion-id="'.$this->Survivor->getExpansionId();
    return $str.'" data-survivor-id="'.$this->Survivor->getId().'"><i class="far fa-square"></i> '.$this->Survivor->getName().'</button>';
  }







  /**
   * @return string
   */
  public function getRowForAdminPage()
  {
    $Survivor = $this->Survivor;
    $queryArgs = array(
      self::CST_ONGLET => self::CST_SURVIVOR,
      self::CST_POSTACTION => self::CST_EDIT,
      self::FIELD_ID =>$Survivor->getId()
    );
    $hrefEdit = $this->getQueryArg($queryArgs);
    $queryArgs[self::CST_POSTACTION] = self::CST_TRASH;
    $hrefTrash = $this->getQueryArg($queryArgs);
    $queryArgs[self::CST_POSTACTION] = self::CST_CLONE;
    $hrefClone = $this->getQueryArg($queryArgs);
    $urlWpPost = $Survivor->getWpPostUrl();
    ///////////////////////////////////////////////////////////////
    // On enrichi le template et on le retourne.
    $args = array(
      // Identifiant du Survivant
      $Survivor->getId(),
      // Url d'édition
      $hrefEdit,
      // Nom du Survivant
      $Survivor->getName(),
      // Url de suppression
      $hrefTrash,
      // Url de Duplication
      $hrefClone,
      // Article publié ?
      $urlWpPost!='#' ? '' : ' hidden',
      // Url Article
      $urlWpPost,
      //
      ($Survivor->isZombivor()?self::CST_CHANGEPROFILE:''),
      // Le Survivant a-t-il un profil Zombivant ?
      '<i class="far fa-'.($Survivor->isZombivor()?self::CST_SQUAREPOINTER:self::CST_WINDOWCLOSE).'"></i>',
      //
      ($Survivor->isUltimate()?self::CST_CHANGEPROFILE:''),
      // Le Survivant a-t-il un profil Ultimate ?
      '<i class="far fa-'.($Survivor->isUltimate()?self::CST_SQUAREPOINTER:self::CST_WINDOWCLOSE).'"></i>',
      // Extension de provenance
      $Survivor->getExpansionName(),
      // Background du Survivant
      $Survivor->getBackground(),
      // Nom de l'image alternative, si défini.
      $Survivor->getAltImgName(),
      // Portraits
      $this->getAllPortraits(),
      '', '', '', '', '', '', '',
    );
    return $this->getRender($this->urlRowAdmin, $args);
  }
  /**
   * @param string $color
   * @return string
   */
  public function getSkillBadge($color)
  {
    ////////////////////////////////////////////////////////////////////
    // On enrichi les paramètres du template et on le retourne
    $args = array(
      $color,
      $this->Survivor->getWpPostUrl(),
      $this->Survivor->getName()
    );
    return vsprintf(($this->Survivor->getWpPostUrl()=='#' ? $this->tplDisabledSkillBadge : $this->tplSkillBadge), $args);
  }
  /**
   * @return string
   */
  public function getAllPortraits()
  {
    $Survivor = $this->Survivor;
    $name = $Survivor->getName();
    $str  = $this->getStrImgPortrait($Survivor->getPortraitUrl(), 'Portrait Survivant - '.$name, $this->strPortraitSurvivant);
    if ($Survivor->isZombivor()) {
      $str .= $this->getStrImgPortrait($Survivor->getPortraitUrl('z'), 'Portrait Zombivant - '.$name, $this->strPortraitZombivant);
    }
    if ($Survivor->isUltimate()) {
      $label = $this->strPortraitSurvivant.$this->strPortraitUltimate;
      $str .= $this->getStrImgPortrait($Survivor->getPortraitUrl('u'), 'Portrait Ultimate - '.$name, $label);
      $label = $this->strPortraitZombivant.$this->strPortraitUltimate;
      $str .= $this->getStrImgPortrait($Survivor->getPortraitUrl('uz'), 'Portrait ZUltimate - '.$name, $label);
    }
    return $str;
  }
  /**
   * @param string $src
   * @param string $alt
   * @param string $addClass
   * @return string
   */
  private function getStrImgPortrait($src, $alt, $addClass)
  {
    $attributes = array(
      self::ATTR_SRC => $src,
      self::ATTR_ALT => $alt,
      self::ATTR_CLASS => 'thumb '.$addClass,
    );
    return $this->getBalise(self::TAG_IMG, '', $attributes);
  }
  /**
   * @return string
   */
  public function getAllSkills()
  {
    $Survivor = $this->Survivor;
    $str = $this->getSkillsBySurvivorType('skills-survivant row', $Survivor->getUlSkills('', true));
    if ($Survivor->isZombivor()) {
      $str .= $this->getSkillsBySurvivorType('skills-zombivant row', $Survivor->getUlSkills('z', true));
    }
    if ($Survivor->isUltimate()) {
      $str .= $this->getSkillsBySurvivorType('skills-ultimate skills-survivant row', $Survivor->getUlSkills('u', true));
      $str .= $this->getSkillsBySurvivorType('skills-ultimate skills-zombivant row', $Survivor->getUlSkills('uz', true));
    }
    return $this->getBalise(self::TAG_UL, $str);
  }
  /**
   * @param string $addClass
   * @param string $content
   * @return string
   */
  private function getSkillsBySurvivorType($addClass, $content)
  { return $this->getBalise(self::TAG_LI, $content, array(self::ATTR_CLASS=>$addClass)); }
  /**
   * @return string
   */
  public function getCheckBoxType()
  {
    $strType  = '';
    if ($this->Survivor->isZombivor()) {
      $attributes = array(
        self::ATTR_DATA_ID => $this->Survivor->getId(),
        self::ATTR_DATA_TYPE => 'zombivant',
        self::ATTR_CLASS => 'changeProfile',
      );
      $strType .= $this->getBalise(self::TAG_DIV, $this->getIconFarSquarePointer().' Zombivant', $attributes);
      if ($this->Survivor->isUltimate()) {
        $attributes[self::ATTR_DATA_TYPE] = self::FIELD_ULTIMATE;
        $strType .= '&nbsp;'.$this->getBalise(self::TAG_DIV, $this->getIconFarSquarePointer().' Ultimate', $attributes);
      }
    }
    return $strType;
  }




  /**
   * Utiliser dans la partie génération aléatoire d'une équipe.
   * @param string $addClass
   * @return string
   */
  public function getVisitCard($addClass='')
  {
    $Survivor = $this->Survivor;
    $name = $Survivor->getName();
    $args = array(
      $this->getStrImgPortrait($Survivor->getPortraitUrl(), 'Portrait Survivant - '.$name, $this->strPortraitSurvivant),
      $name,
      $this->getSkillsBySurvivorType('skills-survivant', $Survivor->getUlSkills()),
      ($addClass=='' ? '' : ' '.$addClass),
    );
    return $this->getRender($this->urlCardVisit, $args);
  }

}
