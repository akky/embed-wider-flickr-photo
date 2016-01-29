<?php

use \Akky\EmbedWiderFlickrPlugin;

class RegexTest extends PHPUnit_Framework_TestCase {

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
}
