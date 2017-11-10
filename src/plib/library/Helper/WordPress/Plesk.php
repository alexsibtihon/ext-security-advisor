<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH.

use PleskExt\SecurityAdvisor\Helper\Domain;

class Modules_SecurityAdvisor_Helper_WordPress_Plesk extends Modules_SecurityAdvisor_Helper_WordPress_Abstract
{
    protected function _getInstances()
    {
        return $this->_dbAdapter->query("SELECT * FROM WordpressInstances");
    }

    protected function _getInstance($wpId)
    {
        return $this->_dbAdapter->fetchRow("SELECT * FROM WordpressInstances WHERE id = ?", [$wpId]);
    }

    protected function _getInstanceProperties($wpId)
    {
        return $this->_dbAdapter->query("SELECT * FROM WordpressInstanceProperties WHERE wordpressInstanceId = ?", [$wpId]);
    }

    protected function _getNotSecureCount()
    {
        $client = pm_Session::getClient();

        $domainIds = Domain::getAllVendorDomainsIds($client);
        $domainIds = implode(',', $domainIds);
        $where = "wp.value LIKE '%http://%' AND subscriptionId IN ($domainIds)";

        return $this->_dbAdapter->fetchOne("SELECT count(*) FROM WordpressInstances w
            INNER JOIN WordpressInstanceProperties wp ON (wp.wordpressInstanceId = w.id AND wp.name = 'url')
            WHERE $where");
    }

    protected function _callWpCli($wordpress, $args)
    {
        $subscription = new Modules_SecurityAdvisor_Helper_Subscription($wordpress['subscriptionId']);
        $fileManager = new pm_FileManager($subscription->getPmDomain()->getId());

        $res = pm_ApiCli::callSbin('wpmng', array_merge([
            '--user=' . $subscription->getSysUser(),
            '--php=' . $subscription->getPhpCli(),
            '--',
            '--path=' . $fileManager->getFilePath($wordpress['path']),
        ], $args));

        if (0 !== $res['code']) {
            throw new pm_Exception($res['stdout'] . $res['stderr']);
        }
    }

    protected function _resetCache($wpId)
    {
        $request = <<<APICALL
        <wp-instance>
            <clear-cache>
                <filter>
                    <id>{$wpId}</id>
                </filter>
            </clear-cache>
        </wp-instance>
APICALL;
        $response = pm_ApiRpc::getService()->call($request);
        if ('error' == $response->{'wp-instance'}->{'clear-cache'}->result->status) {
            throw new pm_Exception($response->{'wp-instance'}->{'clear-cache'}->result->errtext);
        }
    }

    protected function _getDbAdapter()
    {
        return pm_Bootstrap::getDbAdapter();
    }
}
