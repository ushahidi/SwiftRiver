<?php defined('SYSPATH') or die('No direct script access');

/**
 * Unit test for Links helper
 *
 * @see         Links
 * @package     Swiftriver
 * @category    Tests
 * @author      Ushahidi Team
 * @author      Emmanuel Kala <emmanuel(at)ushahidi.com>
 * @copyright   (c) 2008-2011 Ushahidi Inc
 * @license     For license information, see LICENSE file
 */

class Swiftriver_LinksTest extends Unittest_TestCase {
	
	/**
	 * Provides test data for test_extract
	 *
	 * @dataProvider
	 * @return array
	 */
	public function provider_extract()
	{
		return array(
			array(
				'http://t.co/Kx5OF1PC',
				'Maher, Maddow Talk Occupy Wall Street: Why Does No One Admit that Wealth is a "Fluke"? '
					. '| AlterNet: http://t.co/Kx5OF1PC via @AddThis #ows'
			)
		);
	}
	
	/**
	 * Tests Links::extract()
	 *
	 * @test
	 * @dataProvider provider_extract
	 * @param string $token URL that should exist in the list of extracted links
	 * @param string $text Input for Links::extract
	 */
	public function test_extract($token, $text)
	{
		$urls = Swiftriver_Links::extract($text);
		$this->assertContains($token, $urls, sprintf('%s not found in the list of extracted URLs', $token));
	}
	
	/**
	 * Provides data for test_full
	 *
	 * @return array
	 */
	public function provider_full()
	{
		return array(
			array(
				'http://www.alternet.org/newsandviews/article/679225/maher,_maddow_talk_occupy_wall_street:_why_does_no_one_'
					. 'admit_that_wealth_is_a_"fluke"/#.TpXsfp8w4j0.twitter',
				'http://t.co/Kx5OF1PC'
			)
		);
	}
	
	/**
	 * Tests Links::full
	 *
	 * @test
	 * @dataProvider provider_full
	 * @param string $full_url Expected output of the URL expansion
	 * @param string $short_url The URL to be expanded
	 */
	public function test_full($full_url, $short_url)
	{
		$result = Swiftriver_Links::full($short_url);
		$this->assertSame($full_url, $result, 'Returned URL does not match expected result');
	}
}
?>