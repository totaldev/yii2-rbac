<?php

namespace totaldev\yii\rbac\models;

use Yii;
use yii\base\BaseObject;
use yii\base\Controller;
use yii\base\Module;
use yii\caching\TagDependency;
use yii\helpers\VarDumper;
use yii\web\Application;

/**
 * Class RouteModel
 *
 * @package totaldev\yii\rbac\models
 */
class RouteModel extends BaseObject
{
    /**
     * @var string cache tag
     */
    const CACHE_TAG = 'yii2mod.rbac.route';
    /**
     * @var \yii\caching\Cache
     */
    public $cache;
    /**
     * @var int cache duration
     */
    public $cacheDuration = 3600;
    /**
     * Application {id}=>{path} registry for access dispatch
     * You must set items cause in backend and console mode, application must know about all dispatch contexts
     * @var array
     */
    public static $dispatchApplications = [];
    /**
     * @var array list of module IDs that will be excluded
     */
    public $excludeModules = [
        'gii', 'debug', 'faker',
    ];
    /**
     * @var \yii\rbac\ManagerInterface
     */
    protected $manager;

    /**
     * RouteModel constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->cache = Yii::$app->cache;
        $this->manager = Yii::$app->authManager;

        parent::__construct($config);
    }

    /**
     * @param $route
     * @return \yii\rbac\Permission
     */
    protected function getPermissionForRoute($route)
    {
        return $this->manager->createPermission(trim($route, ' /'));
    }

    /**
     * Assign items
     * @param array $routes
     * @return bool
     */
    public function addNew(array $routes): bool
    {
        foreach ($routes as $route) {
            $this->manager->add($this->getPermissionForRoute($route));
        }

        $this->invalidate();

        return true;
    }

    protected function getApplicationInstance($applicationId, $path)
    {
        $config = yii\helpers\ArrayHelper::merge(
            require(Yii::getAlias('@common/config/main.php')),
            require(Yii::getAlias('@common/config/main-local.php')),
            require(Yii::getAlias("$path/config/main.php")),
            require(Yii::getAlias("$path/config/main-local.php"))
        );

        // some developers modules bring some bugs
        unset($config['bootstrap'][array_search('debug', $config['bootstrap'])]);
        unset($config['modules']['debug']);
        unset($config['bootstrap'][array_search('gii', $config['bootstrap'])]);
        unset($config['modules']['gii']);

        $application = clone Yii::$app;
        Application::setInstance($application);
        $application->preInit($config);
        Yii::configure($application, $config);
        $application->init();
        return $application;
    }

    /**
     * Get list of application routes
     *
     * @return array
     */
    public function getAllRoutes(): array
    {
        $all = [];
        foreach (self::$dispatchApplications as $applicationId => $path) {
            if ($applicationId == Yii::$app->id) {
                $application = Yii::$app;
            } else {
                $application = $this->getApplicationInstance($applicationId, $path);
            }

            $key = [__METHOD__, $application->id];
            $result = (($this->cache !== null) ? $this->cache->get($key) : false);

            if ($result === false) {
                $this->getRouteRecursive($application, $result);
                if ($this->cache !== null) {
                    $this->cache->set($key, $result, $this->cacheDuration, new TagDependency([
                        'tags' => self::CACHE_TAG,
                    ]));
                }
                foreach ($result as $key => $value) {
                    $result["@{$applicationId}$key"] = "@{$applicationId}$value";
                    unset($result[$key]);
                }
                ksort($result);
            }
            $all = array_merge($all, $result);
        }

        return $all;
    }

    /**
     * Get available and assigned routes
     *
     * @return array
     */
    public function getAvailableAndAssignedRoutes(): array
    {
        $routes = $this->getAllRoutes();
        $exists = [];

        foreach (array_keys($this->manager->getPermissions()) as $name) {
            $exists[] = $name;
            unset($routes[$name]);
        }

        return [
            'available' => array_keys($routes),
            'assigned' => $exists,
        ];
    }

    /**
     * Invalidate the cache
     */
    public function invalidate()
    {
        if ($this->cache !== null) {
            TagDependency::invalidate($this->cache, self::CACHE_TAG);
        }
    }

    /**
     * Remove items
     *
     * @param array $routes
     *
     * @return bool
     */
    public function remove(array $routes): bool
    {
        foreach ($routes as $route) {
            $this->manager->remove($this->getPermissionForRoute($route));
        }
        $this->invalidate();

        return true;
    }

    /**
     * Get route of action
     *
     * @param Controller $controller
     * @param array $result all controller action
     */
    protected function getActionRoutes(Controller $controller, &$result)
    {
        $token = "Get actions of controller '" . $controller->uniqueId . "'";
        Yii::beginProfile($token, __METHOD__);

        try {
            $total = 0;
            $prefix = '/' . $controller->uniqueId . '/';
            foreach ($controller->actions() as $id => $value) {
                $result[$prefix . $id] = $prefix . $id;
            }
            $class = new \ReflectionClass($controller);

            foreach ($class->getMethods() as $method) {
                $name = $method->getName();
                if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                    $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', substr($name, 6)));
                    $id = $prefix . ltrim(str_replace(' ', '-', $name), '-');
                    $result[$id] = $id;
                    $total++;
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }

        Yii::endProfile($token, __METHOD__);
        return $total;
    }

    /**
     * Get list actions of controller
     *
     * @param mixed $type
     * @param string $id
     * @param Module $module
     * @param mixed $result
     */
    protected function getControllerActions($type, $id, Module $module, &$result)
    {
        $token = 'Create controller with config=' . VarDumper::dumpAsString($type) . " and id='$id'";
        Yii::beginProfile($token, __METHOD__);
        $total = 0;

        try {
            /* @var $controller Controller */
            $controller = Yii::createObject($type, [$id, $module]);
            $total = $this->getActionRoutes($controller, $result);
            if (!$total) {
                return $total;
            }
            $all = "/{$controller->uniqueId}/*";
            $result[$all] = $all;
            ++$total;
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }

        Yii::endProfile($token, __METHOD__);
        return $total;
    }

    /**
     * Get list controllers under module
     *
     * @param Module $module
     * @param string $namespace
     * @param string $prefix
     * @param mixed $result
     */
    protected function getControllerFiles(Module $module, string $namespace, string $prefix, &$result)
    {
        $path = Yii::getAlias('@' . str_replace('\\', '/', $namespace), false);
        $token = "Get controllers from '$path'";
        Yii::beginProfile($token, __METHOD__);

        $total = 0;
        try {
            if (!is_dir($path)) {
                return 0;
            }

            $total = 0;
            foreach (scandir($path) as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (is_dir($path . '/' . $file) && preg_match('%^[a-z0-9_/]+$%i', $file . '/')) {
                    $total += $this->getControllerFiles($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
                } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                    $baseName = substr(basename($file), 0, -14);
                    $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $baseName));
                    $id = ltrim(str_replace(' ', '-', $name), '-');
                    $className = $namespace . $baseName . 'Controller';
                    if (strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller')) {
                        $total += $this->getControllerActions($className, $prefix . $id, $module, $result);
                    }
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }

        Yii::endProfile($token, __METHOD__);
        return $total;
    }

    /**
     * Get route(s) recursive
     *
     * @param Module $module
     * @param array $result
     */
    protected function getRouteRecursive(Module $module, &$result)
    {
        $total = 0;
        if (!in_array($module->id, $this->excludeModules)) {
            $token = "Get Route of '" . get_class($module) . "' with id '" . $module->uniqueId . "'";
            Yii::beginProfile($token, __METHOD__);

            try {
                foreach ($module->getModules() as $id => $child) {
                    if (($child = $module->getModule($id)) !== null) {
                        $total += $this->getRouteRecursive($child, $result);
                    }
                }

                foreach ($module->controllerMap as $id => $type) {
                    $total += $this->getControllerActions($type, $id, $module, $result);
                }

                $namespace = trim($module->controllerNamespace, '\\') . '\\';
                if (!$total && !$this->getControllerFiles($module, $namespace, '', $result)) {
                    return $total;
                }
                $all = '/' . ltrim($module->uniqueId . '/*', '/');
                $result[$all] = $all;
                $total++;
            } catch (\Exception $exc) {
                Yii::error($exc->getMessage(), __METHOD__);
            }

            Yii::endProfile($token, __METHOD__);
        }
        return $total;
    }
}
