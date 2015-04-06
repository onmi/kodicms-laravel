<?php namespace KodiCMS\CMS\Loader;

use Illuminate\Support\Facades\App;
use KodiCMS\CMS\Helpers\File;

class ModuleContainer
{
	/**
	 * @var string
	 */
	protected $_path;

	/**
	 * @var string
	 */
	protected $_name;

	/**
	 * @var bool
	 */
	protected $_isRegistered = FALSE;

	/**
	 * @var bool
	 */
	protected $_isBooted = FALSE;

	/**
	 * @var string
	 */
	protected $_namespace = 'KodiCMS';

	/**
	 * This namespace is applied to the controller routes in your routes file.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $_controllerNamespacePrefix = 'Http\\Controllers';

	/**
	 * @param string $moduleName
	 * @param null|string $modulePath
	 * @param null|string $namespace
	 */
	public function __construct($moduleName, $modulePath = NULL, $namespace = NULL)
	{
		if (empty($modulePath)) {
			$modulePath = base_path('modules/' . $moduleName);
		}

		$this->_path = File::normalizePath($modulePath);
		$this->_name = $moduleName;
		if (!is_null($namespace)) {
			$this->_namespace = $namespace;
		}
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->_namespace . '\\' . $this->getName();
	}

	/**
	 * @return string
	 */
	public function getControllerNamespace()
	{
		return $this->getNamespace() . '\\' . $this->_controllerNamespacePrefix;
	}

	/**
	 * @param strimg|null $sub
	 * @return string
	 */
	public function getPath($sub = NULL)
	{
		$path = $this->_path;
		if (is_array($sub)) {
			$sub = implode(DIRECTORY_SEPARATOR, $sub);
		}

		if (!is_null($sub)) {
			$path .= DIRECTORY_SEPARATOR . $sub;
		}

		return $path;
	}

	/**
	 * @return $this
	 */
	public function boot()
	{
		if (!$this->_isBooted) {
			$this->loadViews();
			$this->loadTranslations();
			$this->loadAssets();
			$this->loadConfig();

			$serviceProviderPath = $this->getPath(['Providers', 'ModuleServiceProvider.php']);
			if (is_file($serviceProviderPath)) {
				App::register($this->getNamespace() . '\Providers\ModuleServiceProvider');
			}

			$this->_isBooted = TRUE;
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function register()
	{
		if (!$this->_isRegistered) {

			$this->_isRegistered = TRUE;
		}

		return $this;
	}

	protected function loadAssets()
	{
		$packagesFile = $this->getPath(['resources', 'packages.php']);
		if (is_file($packagesFile)) {
			require $packagesFile;
		}
	}

	/**
	 * Register a view file namespace.
	 *
	 * @return void
	 */
	protected function loadViews()
	{
		$namespace = strtolower($this->getName());

		if (is_dir($appPath = base_path() . '/resources/views/vendor/' . $namespace)) {
			app('view')->addNamespace($namespace, $appPath);
		}

		app('view')->addNamespace($namespace, $this->getPath(['resources', 'views']));
	}

	/**
	 * Register a translation file namespace.
	 *
	 * @return void
	 */
	protected function loadTranslations()
	{
		$namespace = strtolower($this->getName());
		app('translator')->addNamespace($namespace, $this->getPath(['resources', 'lang']));
	}

	/**
	 * Register a config file namespace.
	 * TODO: Оптимизировать загрузку конфигов модулей
	 * @return void
	 */
	protected function loadConfig()
	{
		$path = $this->getPath('config');

		if (!is_dir($path)) return;

		foreach (new \DirectoryIterator($path) as $file) {
			if ($file->isDot() OR strpos($file->getFilename(), '.php') === FALSE) continue;

			$key = $file->getBasename('.php');
			$config = app('config')->get($key, []);

			app('config')->set($key, array_merge(require $file->getPathname(), $config));
		}
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return (string)$this->getName();
	}
}