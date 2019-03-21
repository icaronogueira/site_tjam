<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Impressions Model
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Component\LOGman
 */
class ComLogmanModelImpressions extends KModelDatabase
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
             ->insert('row', 'string')
             ->insert('name', 'string')
             ->insert('package', 'string')
             ->insert('session', 'cmd')
             ->insert('internal', 'int')
             ->insert('package_name', 'cmd');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array('behaviors' => array('dateable', 'groupable')));

        parent::_initialize($config); // TODO: Change the autogenerated stub
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        $state = $this->getState();

        if ($row = $state->row) {
            $query->where('tbl.row IN :row')->bind(array('row' => (array) $row));
        }

        if ($name = $state->name) {
            $query->where('tbl.name IN :name')->bind(array('name' => (array) $name));
        }

        if ($package = $state->package) {
            $query->where('tbl.package IN :package')->bind(array('package' => (array) $package));
        }

        if ($session = $state->session) {
            $query->where('tbl.session IN :session')->bind(array('session' => (array) $session));
        }

        if (is_numeric($state->internal)) {
            $query->where('tbl.internal = :internal')->bind(array('internal' => $state->internal));
        }

        if ($package_name = $state->package_name)
        {
            $package_name = (array) $package_name;

            $conditions = array();
            $i          = 0;

            foreach ($package_name as $item)
            {
                list($package, $name) = explode('.', $item);

                $conditions[] = '(tbl.package = :package' . $i . ' AND tbl.name = :name' . $i . ')';

                $query->bind(array(sprintf('package%s', $i) => $package, sprintf('name%s', $i) => $name));

                $i++;
            }

            if (count($conditions)) {
                $query->where(sprintf('(%s)', implode(' OR ' , $conditions)));
            }
        }

        parent::_buildQueryWhere($query);
    }

    protected function _actionReset(KModelContext $context)
    {
        $properties = array('entity', 'count', 'views', 'visitors', 'viewsPerVisit');

        foreach ($properties as $property) {
            $this->{'_' . $property} = null;
        }
    }

    /**
     * Initializes the model.
     *
     * It resets both the model and its state and sets a new state if available.
     *
     * @return $this
     */
    public function initialize(KModelStateInterface $state = null)
    {
        $this->reset();
        $this->getState()->reset();

        if ($state) {
            $this->setState($state->getValues());
        }

        return $this;
    }

    /**
     * Provides the number of views
     *
     * @return int The number of views
     */
    final public function views()
    {
        if(!isset($this->_views))
        {
            $context = $this->getContext();
            $context->views  = null;

            if ($this->invokeCommand('before.fetch', $context) !== false)
            {
                $context->views = $this->_actionViews($context);
                $this->invokeCommand('after.fetch', $context);
            }

            $this->_views = $context->views;
        }

        return $this->_views;
    }


    protected function _actionViews(KModelContextInterface $context)
    {
        $context->query
            ->table(array('tbl' => 'logman_impressions'))
            ->columns(array('total' => 'COUNT(*)'))
            ->group('session_hash')
            ->limit(0);

        $this->_buildQueryWhere($context->query);

        $query = $this->getObject('lib:database.query.select')
                      ->columns('COALESCE(SUM(inner.total), 0)')->table(array('inner' => $context->query));

        return $this->getTable()->getAdapter()->select($query, KDatabase::FETCH_FIELD);
    }

    /**
     * Provides the number of visitors
     *
     * @return int The number of visitors
     */
    final public function visitors()
    {
        if(!isset($this->_visitors))
        {
            $context = $this->getContext();
            $context->visitors  = null;

            if ($this->invokeCommand('before.fetch', $context) !== false)
            {
                $context->visitors = $this->_actionVisitors($context);
                $this->invokeCommand('after.fetch', $context);
            }

            $this->_visitors = $context->visitors;
        }

        return $this->_visitors;
    }

    protected function _actionVisitors(KModelContextInterface $context)
    {
        $context->query->table(array('tbl' => 'logman_impressions'))
                ->group('session_hash')
                ->limit(0);

        $this->_buildQueryWhere($context->query);

        $query = $this->getObject('lib:database.query.select')
                      ->columns('COALESCE(COUNT(*), 0)')->table(array('inner' => $context->query));

        return $this->getTable()->getAdapter()->select($query, KDatabase::FETCH_FIELD);;
    }

    /**
     * Provides the number of views per visit
     *
     * @return int The number of views per visit
     */
    final public function viewsPerVisit()
    {
        if(!isset($this->_viewsPerVisit))
        {
            $context = $this->getContext();
            $context->viewsPerVisit  = null;

            if ($this->invokeCommand('before.fetch', $context) !== false)
            {
                $context->viewsPerVisit = $this->_actionViewsPerVisit($context);
                $this->invokeCommand('after.fetch', $context);
            }

            $this->_viewsPerVisit = $context->viewsPerVisit;
        }

        return $this->_viewsPerVisit;
    }

    protected function _actionViewsPerVisit(KModelContextInterface $context)
    {
        $context->query->table(array('tbl' => 'logman_impressions'))
                       ->group('session_hash')
                       ->columns(array('total' => 'COUNT(*)'))
                       ->limit(0);

        $this->_buildQueryWhere($context->query);

        $query = $this->getObject('lib:database.query.select')
                      ->columns('COALESCE(SUM(inner.total)/COUNT(*), 0)')->table(array('inner' => $context->query));

        return $this->getTable()->getAdapter()->select($query, KDatabase::FETCH_FIELD);
    }
}