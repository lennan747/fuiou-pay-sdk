<?php

namespace Lennan\Fuiou\Sdk;

use Pimple\Container;

/**
 *
 */
class Application extends Container
{
    /**
     * @var array
     */
    protected $providers = [
        ServiceProviders\PaymentServiceProvider::class
    ];

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct();

        $this['config'] = function () use ($config) {
            return new Config($config);
        };

        $this->registerProviders();

        $this->logConfiguration($config);
    }

    /**
     * Log configuration.
     *
     * @param array $config
     */
    public function logConfiguration(array $config)
    {
        $config = new Config($config);

        $keys = ['mchnt_cd', 'app_key', 'secret'];

        foreach ($keys as $key) {
            !$config->has($key) || $config[$key] = '***' . substr($config[$key], -5);
        }

    }

    /**
     * Add a provider.
     *
     * @param string $provider
     *
     * @return Application
     */
    public function addProvider(string $provider): Application
    {
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * Set providers.
     *
     * @param array $providers
     */
    public function setProviders(array $providers)
    {
        $this->providers = [];

        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get(string $id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed $value
     */
    public function __set(string $id, $value)
    {
        $this->offsetSet($id, $value);
    }

    /**
     * Register providers.
     */
    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }
}