<?php
/**
 * @copyright Copyright &copy; 2015
 * @license http://gnu.org/licenses/gpl-2.0.html GNU/GPL Version 2
 * @author joomla.cn
 * @link joomla.cn
 */
defined('_JEXEC') or die;

class Pkg_DogecoinTippingInstallerScript
{
	public function postflight($type, $parent)
	{
        if ($type == 'uninstall') {
            return true;
        }

		$manifest = $parent->getManifest();
		$this->prepareExtensions($manifest, 1);
		return true;		
	}
	
	protected function prepareExtensions($manifest, $state = 1)
	{
		foreach($manifest->files->children() as $file) {
			$attributes = $file->attributes();
			$search = array(
				'type' => (string)$attributes->type,
				'element' => (string)$attributes->id
			);
            $clientName = (string) $attributes->client;
            if (!empty($clientName)) {
                $client = JApplicationHelper::getClientInfo($clientName, true);
                $search +=  array('client_id' => $client->id);
            }

            $group = (string) $attributes->group;
            if (!empty($group)) {
                $search +=  array('folder' => $group);
            }

            $extension = JTable::getInstance('extension');

            if (!$extension->load($search)) {
                continue;
            }

            $extension->enabled = $state;
            $extension->store();			
		}
	}
}
