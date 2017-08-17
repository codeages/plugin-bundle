<?php
namespace Codeages\PluginBundle\Biz\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Codeages\PluginBundle\Biz\Dao\OldAppDao;

class OldAppDaoImpl extends GeneralDaoImpl implements OldAppDao
{
    protected $table = 'cloud_app';

    public function getByCode($code)
    {
        return $this->getByFields(array('code' => $code));
    }

    public function findByTypes($types = array(), $start, $limit)
    {
        return $this->search(array('types' => $types), array('installedTime' => 'ASC'), $start, $limit);
    }

    public function countByTypes($types = array())
    {
        return $this->count(array('types' => $types));
    }

    public function declares()
    {
        return array(
            'timestamps' => array('installedTime', 'updatedTime'),
            'serializes' => array(),
            'orderbys' => array('installedTime'),
            'conditions' => array(
                'type = :type',
                'name = :name',
                'type IN ( :types)'
            ),
        );
    }
}
