<?php
namespace Codeages\PluginBundle\Biz\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface AppDao extends GeneralDaoInterface
{
    public function getByCode($code);

    public function findByTypes($types = array(), $start, $limit);

    public function countByTypes($types = array());


}
