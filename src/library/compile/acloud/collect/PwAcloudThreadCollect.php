<?php

Wind::import('LIB:compile.acloud.collect.AbstractAcloudCollect');

/**
 * 手机帖子列表页的数据
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwAcloudThreadCollect.php 7049 2012-03-29 03:20:38Z liusanbian $
 * @package wekit.compile.acloud.collect
 */
class PwAcloudThreadCollect extends AbstractAcloudCollect
{
    /* (non-PHPdoc)
     * @see AbstractCollect::collect()
     */
    public function collect(PwAcloudDataMapper $dataMapper, $vars)
    {
        $dataMapper->setFid($vars['pwforum']->fid);
    }
}
