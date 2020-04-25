<?php
if (!defined('ABSPATH')) {
  die('Forbidden');
}
/**
 * Classe Rule
 * @author Hugues.
 * @since 1.04.08
 * @version 1.04.08
 */
class Rule extends LocalDomain
{
  /**
   * Id technique de la donnée
   * @var int $id
   */
  protected $id;
  /**
   * Eventuel Settings
   * @var bool $setting
   */
  protected $setting;
  /**
   * Code de la Règle
   * @var string $code
   */
  protected $code;
  /**
   * Description de la Règle
   * @var string $description
   */
  protected $description;
  /**
   * @return int
   */
  public function getId()
  { return $this->id; }
  /**
   * @return int
   */
  public function getSetting()
  { return $this->setting; }
  /**
   * @return string
   */
  public function getCode()
  { return $this->code; }
  /**
   * @return string
   */
  public function getDescription()
  { return $this->description; }
  /**
   * @param int $id
   */
  public function setId($id)
  { $this->id=$id; }
  /**
   * @param int $setting
   */
  public function setSetting($setting)
  { $this->setting=$setting; }
  /**
   * @param string $code
   */
  public function setCode($code)
  { $this->code=$code; }
  /**
   * @param string $description
   */
  public function setDescription($description)
  { $this->description=$description; }
  /**
   * @return array
   */
  public function getClassVars()
  { return get_class_vars('Rule'); }
  /**
   * @param array $row
   * @param string $a
   * @param string $b
   * @return Rule
   */
  public static function convertElement($row, $a='', $b='')
  { return parent::convertElement(new Rule(), self::getClassVars(), $row); }
  /**
   * @return RuleBean
   */
  public function getBean()
  { return new RuleBean($this); }

}
