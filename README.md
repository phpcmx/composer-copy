# composer-copy
将composer中第三方资源包复制到网站资源目录下

此插件将要解决 jquery bootstrap等插件在用composer更新的时候无法同步更新网站资源目录下的对应文件的问题。

加载了此插件以后，将会在往vender同级目录生成一个叫cli的文件夹。这个文件夹里会有一个配置文件，将配置要移动的文件夹和网站的根目录。

然后再次使用composer update nothing

请注意对应配置文件的权限问题

1. vendor同级目录/ 
1. 要复制的目标目录