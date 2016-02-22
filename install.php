<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * Script file of HelloWorld module
 */
class mod_flexigooglemapInstallerScript
{
	/**
	 * Method to install the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	function install($parent) 
	{
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
		echo '<p>The module has been installed</p>';
	}
 
	/**
	 * Method to uninstall the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		echo '<p>The module has been uninstalled</p>';
	}
 
	/**
	 * Method to update the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	function update($parent) 
	{
		//echo '<p>The module has been updated to version' . $parent->get('manifest')->version) . '</p>';
	}
 
	/**
	 * Method to run before an install/update/uninstall method
	 * $parent is the class calling this method
	 * $type is the type of change (install, update or discover_install)
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		echo '<p>Anything here happens before the installation/update/uninstallation of the module</p>';
	}
 
	/**
	 * Method to run after an install/update/uninstall method
	 * $parent is the class calling this method
	 * $type is the type of change (install, update or discover_install)
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		echo '<p>Anything here happens after the installation/update/uninstallation of the module</p>';
	}
}