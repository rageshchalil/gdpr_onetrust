<?php

namespace Gdpr\Gdpr;


class Gdpronetrust
{
  const GDPR_ONETRUST_UUID_REGEX =
    '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}(-test){0,1}$/i';
  const GDPR_ONETRUST_LIVE_DOMAIN = '//cdn.cookielaw.org/consent/';
  const GDPR_ONETRUST_STAGE_DOMAIN =
    'https://optanon.blob.core.windows.net/consent/';

  private $insertscript;
  private $inserthtml;
  private $valid_gdpr_uuid = FALSE;

  /**
   * Implement class construct.
   */
  private function __construct() {

  }

  /**
   * Create an singleton factory object.
   */
  public static function instance() {
    static $inst = NULL;
    if ($inst === NULL) {
      $inst = new Gdpronetrust();
    }
    return $inst;
  }

  /**
   * Set the header with the cdn domain.
   * @param $uuid
   *  UUID procured from OneTrust
   * @return string
   *  The OneTrust url api url.
   */
  public function setCdn($uuid) {

    if (!empty($uuid) && ($this->isvalid_uuid($uuid) == TRUE)) {
      $this->valid_gdpr_uuid = TRUE;
      $ret = preg_match(self::GDPR_ONETRUST_UUID_REGEX, $uuid, $matches);

      if (isset($matches[1]) && $matches[1] == '-test') {
        $domain = self::GDPR_ONETRUST_STAGE_DOMAIN;
      }
      else {
        $domain = self::GDPR_ONETRUST_LIVE_DOMAIN;
      }

      $gdpr_api_url = $domain . $uuid . '.js';
      return '<script charset="UTF-8" src="' . $gdpr_api_url . '"></script>';
    }
  }

  /**
   * Check if UUID is valid .
   * @param $uuid
   * @return bool
   */
  private function isvalid_uuid($uuid) {

    if (!empty($uuid) && preg_match(self::GDPR_ONETRUST_UUID_REGEX, $uuid) !== 1) {
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  /**
   * @param string $js_path
   *  The javascript file path
   * @param string $selector
   *  The location to load the js 'head', 'body', '<parent id>'
   * @param $callback
   *  A JavaScript function to be called once the <script> tag has been inserted
   * @param $options
   *  A list of behaviours for when the <script> tag is inserted
   * @param $js_group
   *  Group id for which the <script> tag will be inserted
   * @param bool $inline
   *  Set TRUE if js should be loaded inline, ie. outside optanon wrapper.
   * @return string
   */
  public function optanonInsertscript($js_path, $selector = "head", $callback,
                                      $options, $js_group, $inline = FALSE) {

    $callback = (!empty($callback)) ? $callback : 'null';
    $options = (!empty($options)) ? $options : 'null';

    $insert_script = 'Optanon.InsertScript(\'' . $js_path . '\', \'' .
      $selector . '\', ' . $callback . ', ' . $options . ', ' .
      $js_group . ' ); ';

    if ($inline === TRUE) {
      return $insert_script;
    }
    else {
      $this->insertscript .= $insert_script;
    }
  }

  /**
   * @param $element
   *  Html tag to be inserted
   * @param $selector
   *  Html parent element id where the element will be inserted
   * @param $callback
   *  A javascript function to be called once the element has been inserted
   * @param $options
   *  A list of behaviours for when the element is inserted
   * @param $groupid
   *  Group id for which the element will be inserted.
   * @param bool $inline
   *  Set TRUE if js should be loaded inline, ie. outside optanon wrapper.
   * @return string
   */
  public function optanonInserthtml($element, $selector, $callback, $options, $groupid, $inline = FALSE) {

    $callback = (!empty($callback)) ? $callback : 'null';
    $options = (!empty($options)) ? $options : 'null';

    $insert_html = 'Optanon.InsertHtml(\'' . $element . '\', \'' . $selector .
      '\',  ' . $callback . ',  ' . $options . ', ' . $groupid . '); ';

    if ($inline === TRUE) {
      return $insert_html;
    }
    else {
      $this->inserthtml .= $insert_html;
    }
  }

  /**
   * Implement the optanon wrapper function to page head section.
   * @return string
   *  The optanon wrapper with the javascript and htmls to be under consent.
   */
  public function optanonPublishScript() {
    if ($this->valid_gdpr_uuid == TRUE) {
      return '<script type="text/javascript"> function OptanonWrapper() { ' . $this->insertscript . $this->inserthtml . '} </script>';
    }
  }

}