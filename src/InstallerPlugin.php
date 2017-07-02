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
class InstallerPlugin // implements PluginInterface
{
    public $configPath = '';
    private $configFileName = 'composerCopy.php';
    
    
    /**
     * Apply plugin modifications to Composer
     *
     * @param Composer    $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        // 获取项目根目录
        $baseDir = $composer->getConfig()->get('baseDir');
        // 生成目标位置
        $this->configPath = $baseDir.DIRECTORY_SEPARATOR.
            'cli'.DIRECTORY_SEPARATOR.
            "config".DIRECTORY_SEPARATOR;
    
        // 生成最终的文件路径
        $configFile = $this->configPath . $this->configFileName;
        
        // 检查是否有配置文件
        if(!file_exists($configFile)){
            $this->createConfigFile();
        }
        
        
    }
    
    /**
     * 生成默认配置文件
     */
    public function createConfigFile(){
        // 生成最终的文件路径
        $configFilePath = $this->configPath . $this->configFileName;
        
        // 默认配置文件
        $configTplFile = realpath((__DIR__)."/../data/configDefault.data");
        
        // 默认内容
        $configContentText = file_get_contents($configTplFile);
        
        // 循环遍历生成文件路径
        $this->createDir($this->configPath);
        
        // 插入内容
        file_put_contents($configFilePath, $configContentText);
    }
    
    /**
     * @param $path string 要生成的path，要带/ 或者带/文件.后缀。即最后一个不处理
     */
    private function createDir($path){
        if (!file_exists($path)){
            $this->createDir(dirname($path));
            mkdir($path, 0777);
        }
    }
}