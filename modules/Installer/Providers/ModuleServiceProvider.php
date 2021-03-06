<?php namespace KodiCMS\Installer\Providers;

use KodiCMS\CMS\Providers\ServiceProvider;
use KodiCMS\Installer\Console\Commands\Install;
use KodiCMS\Installer\Console\Commands\ModuleSeed;
use KodiCMS\Installer\Console\Commands\ModuleMigrate;

class ModuleServiceProvider extends ServiceProvider {

	public function register()
	{
		$this->registerConsoleCommand('module.migrate', ModuleMigrate::class);
		$this->registerConsoleCommand('module.seed', ModuleSeed::class);
		$this->registerConsoleCommand('cms.install', Install::class);
	}
}