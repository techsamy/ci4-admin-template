## Solution 1

* Clone project using git clone
* sudo git clone https://github.com/techsamy/ci4-admin-template.git .
* then give permission to your project directory  "sudo chown -R ubuntu:ubuntu /www/wwwroot/ci4.h9l.site/"
* then run composer install
* then if you are getting these error:
```
Warning: require(/www/wwwroot/ci4.h9l.site/vendor/composer/../phpunit/phpunit/src/Framework/Assert/Functions.php): Failed to open stream: No such file or directory in /www/wwwroot/ci4.h9l.site/vendor/composer/autoload_real.php on line 41 Fatal error: Uncaught Error: Failed opening required '/www/wwwroot/ci4.h9l.site/vendor/composer/../phpunit/phpunit/src/Framework/Assert/Functions.php' (include_path='.:') in /www/wwwroot/ci4.h9l.site/vendor/composer/autoload_real.php:41 Stack trace: #0 /www/wwwroot/ci4.h9l.site/vendor/composer/autoload_real.php(45): {closure}() #1 /www/wwwroot/ci4.h9l.site/vendor/autoload.php(22): ComposerAutoloaderInit657ac079313fab046eb8c29fe1d6992b::getLoader() #2 /www/wwwroot/ci4.h9l.site/vendor/codeigniter4/framework/system/Autoloader/Autoloader.php(146): include('...') #3 /www/wwwroot/ci4.h9l.site/vendor/codeigniter4/framework/system/Autoloader/Autoloader.php(131): CodeIgniter\Autoloader\Autoloader->loadComposerAutoloader() #4 /www/wwwroot/ci4.h9l.site/vendor/codeigniter4/framework/system/Boot.php(253): CodeIgniter\Autoloader\Autoloader->initialize() #5 /www/wwwroot/ci4.h9l.site/vendor/codeigniter4/framework/system/Boot.php(54): CodeIgniter\Boot::loadAutoloader() #6 /www/wwwroot/ci4.h9l.site/public/index.php(59): CodeIgniter\Boot::bootWeb() #7 {main} thrown in /www/wwwroot/ci4.h9l.site/vendor/composer/autoload_real.php on line 41
```

* then run this command " rm -rf vendor composer.lock" 
* then composer install : composer install --no-dev --optimize-autoloader
* then delete composer.lock file & vendor file again from aapanel file manager
* Drag and drop the old vendor zip & composer.lock file from your local system to the aapanel file manager
* then extract the vendor zip file
* then give permission to your project directory  "sudo chown -R ubuntu:ubuntu /www/wwwroot/ci4.h9l.site/"

====================================

## Solution 2

## change ownership of the project directory to your user (ubuntu in this case)
sudo chown -R ubuntu:ubuntu /www/wwwroot/ci4.h9l.site/


then i am getting these erros 
`CodeIgniter\Cache\Exceptions\CacheException Cache unable to write to "/www/wwwroot/ci4.h9l.site/writable/cache/".`

## So for this i am giving write permissions to the writable directory
# Give ownership back to the web server user (aaPanel usually runs as www or www-data)
sudo chown -R www:www /www/wwwroot/ci4.h9l.site/writable

# Allow write permissions
sudo chmod -R 775 /www/wwwroot/ci4.h9l.site/writable
