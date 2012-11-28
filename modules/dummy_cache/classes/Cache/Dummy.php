<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Dummy cache driver that stores nothing and returns nothing
	 *
	 * PHP version 5
	 * LICENSE: This source file is subject to the AGPL license 
	 * that is available through the world-wide-web at the following URI:
	 * http://www.gnu.org/licenses/agpl.html
	 * @author      Ushahidi Team <team@ushahidi.com> 
	 * @package     Swiftriver - http://github.com/ushahidi/Swiftriver_v2
	 * @subpackage  Libraries
	 * @copyright   Ushahidi - http://www.ushahidi.com
	 * @license     http://www.gnu.org/licenses/agpl.html GNU Affero General Public License (AGPL) 
	 */
	class Cache_Dummy extends Cache {

		/**
		 * Retrieve a cached value entry by id.
		 *
		 * @param   string   id of cache to entry
		 * @param   string   default value to return if cache miss
		 * @return  mixed
		 * @throws  Kohana_Cache_Exception
		 */
		public function get($id, $default = NULL)
		{
			return $default;
		}

		/**
		 * Set a value to cache with id and lifetime
		 * 
		 * @param   string   id of cache entry
		 * @param   mixed    data to set to cache
		 * @param   integer  lifetime in seconds, default __3600__
		 * @return  boolean
		 */
		public function set($id, $data, $lifetime = 3600)
		{
			return TRUE;
		}

		/**
		 * Delete a cache entry based on id
		 * 
		 * @param   string   id of entry to delete
		 * @param   integer  timeout of entry, if zero item is deleted immediately, otherwise the item will delete after the specified value in seconds
		 * @return  boolean
		 */
		public function delete($id, $timeout = 0)
		{
			return TRUE;
		}

		/**
		 * Delete all cache entries.
		 * 
		 * @return  boolean
		 */
		public function delete_all()
		{
			return TRUE;
		}

	}
