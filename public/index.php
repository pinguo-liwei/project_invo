<?php

error_reporting(E_ALL);

try {
	
	// 读取配置文件
	$config = new Phalcon\Config\Adapter\Ini(__DIR__.'/../app/config/config.ini');
	
	//从配置文件中，注册要用到的类文件
	$loader = new \Phalcon\Loader();
	
	$loader->registerDirs(array(
	
			//需要注册的是，注册的这些目录并不包括 viewsDir,因为viewsDir中并不包含classes文件，而是html+php文件
			__DIR__.$config->application->controllersDir,
			__DIR__.$config->application->pluginsDir,
			__DIR__.$config->application->libraryDir,
			__DIR__.$config->application->modelsDir,
	))->register();
	
	//载入依赖注入器DI
	$di = new \Phalcon\DI\FactoryDefault();
	
	//用di连接数据库
	$di->set('db', function() use ($config) {
		return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
			"host" => $config->database->host,
        	"username" => $config->database->username,
        	"password" => $config->database->password,
        	"dbname" => $config->database->name,
			 "charset"=>"utf8",
		));
	});
	
	//开启session组件,"session"名称不能更改，其他的可以自由更改适配器
	$di->set('session', function(){
		$session = new Phalcon\Session\Adapter\Files();
		$session->start();
		return $session;
	});
	
	//开启url组件用于管理整个应用的url
	$di->set('url', function() use ($config){
		$url = new \Phalcon\Mvc\Url();
		$url->setBaseUri($config->application->baseUri);
		return $url;
	});
	
	//在引导文件的最后部分，我们使用 Phalcon\Mvc\Application ，这个类初始化并执行用户的请求
	$application = new \Phalcon\Mvc\Application();
	$application->setDI($di);
	echo $application->handle()->getContent();
} catch (Exception $e) {
	
}