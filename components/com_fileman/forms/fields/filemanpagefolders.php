<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

JFormHelper::loadFieldClass('groupedlist');

class JFormFieldFilemanpagefolders extends JFormField
{
    protected $type = 'Filemanpagefolders';

    protected function getInput()
    {
        if (!class_exists('Koowa')) {
            return '';
        }

        $value   = $this->value;
        $name    = $this->name;

        $id    = isset($this->element['id']) ? (string) $this->element['id'] : 'fileman_folders_select2';
        $pages = isset($this->element['pages']) ? (string) $this->element['pages'] : 'fileman_pages_select2';

        $url_encode = (bool) $this->element['url_encode'];

        $manager = KObjectManager::getInstance();

        $data = $this->form->getData();

        $page = 0;

        if ($data->exists('params')) {
            $page = $data->get('params')->page;
        }

        $page = $manager->getObject('com://admin/fileman.model.pages')->id($page)->fetch();

        $string = "<?= helper('ui.load', array('styles' => array('file' => 'component'))); ?>
            <?= helper('com://admin/fileman.listbox.folders', array(
                'select2' => true,
                'url_encode' => \$url_encode,
                'name'       => \$name,
                'showroot'   => \$show_root,
                'selected'   => \$selected,
                'attribs'    => array(
                    'id'         => \$id),
                'page'       => \$page
            ));?>
        ";

        $selected = htmlspecialchars(($url_encode ? rawurlencode($value) : $value), ENT_QUOTES);

        $string .= "
        <script>
            kQuery(function($){
                $('#s2id_<?= \$id ?>').show();
                $('#<?= \$id ?>_chzn').remove();
                
                var base_url = '" . JRoute::_('index.php?option=com_fileman&view=folder&layout=select&field=' . urldecode($name) . '&page=', false) . "'
                
                var selectHandler = function(select)
                {
                      var url = base_url + select.val()  
                
                      $.ajax({
                        url: url,
                        method: 'GET',
                        success: function(data)
                        { 
                            var container = $('#<?=\$id?>').closest('.control-group');
                            
                            $('#<?=\$id?>').parent('div').html($('#container', data).html());
                            $('#<?=\$id?>').select2({'allowClear':false,'theme':'bootstrap','width':'resolve'});
                            
                            container.show();
                        }
                      });  
                };
                                
                $('#" . $pages . "').on('select2:select', function(e) {
                       selectHandler($(this));
                });

                $('#" . $pages . "').on('select2:unselect', function(e) {
                       selectHandler($(this));
                });
                
                if (" . ($page->isNew() ? 'true' : 'false') . ") {
                    selectHandler($('#" . $pages . "'));
                }
            });
        </script>
        ";

        $manager->getObject('translator')->load('com://admin/fileman');

        $view = KObjectManager::getInstance()->getObject('com://admin/fileman.view.default.html');
        $template = $view->getTemplate()
                         ->addFilter('style')
                         ->addFilter('script');

        return $template->loadString($string, 'php')
                        ->render(array(
                            'page'       => $page->id,
                            'id'         => $id,
                            'name'       => $name,
                            'show_root'  => true,
                            //'options'       => $options,
                            'selected'   => $selected,
                            'url_encode' => $url_encode
                        ));
    }
}
