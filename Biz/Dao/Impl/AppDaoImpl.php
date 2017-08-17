<?php
namespace Codeages\PluginBundle\Biz\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Codeages\PluginBundle\Biz\Dao\AppDao;

class AppDaoImpl extends GeneralDaoImpl implements AppDao
{
    protected $table = 'app';

    public function getByCode($code)
    {
        return $this->getByFields(array('code' => $code));
    }

    public function findByType($types, $start, $limit)
    {
        return $this->search(array('types' => $types), array('created_time' => 'ASC'), $start, $limit);
    }

    public function countByType($types)
    {
        return $this->count(array('types' => $types));
    }

    public function declares()
    {
        return array(
            'timestamps' => array('created_time', 'updated_time'),
            'serializes' => array(),
            'orderbys' => array('created_time'),
            'conditions' => array(
                'type = :type',
                'name = :name',
                'type IN ( :types)'
            ),
        );
    }
}
