<?php
/**
 * InstallerPLugin.php
 *
 * 作者: 不二进制·Number
 * 创建日期: 2017/7/2 下午3:11
 * 修改记录:
 *
 * $Id$
 */

namespace phpcmx\composerCopy;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

/**
 * Class InstallerPlugin
 * 插件类入口
 *
 * @package phpcmx\composerCopy
 */
class InstallerPlugin implements PluginInterface
{
    
    /**
     * Apply plugin modifications to Composer
     *
     * @param Composer    $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        var_export($composer->getConfig());
        var_export($io);
    }
}