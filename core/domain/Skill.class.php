<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe Skill
 * @author Hugues.
 * @since 1.00.00
 * @version 1.05.01
 */
class Skill extends LocalDomain
{
  /**
   * Id technique de la donnée
   * @var int $id
   */
  protected $id;
  /**
   * Code de la donnée
   * @var string $code
   */
  protected $code;
  /**
   * Nom de la donnée
   * @var string $name
   */
  protected $name;
  /**
   * Description de la donnée
   * @var string $description
   */
  protected $description;
  /**
   * Extension de la compétence (première apparition ou derni_ère modification)
   * @var int $expansionId
   */
  protected $expansionId;
  /**
   * @return int
   */
  public function getId()
  { return $this->id; }
  /**
   * @return string
   */
  public function getCode()
  { return $this->code; }
  /**
   * @return string
   */
  public function getName()
  { return $this->name; }
  /**
   * @return string
   */
  public function getDescription()
  { return $this->description; }
  /**
   * @return int
   */
  public function getExpansionId()
  { return $this->expansionId; }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id=$id; }
  /**
   * @param string $code
   */
  public function setCode($code)
  { $this->code=$code; }
  /**
   * @param string $name
   */
  public function setName($name)
  { $this->name=$name; }
  /**
   * @param string $description
   */
  public function setDescription($description)
  { $this->description=$description; }
  /**
   * @param int $official
   */
  public function setExpansionId($expansionId)
  { $this->expansionId = $expansionId; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('Skill'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return Skill
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new Skill(), self::getClassVars(), $row); }
  /**
   * @return string
   */
  public function getWpPostUrl()
  {
    $args = array(
      self::WP_METAKEY   => self::FIELD_CODE,
      self::WP_METAVALUE => $this->getCode(),
      self::WP_TAXQUERY  => array(),
      self::WP_CAT       => self::WP_CAT_SKILL_ID,
    );
    $WpPosts = $this->WpPostServices->getArticles($args);
    if (!empty($WpPosts)) {
      $WpPost = array_shift($WpPosts);
      $url = $WpPost->getPermalink();
    } else {
      $url = '/page-competences/?skillId='.$this->id;
    }
    return $url;
  }
  /**
   * @return SkillBean
   */
  public function getBean()
  { return new SkillBean($this); }

  /**
   * @version 1.04.27
   * @return WpPost
   */
  public function getArticle()
  {
    $WpPosts = $this->WpPostServices->getWpPostsByCustomField(self::FIELD_CODE, $this->getCode());
    return (empty($WpPosts) ? new WpPost() : array_shift($WpPosts));
  }

  public function getExpansion()
  {
    if ($this->Expansion==null) {
      $this->Expansion = $this->ExpansionServices->selectExpansion($this->getExpansionId());
    }
    return $this->Expansion;
  }

  /**
   * @version 1.04.27
   * @return string
   */
  public function getEditUrl()
  {
    $WpPost = $this->getArticle();
    if ($WpPost->getID()=='') {
      $queryArgs = array(
        self::CST_ONGLET     => self::CST_SKILL,
        self::CST_POSTACTION => self::CST_EDIT,
        self::FIELD_ID       => $this->getId()
      );
      $hrefEdit = $this->getQueryArg($queryArgs);
    } else {
      $hrefEdit = 'http://zombicidev2.jhugues.fr/wp-admin/post.php?post='.$WpPost->getID().'&action=edit';
    }
    return $hrefEdit;
  }
}
