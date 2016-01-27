<?php

use \Akky\EmbedWiderFlickrPlugin;

class RegexTest extends WP_UnitTestCase {

	/**
	 */
	public function testIsFlickrEmbeddable()
	{
		$this->assertTrue(EmbedWiderFlickrPlugin::isFlickrEmbeddable('https://www.flickr.com/photos/62081902@N00/24160524461/'));
		$this->assertFalse(EmbedWiderFlickrPlugin::isFlickrEmbeddable('https://www.youtube.com/watch?v=IN05jVNBs64'));
	}


	/**
	 */
	public function testIsShortenedFlickrEmbeddable()
	{
		$this->assertTrue(EmbedWiderFlickrPlugin::isFlickrEmbeddable('http://flic.kr/p/CNZ1bz'));
		$this->assertFalse(EmbedWiderFlickrPlugin::isFlickrEmbeddable('https://www.youtube.com/watch?v=IN05jVNBs64'));
	}

	/**
	 */
	public function testGetPhotoInfo()
	{
		$info = EmbedWiderFlickrPlugin::getPhotoInfo('<a href="https://www.flickr.com/photos/62081902@N00/24160524461/"><img src="https://farm2.staticflickr.com/1501/24160524461_54125b7466_n.jpg" alt="Nihonbashi lunch" width="320" height="180" /></a>');
		$this->assertEquals($info['src'], 'https://farm2.staticflickr.com/1501/24160524461_54125b7466_n.jpg');
		$this->assertEquals($info['alt'], 'Nihonbashi lunch');
	}

	/**
	 */
	public function testGetPhotoInfoRequireSanitize()
	{
		$info = EmbedWiderFlickrPlugin::getPhotoInfo('<a href="https://www.flickr.com/photos/62081902@N00/24160524461/"><img src="https://farm2.staticflickr.com/1501/24160524461_54125b7466_n.jpg" alt="><script>alert(\'hello\');</script><a name=" width="320" height="180" /></a>');
		$this->assertEquals($info['src'], 'https://farm2.staticflickr.com/1501/24160524461_54125b7466_n.jpg');
		$this->assertEquals($info['alt'], '&gt;&lt;script&gt;alert(&#039;hello&#039;);&lt;/script&gt;&lt;a name=');
	}

	/**
	 */
	public function testGetMidiumWidthPhotoUrl()
	{
		$url = EmbedWiderFlickrPlugin::getMidiumWidthPhotoUrl('https://farm2.staticflickr.com/1501/24160524461_54125b7466_n.jpg');
		$this->assertEquals($url, 'https://farm2.staticflickr.com/1501/24160524461_54125b7466.jpg');
	}


	const REGEX_APP_STORE = '#\Ahttps://www.windowsphone.com/(?P<lang>\w{2})-(?P<region>\w{2})/store/app/(?P<slug>[\w\d-%]*)/(?P<guid>[\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12})(?:\?.*)?\z#i';

	/**
   * test data definitions
	 */
	static function forTestMatches() {
			return array(
					// proper form of url
					array('https://www.windowsphone.com/en-us/store/app/texas-holdem-poker/f51dcd7a-5c19-481b-a19b-7124649237bc', true),
					// locale can be Capital
					array('https://www.windowsphone.com/en-US/store/app/texas-holdem-poker/f51dcd7a-5c19-481b-a19b-7124649237bc', true),
					// parameters ignored
					array('https://www.windowsphone.com/en-us/store/app/lara-croft-relic-run/4bfa010c-2e5f-4da8-86fa-03de83fb1ba3?signin=true', true),
					// another app
					array('https://www.windowsphone.com/en-us/store/app/koozac/227d63c4-0730-47d2-937c-b2e0a956b701', true),
					// ----------------------------------------------------

					// non SSL url is forwarded to SSL, so should not be matched?
					array('http://www.windowsphone.com/en-us/store/app/texas-holdem-poker/f51dcd7a-5c19-481b-a19b-7124649237bc', false),
					// no lang/country leads not found
					array('http://www.windowsphone.com/store/app/texas-holdem-poker/f51dcd7a-5c19-481b-a19b-7124649237bc', false),
					// no app name slug leads not found
					array('http://www.windowsphone.com/en-us/store/app/f51dcd7a-5c19-481b-a19b-7124649237bc', false),
					// no "www." falls 404
					array('http://windowsphone.com/en-us/store/app/texas-holdem-poker/f51dcd7a-5c19-481b-a19b-7124649237bc', false),

					// under the store, but not apps
					array('https://www.windowsphone.com/', false),
					array('https://www.windowsphone.com/ja-jp/store', false),
					array('https://www.windowsphone.com/how-to', false),
			);
	}

	/**
	 * test data if matches
	 *
	 * @dataProvider forTestMatches
	 */
/*	public function testRegex($value, $expected)
	{
			if ($expected) {
					$this->assertRegExp(self::REGEX_APP_STORE, $value);
			} else {
					$this->assertNotRegExp(self::REGEX_APP_STORE, $value);
			}
	}*/

	static function forTestMatchesFound() {
			return array(
					array('https://www.windowsphone.com/en-us/store/app/texas-holdem-poker/f51dcd7a-5c19-481b-a19b-7124649237bc',
							array(
									'lang' => 'en',
									'region' => 'us',
									'slug' => 'texas-holdem-poker',
									'guid' => 'f51dcd7a-5c19-481b-a19b-7124649237bc',
							)
					),

					array('https://www.windowsphone.com/en-us/store/app/koozac/227d63c4-0730-47d2-937c-b2e0a956b701',
							array(
									'lang' => 'en',
									'region' => 'us',
									'slug' => 'koozac',
									'guid' => '227d63c4-0730-47d2-937c-b2e0a956b701',
							)
					),

					// non-latin slug
					array('https://www.windowsphone.com/ja-jp/store/app/%E3%83%AA%E3%82%B3%E3%83%AA%E3%82%B9/486b3dab-7c21-4d6e-a2e5-cbab0c9ae762',
							array(
									'lang' => 'ja',
									'region' => 'jp',
									'slug' => '%E3%83%AA%E3%82%B3%E3%83%AA%E3%82%B9',
									'guid' => '486b3dab-7c21-4d6e-a2e5-cbab0c9ae762',
							)
					),

					// non-latin slug
					array('https://www.windowsphone.com/ja-jp/store/app/%E3%83%AA%E3%82%B3%E3%83%AA%E3%82%B9/486b3dab-7c21-4d6e-a2e5-cbab0c9ae762',
							array(
									'lang' => 'ja',
									'region' => 'jp',
									'slug' => '%E3%83%AA%E3%82%B3%E3%83%AA%E3%82%B9',
									'guid' => '486b3dab-7c21-4d6e-a2e5-cbab0c9ae762',
							)
					),

			);
	}

	/**
	 * test data matched parameters
	 *
	 * @dataProvider forTestMatchesFound
	 */
/*	public function testRegexMatched($value, $expected)
	{
			$result = preg_match(self::REGEX_APP_STORE, $value, $matched);
			$this->assertEquals($result, 1);
			$this->assertEquals($expected['lang'], $matched['lang']);
			$this->assertEquals($expected['region'], $matched['region']);
			$this->assertEquals($expected['slug'], $matched['slug']);
			$this->assertEquals($expected['guid'], $matched['guid']);
	}*/

}
