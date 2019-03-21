<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerFile extends ComKoowaControllerView
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'view' => 'files'
        ));

        parent::_initialize($config);
    }

	public function getView()
	{
		$view    = parent::getView();
		$request = $this->getRequest();

		if ($request->query->callback && $request->query->layout === 'select') {
			$view->callback = $request->query->callback;
		}

		$query = $this->getRequest()->getQuery();

		if ($container = $query->container) {
            $view->getConfig()->container = $container;
        }

		return $view;
	}
}
