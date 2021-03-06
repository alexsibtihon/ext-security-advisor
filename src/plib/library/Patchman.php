<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH.
class Modules_SecurityAdvisor_Patchman
{
    const INSTALL_URL = 'https://ext.plesk.com/packages/6a51a3d4-ba72-4820-96bc-305e2a72bccc-patchmaninstaller/download';
    const NAME = 'patchmaninstaller';

    public static function isInstalled()
    {
    return Modules_SecurityAdvisor_Extension::isInstalled(self::NAME);
    }

    public static function isActive()
    {
    return true;
    }

    public static function install()
    {
    return Modules_SecurityAdvisor_Extension::install(self::INSTALL_URL);
    }

}
