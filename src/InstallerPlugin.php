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
     * @var Composer
     */
    private $composer = null;
    /**
     * @var IOInterface
     */
    private $io = null;
    
    private $baseDir        = '';
    private $configDir      = '';
    private $vendorDirName     = 'vendor';
    private $configFileName = 'composerCopy.php';
    /**
     * @var array
     */
    private $copyList       = [];
    
    
    /**
     * Apply plugin modifications to Composer
     *
     * @param Composer    $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        
        // 获取项目根目录
        $this->baseDir = $composer->getConfig()->get('baseDir');
        // 获取vendor目录
        $this->vendorDirName = $composer->getConfig()->get('vendor-dir');
        
        // 生成目标位置
        $this->configDir = $this->baseDir.DIRECTORY_SEPARATOR.
            'cli'.DIRECTORY_SEPARATOR.
            "config".DIRECTORY_SEPARATOR;
    
        // 生成最终的文件路径
        $configFile = $this->configDir . $this->configFileName;
    
        // 检查是否有配置文件
        if(!file_exists($configFile)){
            $this->createConfigFile();
        }
        
        // 保存要复制的列表
        $this->copyList = include $configFile;

        if(empty($this->copyList)){
            $this->copyList = [];

            throw new \RuntimeException('加载文件失败'.$configFile);
        }

        // 移动
        $this->copy();
    }
    
    /**
     * 生成默认配置文件
     */
    public function createConfigFile(){
        // 生成最终的文件路径
        $configFilePath = $this->configDir . $this->configFileName;

        // log
        echo "生成配置文件".PHP_EOL;
        
        // 默认配置文件
        $configTplFile = (dirname(__DIR__)."/data/configDefault.data");
        
        // 默认内容
        $configContentText = file_get_contents($configTplFile);
        
        // 循环遍历生成文件路径
        $this->makeDir($this->configDir);
        
        // 插入内容
        file_put_contents($configFilePath, $configContentText);
    }
    
    /**
     * @param $path string 要生成的path，要带/ 或者带/文件.后缀。即最后一个不处理
     */
    private function makeDir($path){
        if (!file_exists($path)){
            $this->makeDir(dirname($path));
            
            mkdir($path, 0777);
        }
    }
    
    
    /**
     * 复制项目
     */
    private function copy(){
        // log
        echo "开始复制资源".PHP_EOL;
        
        // 要复制的项目列表
        $copyList = $this->copyList;
    
        foreach ($copyList as $fromPath => $toPath) {
            // log
            echo "PATH: {$fromPath} => {$toPath}".PHP_EOL;
            
            // 解析路径
            $source = $this->parseDir(
                $this->baseDir.DIRECTORY_SEPARATOR .$this->vendorDirName,
                $fromPath
            );
            $destination = $this->parseDir(
                $this->baseDir,
                $toPath
            );
            
            if(is_dir($source)){
                $this->copyDir($source, $destination);
            }else{
                $this->copyFile($source, $destination);
            }
        }

        echo "DONE...".PHP_EOL;
    }
    
    /**
     * 解析
     *
     * @param $baseDir
     * @param $destination
     *
     * @return string
     */
    private function parseDir($baseDir, $destination){
        // 判断是不是绝对路径
        if(substr($destination, 0, 1) === DIRECTORY_SEPARATOR
            or substr($destination, 1, 1) === ':'){
            return $destination;
        }
        // 相对路径
        else{
            return rtrim($baseDir).DIRECTORY_SEPARATOR.$destination;
        }
    }
    
    /**
     * 生成
     *
     * @param $src
     * @param $dst
     */
    function copyDir($src,$dst) {

        $dir = opendir($src);
        $this->makeDir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . DIRECTORY_SEPARATOR . $file) ) {
                    $this->copyDir($src . DIRECTORY_SEPARATOR . $file,$dst . DIRECTORY_SEPARATOR . $file);
                    continue;
                }
                else {
                    $this->copyFile($src.DIRECTORY_SEPARATOR. $file, $dst . DIRECTORY_SEPARATOR. $file);
                }
            }
        }
    
        closedir($dir);
    }
    
    
    /**
     * 复制文件
     *
     * @param $srcFile
     * @param $dstFile
     */
    private function copyFile($srcFile, $dstFile){
        // log
        echo "$srcFile => $dstFile".PHP_EOL;
        
        // copy
        copy($srcFile,$dstFile);
    }
}