<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;

class JFormFieldFolders extends JFormField
{
	protected $type = 'Folders';

	protected function getInput()
	{
        if (!class_exists('Koowa')) {
            return '';
        }

		$value   = $this->value;
		$name    = $this->name;

        $id         = isset($this->element['id']) ? (string) $this->element['id'] : 'fileman_folders_select2';
        $show_root  = isset($this->element['show_root']) ? ((string)$this->element['show_root']) : true;
        $url_encode = (bool) $this->element['url_encode'];

        $selected = htmlspecialchars(($url_encode ? rawurlencode($value) : $value), ENT_QUOTES);

        $string = "
        <?= helper('ui.load', array('styles' => array('file' => 'component'))); ?>
        <?= helper('com://admin/fileman.listbox.folders', array(
            'select2' => true,
            'url_encode'  => \$url_encode,
            'name' => \$name,
            'showroot' => \$show_root,
            'selected' => \$selected,
            'attribs'  => array(
                'id' => \$id
            )
        )); ?>";

        $string .= "
        <script>
            kQuery(function($){
                $('#s2id_<?= \$id ?>').show();
                $('#<?= \$id ?>_chzn').remove();
            });
        </script>
        ";

        KObjectManager::getInstance()->getObject('translator')->load('com://admin/fileman');

        $view = KObjectManager::getInstance()->getObject('com://admin/fileman.view.default.html');
        $template = $view->getTemplate()
            ->addFilter('style')
            ->addFilter('script');

        return $template->loadString($string, 'php')
            ->render(array(
                'id'       => $id,
                'name'     => $name,
                'show_root' => $show_root,
                //'options'       => $options,
                'selected' => $selected,
                'url_encode' => $url_encode
            ));
	}
}