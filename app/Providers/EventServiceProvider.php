<?php

namespace App\Providers;

use App\Http\Resources\Package\OrderPackageResource;
use App\Models\CartDetail;
use App\Models\Complain;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Fine;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDetailNote;
use App\Models\OrderPackage;
use App\Models\Transaction;
use App\Observers\CartDetailObserver;
use App\Observers\ComplainObserver;
use App\Observers\CustomerObserver;
use App\Observers\DeliveryObserver;
use App\Observers\FineObserve;
use App\Observers\OrderDetailNoteObserve;
use App\Observers\OrderDetailObserve;
use App\Observers\OrderObserve;
use App\Observers\OrderPackageObserve;
use App\Observers\TransactionObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Customer::observe(CustomerObserver::class);
        CartDetail::observe(CartDetailObserver::class);
        OrderDetailNote::observe(OrderDetailNoteObserve::class);
        Order::observe(OrderObserve::class);
        OrderDetail::observe(OrderDetailObserve::class);
        Fine::observe(FineObserve::class);
        OrderPackage::observe(OrderPackageObserve::class);
        Delivery::observe(DeliveryObserver::class);
        Complain::observe(ComplainObserver::class);
        Transaction::observe(TransactionObserver::class);
    }
}
