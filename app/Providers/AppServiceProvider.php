<?php

namespace App\Providers;

use Exception;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use App\Observers\ClientObserver;
use App\Models\Client as ClientModel;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ClientModel::observe(ClientObserver::class);

        $this->loadGoogleStorageDriver();
    }

    private function loadGoogleStorageDriver(string $driverName = 'google')
    {
        Storage::extend($driverName, function ($app, $config) {
            try {
                $options = [];

                if (! empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                if (empty($config['refreshToken'])) {
                    throw new Exception('Google Drive refresh token is not configured.');
                }

                $client = new Client;
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new Drive($client);
                $adapter = new GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
                $driver = new Filesystem($adapter);

                return new FilesystemAdapter($driver, $adapter);
            } catch (Exception $e) {
                info('Google Drive Storage Error: ' . $e->getMessage());
                throw $e; // Re-throw so spatie/backup can handle it gracefully
            }
        });
    }
}
