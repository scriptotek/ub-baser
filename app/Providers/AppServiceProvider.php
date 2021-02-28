<?php

namespace App\Providers;

use App\Base;
use App\Http\Request;
use App\Http\Requests\DataTableRequest;
use App\Http\Requests\SearchRequest;
use App\Schema\Schema;
use App\Services\AutocompleteServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        \Psr\Http\Client\ClientInterface::class => \DivineOmega\Psr18GuzzleAdapter\Client::class,
        \Psr\Http\Message\RequestFactoryInterface::class => \Http\Factory\Guzzle\RequestFactory::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \App\Bases\Litteraturkritikk\Record::observe(\App\Bases\Litteraturkritikk\RecordObserver::class);
        \App\Bases\Litteraturkritikk\Person::observe(\App\Bases\Litteraturkritikk\PersonObserver::class);
        \App\Bases\Dommer\Record::observe(\App\Bases\Dommer\RecordObserver::class);
        \App\Bases\Opes\Record::observe(\App\Bases\Opes\RecordObserver::class);
        \App\Bases\Sakbib\Publication::observe(\App\Bases\Sakbib\RecordObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $requestClasses = [
            Request::class,
            SearchRequest::class,
            DataTableRequest::class,
        ];

        foreach ($requestClasses as $cls) {
            $this->app->singleton($cls, function ($app) use ($cls) {
                return $cls::createFrom($app['request']);
            });
        }

        // IDÉ: Bind 'Record', 'RecordView', 'AutocompleteService' based on base
        // Kan jeg binde Base også??

        $this->app->singleton(Base::class, function ($app) {
            $request = $app[Request::class];
            return $request->getBase();
        });

        $this->app->singleton(Schema::class, function ($app) {
            $base = $app[Base::class];
            return $base->getSchema();
        });

        $this->app->singleton(AutocompleteServiceInterface::class, function ($app) {
            $base = $app[Base::class];
            $serviceClass = $base->getClass('AutocompleteService');
            return new $serviceClass($base);
        });
    }

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        //
    ];
}
