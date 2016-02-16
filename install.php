<?php
/**
 * Part of Component Akquickicons files.
 *
 * @copyright   Copyright (C) 2014 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Script file of Akquickicons component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_akquickicons
 */
class Mod_flexigooglemapInstallerScript
{
	/**
	 * Method to install the component.
	 *
	 * @param JInstallerAdapterComponent $parent
	 *
	 * @return  void
	 */
	public function install(\JInstallerAdapterModule $parent)
	{
		// Set Default datas with asset.
		$db = JFactory::getDbo();

		$p_installer = $parent->getParent();
		$path        = $p_installer->getPath('source');

		jimport('joomla.filesystem.folder');
		$origin = $path . 'assets/marker';
		$target = JPATH_ROOT . '/images/marker';

		if (!is_dir($target))
		{
			if (JFolder::copy($origin, $target))
			{
				JFactory::getApplication()->enqueueMessage('Copy icons to: ' . $target, 'message');
			}
		}
		else
		{
			JFactory::getApplication()->enqueueMessage('images/marker folder has exists.', 'warning');
		}

		
	}

	/**
	 * Method to uninstall the component.
	 *
	 * @param JInstallerAdapterComponent $parent
	 *
	 * @return  void
	 */
	public function uninstall(\JInstallerAdapterModule $parent)
	{
	}

	/**
	 * Method to update the component
	 *
	 * @param JInstallerAdapterComponent $parent
	 *
	 * @return  void
	 */
	public function update(\JInstallerAdapterModule $parent)
	{
	}

	/**
	 * ethod to run before an install/update/uninstall method
	 *
	 * @param string                     $type
	 * @param JInstallerAdapterComponent $parent
	 *
	 * @return  void
	 */
	public function preflight($type, \JInstallerAdapterModule $parent)
	{
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param string                     $type
	 * @param JInstallerAdapterComponent $parent
	 *
	 * @return  void
	 */
	public function postflight($type, \JInstallerAdapterModule $parent)
	{
    }
}
