<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SurvivorBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.05.10
 */
class SurvivorBean extends LocalBean
{
  protected $urlRowAdmin  = 'web/pages/admin/fragments/survivor-row.php';
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
      $this->Survivor->getExpansion()->getName(),
      // Liste des Compétences du Survivant - 10
      $this->getAllSkills(),
      // Background du Survivant - 11
      $this->Survivor->getBackground(),
      // Classe additionnelle, pour la ligne - 12
      (!$this->Survivor->isStandard() ? ' ultimate' : ''),
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
      $this->Survivor->getExpansion()->getName(),
    );
    return $this->getRender($this->urlArticle, $args);
  }
  /**
   * @return string
   */
  public function getButton()
  {
    $label = $this->getIconFarSquare().' '.$this->Survivor->getName();
    $attributes = array(
      self::ATTR_TYPE             => self::TAG_BUTTON,
      self::ATTR_CLASS            => 'btn btn-light btn-survivor hidden',
      self::ATTR_DATA_EXPANSIONID => $this->Survivor->getExpansionId(),
      self::ATTR_DATA_SURVIVORID  => $this->Survivor->getId(),
    );
    return $this->getBalise(self::TAG_BUTTON, $label, $attributes);
  }







  /**
   * @return string
   */
  public function getRowForAdminPage()
  {
    $wpPostId = $this->Survivor->getWpPost()->getID();
    if ($wpPostId=='') {
      $hrefEditWpPost = '/wp-admin/post-new.php';
      $labelEditWpPost = 'Créer';
    } else {
      $hrefEditWpPost = '/wp-admin/post.php?post='.$this->Survivor->getWpPost()->getID().'&action=edit';
      $labelEditWpPost = 'Modifier';
    }
    $queryArgs = array(
      self::CST_ONGLET => self::CST_SURVIVOR,
      self::CST_POSTACTION => self::CST_EDIT,
      self::FIELD_ID =>$this->Survivor->getId()
    );
    $urlWpPost = $this->Survivor->getWpPostUrl();
    ///////////////////////////////////////////////////////////////
    // On enrichi le template et on le retourne.
    $args = array(
      // Identifiant du Survivant - 1
      $this->Survivor->getId(),
      // Portraits - 2
      $this->getAllPortraits(),
      // Url d'édition - 3
      $this->getQueryArg($queryArgs),
      // Nom du Survivant - 4
      $this->Survivor->getName(),
      // Url d'édition du WpPost - 5
      $hrefEditWpPost,
     // Article publié ? - 6
      $urlWpPost!='#' ? '' : ' hidden',
      // Url de l'Article - 7
      $urlWpPost,
      // Libellé de l'action sur le WpPost - 8
      $labelEditWpPost,
      // Liste des profils existants - 9
      $this->getListeProfils(),
      /*
      // - 8
      ($this->Survivor->isZombivor() ? self::CST_CHANGEPROFILE : ''),
      // Le Survivant a-t-il un profil Zombivant ? - 9
      ($this->Survivor->isZombivor() ? $this->getIconFarSquarePointer() : $this->getIconFarWindowClose()),
      // - 10
      ($this->Survivor->isUltimate() ? self::CST_CHANGEPROFILE : ''),
      // Le Survivant a-t-il un profil Zombivant ? - 11
      ($this->Survivor->isUltimate() ? $this->getIconFarSquarePointer() : $this->getIconFarWindowClose()),
      */
      // Extension de provenance - 10
      $this->Survivor->getExpansion()->getName(),
      // Background du Survivant - 11
      ($this->Survivor->getBackground()!='' ? substr($this->Survivor->getBackground(), 0, 50).'...' : 'Non renseigné'),
      // Nom de l'image alternative, si défini. - 12
      $this->Survivor->getAltImgName(),
      // Type du Survivant - 13
      (!$this->Survivor->isStandard() ? ' ultimate' : ''),
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
  public function getAllPortraits($displayedFiltered=true)
  {
    $Survivor = $this->Survivor;
    $name = $Survivor->getName();
    $str = '';
    if ($Survivor->isStandard()) {
      $str .= $this->getStrImgPortrait($Survivor->getPortraitUrl(), 'Portrait Survivant - '.$name, ($displayedFiltered?$this->strPortraitSurvivant:''));
    }
    if ($Survivor->isZombivor()) {
      $str .= $this->getStrImgPortrait($Survivor->getPortraitUrl('z'), 'Portrait Zombivant - '.$name, ($displayedFiltered?$this->strPortraitZombivant:''));
    }
    if ($Survivor->isUltimate()) {
      $label = $this->strPortraitSurvivant.$this->strPortraitUltimate;
      $str .= $this->getStrImgPortrait($Survivor->getPortraitUrl('u'), 'Portrait Ultimate - '.$name, ($displayedFiltered?$label:''));
    }
    if ($Survivor->isUltimatez()) {
      $label = $this->strPortraitZombivant.$this->strPortraitUltimate;
      $str .= $this->getStrImgPortrait($Survivor->getPortraitUrl('uz'), 'Portrait ZUltimate - '.$name, ($displayedFiltered?$label:''));
    }
    return $str;
  }
  private function getProfileLi($type, $survivorTypeId, $label)
  {
    $strProfils = '<li data-id="'.$this->Survivor->getId().'" data-type="'.$type.'" class="hasTooltip pointer"> ';
    if ($this->Survivor->areDataSkillsOkay($survivorTypeId)) {
      $strProfils .= $this->getIconFarCheckSquare().' '.$label.' <div class="tooltip">';
      $strProfils .= $this->Survivor->getAdminUlSkills($survivorTypeId).'</div>';
    } else {
      $strProfils .=  $this->getIconFarWindowClose().' '.$label;
    }
    return $strProfils.'</li>';
  }
  public function getListeProfils()
  {
    $strProfils  = '<ul>';
    // A-t-il un profil Standard ?
    if ($this->Survivor->isStandard()) {
      $strProfils .= $this->getProfileLi('survivant', self::CST_SURVIVORTYPEID_S, 'Standard');
    }
    // A-t-il un profil Zombivant ?
    if ($this->Survivor->isZombivor()) {
      $strProfils .= $this->getProfileLi('zombivant', self::CST_SURVIVORTYPEID_Z, 'Zombivant');
    }
    // A-t-il un profil Ultimate ?
    if ($this->Survivor->isUltimate()) {
      $strProfils .= $this->getProfileLi('ultimate survivant', self::CST_SURVIVORTYPEID_U, 'Ultimate');
    }
    // A-t-il un profil UltimateZ ?
    if ($this->Survivor->isUltimatez()) {
      $strProfils .= $this->getProfileLi('ultimate zombivant', self::CST_SURVIVORTYPEID_UZ, 'Ultimate Zombivant');
    }
    return $strProfils.'</ul>';
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



}
