<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH.
class Modules_SecurityAdvisor_Helper_Hostname
{
    public static function getServerHostname()
    {
        if (pm_ProductInfo::isWindows()) {
            $cmd = 'ifmng';
            $args = ['--get-hostname'];
        } else {
            $cmd = 'serverconf';
            $args = ['--key', 'FULLHOSTNAME'];
        }
        $res = pm_ApiCli::callSbin($cmd, $args);
        $hostname = trim($res['stdout']);
        return $hostname;
    }
}
