<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe SkillBean
 * @author Hugues
 * @since 1.00.00
 * @version 1.05.12
 */
class SkillBean extends LocalBean
{
  protected $urlRowAdmin  = 'web/pages/admin/fragments/skill-row.php';
  protected $urlRowPublic = 'web/pages/public/fragments/skill-row.php';
  /**
   * @param Skill $Skill
   */
  public function __construct($Skill=null)
  {
    parent::__construct();
    $this->Skill = ($Skill==null ? new Skill() : $Skill);
    $this->SurvivorSkillServices = new SurvivorSkillServices();
  }

  //////////////////////////////////////////////////////////////////////////
  // Différentes modes de présentation
  /**
   * @return string
   */
  public function getRowForAdminPage()
  {
    ///////////////////////////////////////////////////////////////
    // On enrichit le template et on le retourne.
    $args = array(
      // Identifiant de la Competence - 1
      $this->Skill->getId(),
      // Code de la Compétence - 2
      $this->Skill->getCode(),
      // Url d'édition du WpPost - 3
      $this->Skill->getWpPostEditUrl(),
      // Url d'édition BDD - 4
      $this->Skill->getEditUrl(self::CST_SKILL),
      // Url publique de l'Article - 5
      $this->Skill->getWpPostUrl(),
      // Nom de la Compétence - 6
      $this->Skill->getName(),
      // Description du Skill - 7
      $this->Skill->getDescription(),
      // Officiel ou non ? - 8
      ($this->Skill->getExpansion()->isOfficial() ? 'Oui' : 'Non'),
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
      $this->Skill->getWpPostUrl(),
      // Nom de la Compétence - 2
      $this->Skill->getName(),
      // Nombre de Compétences possédées par un Survivant, un Zombivant ou un Ultimate par niveau - 3
      $this->getSkillCartouches(),
      // Description de la Compétence - 4
      $this->Skill->getDescription(),
      // Identifiant de la Compétence - 5
      $this->Skill->getId(),
    );
    return $this->getRender($this->urlRowPublic, $args);
  }

















  private function getSkillCartouches()
  {
    ///////////////////////////////////////////////////////////////
    // Construction des éléments de la colonne Niveau
    $strSkillsCartouches = '';
    $arrTags = array(
      self::LVL_BLUE   => array(10, 11),
      self::LVL_YELLOW => array(20),
      self::LVL_ORANGE => array(30, 31),
      self::LVL_RED    => array(40, 41, 42),
    );
    $arrLvls = array(1=>'S', 2=>'Z', 3=>'U', 4=>'UZ');
    foreach ($arrTags as $key => $value) {
      foreach ($arrLvls as $k => $v) {
        $nb = $this->getNbSkillsByTag($value, $k);
        if ($nb!=0) {
          $strSkillsCartouches .= $this->getBalise(self::TAG_SPAN, $v.' : '.$nb, array(self::ATTR_CLASS=>'badge badge-'.$key.'-skill'));
        }
      }
    }
    return $strSkillsCartouches;
  }
  /**
   * @param array $arrTags Liste des tags dont on veut le nombre de couples SurvivorSkill
   * @return int
   */
  private function getNbSkillsByTag($arrTags, $type=1)
  {
    $arrFilters = array(
      self::FIELD_SKILLID        => $this->Skill->getId(),
      self::FIELD_SURVIVORTYPEID => $type,
    );
    $nb = 0;
    // Pour chaque tag, on fait une recherche en base et on cumule le nombre que l'on renvoie.
    while (!empty($arrTags)) {
      $arrFilters[self::FIELD_TAGLEVELID] = array_shift($arrTags);
      $SurvivorSkills = $this->SurvivorSkillServices->getSurvivorSkillsWithFilters($arrFilters);
      $nb += count($SurvivorSkills);
    }
    return $nb;
  }
}
