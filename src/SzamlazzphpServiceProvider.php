<?php

namespace Szamlazzphp;

use Illuminate\Support\ServiceProvider;
use Szamlazzphp\Client\SzamlaAgentClient;
use Szamlazzphp\Client\AuthBasedClient;
use Szamlazzphp\Client\ClientInterface;
use Szamlazzphp\Exceptions\MissingCredentialsException;

/**
 * Számlázz.hu ServiceProvider Laravel keretrendszerhez
 * 
 * Ez a ServiceProvider regisztrálja:
 * - A ClientInterface-t, amely a konfiguráció alapján egy SzamlaAgentClient vagy AuthBasedClient példányt ad vissza
 * - A SzamlazzHU Facade-t, amely a ClientInterface-re mutat
 */
class SzamlazzphpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/szamlazzphp.php', 'szamlazzphp'
        );

        $this->registerServices();
    }

    /**
     * Register Számlázz.hu services.
     *
     * @return void
     */
    protected function registerServices()
    {
        $this->app->singleton(ClientInterface::class, function () {
            $config = $this->app['config']->get('szamlazzphp', []);

            $authType = $config['auth_type'] ?? 'api_key';

            // API kulcs alapú autentikáció
            if ($authType === 'api_key') {
                if (empty($config['auth_token'])) {
                    throw new MissingCredentialsException('Missing auth token');
                }

                return new SzamlaAgentClient(
                    $config['auth_token'],
                    $config['e_invoice'] ?? false,
                    $config['request_invoice_download'] ?? false,
                    $config['downloaded_invoice_count'] ?? 1,
                    $config['response_version'] ?? 2,
                    $config['timeout'] ?? 0
                );
            } 
            // Felhasználónév-jelszó alapú autentikáció
            else if ($authType === 'auth') {
                if (empty($config['username']) || empty($config['password'])) {
                    throw new MissingCredentialsException('Missing username or password');
                }

                return new AuthBasedClient(
                    $config['username'],
                    $config['password'],
                    $config['e_invoice'] ?? false,
                    $config['request_invoice_download'] ?? false,
                    $config['downloaded_invoice_count'] ?? 1,
                    $config['response_version'] ?? 2,
                    $config['timeout'] ?? 0
                );
            }
            // Automatikus választás (visszafelé kompatibilitás)
            else {
                if (!empty($config['auth_token'])) {
                    return new SzamlaAgentClient(
                        $config['auth_token'],
                        $config['e_invoice'] ?? false,
                        $config['request_invoice_download'] ?? false,
                        $config['downloaded_invoice_count'] ?? 1,
                        $config['response_version'] ?? 1,
                        $config['timeout'] ?? 0
                    );
                } elseif (!empty($config['username']) && !empty($config['password'])) {
                    return new AuthBasedClient(
                        $config['username'],
                        $config['password'],
                        $config['e_invoice'] ?? false,
                        $config['request_invoice_download'] ?? false,
                        $config['downloaded_invoice_count'] ?? 1,
                        $config['response_version'] ?? 1,
                        $config['timeout'] ?? 0
                    );
                } else {
                    throw new MissingCredentialsException('Missing auth token or username and password');
                }
            }
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (function_exists('\config_path')) {
            $this->publishes([
                __DIR__ . '/../config/szamlazzphp.php' => \config_path('szamlazzphp.php'),
            ], 'config');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ClientInterface::class];
    }
} 