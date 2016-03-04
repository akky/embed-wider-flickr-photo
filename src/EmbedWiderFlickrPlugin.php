<?php

namespace Akky;

// by akky
// force Flickr to show larger size photo, instead of default 320px
// http://wordpress.stackexchange.com/questions/14434/add-filter-to-youtube-embeds
// http://wordpress.stackexchange.com/questions/77745/how-to-increase-image-size-returned-from-flickr-oembed-in-twenty-twelve-theme
//add_filter( 'embed_handler_html', 'embed_wider_flickr', 80, 4);
//add_filter('embed_oembed_html', 'embed_wider_flickr', 80, 4);

class EmbedWiderFlickrPlugin {
   public function __construct() {
    load_plugin_textdomain('embed-wider-flickr-plugin', false,
      dirname(plugin_basename(__FILE__)) . '/lang');

    add_filter('embed_oembed_html', array($this, 'hook'), 80, 4);
  }

  /**
   *
   * @SuppressWarnings("PHPMD.UnusedFormalParameter") $postId
   */
  public function hook($html, $url, $attr, $postId)
  {
    // WordPress's default embed width
    static $defaultWidth = 500;
    if (!self::isFlickrEmbeddable($url)) { return $html; }

    $info = self::getPhotoInfo($html);
    if (!$info) { return $html; }

    $largePhotoUrl = self::getMidiumWidthPhotoUrl($info['src']);

    $alt = '""';
    if (array_key_exists('alt', $info)) {
      $alt = $info['alt'];
    }

    $width = $defaultWidth;
    if (array_key_exists('width', $attr)) {
      $width = (integer)$attr['width'];
    }

    $generatedHtml = self::composeFlickrImageHtml($url, $largePhotoUrl, $alt, $width);

    return $generatedHtml;
  }

  /**
   * @return true if the oembed URL ends with 'flickr.com'
   */
  public static function isFlickrEmbeddable($url) {
//  var_dump($url);
    $urlHost = parse_url($url, PHP_URL_HOST);
    return (
      strcmp(substr($urlHost, -strlen('flickr.com')),'flickr.com') === 0
      || strcmp($urlHost, 'flic.kr') === 0
    );
  }

  /**
   * parse Flickr's oemebd generated HTML
   *
   * @return array of gotten attributes
   */
  public static function getPhotoInfo($html) {
    // parse gotten HTML code for oembed
    $gotten = null;
    $results = preg_match_all(
      '/\s(?<name>alt|title|src)="(?<value>[^"]*)"/i',
      $html,
      $gotten
    );
    if ($results === 0 || $results === false) { return false; }

    // Although data taken from Flickr oembed API may not be dangerous...
    foreach ($gotten['name'] as $index => $curName) {
      $curValue = $gotten['value'][$index];
      switch ($curName) {
      case 'src':
        $info['src'] = filter_var($curValue, FILTER_SANITIZE_URL);
        break;
      default:
        $info[$curName] = filter_var($curValue, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      }
    }
    return $info;
  }

  public static function getMidiumWidthPhotoUrl($thumbnailUrl) {
    return str_replace('_n.jpg', '.jpg', $thumbnailUrl);
  }

  public static function composeFlickrImageHtml($url, $src, $alt, $width) {
    return "<a href=\"{$url}\"><img src=\"$src\" alt=\"$alt\"  style='width:100%; max-width: " . $width . "px;' /></a>";
  }
}
