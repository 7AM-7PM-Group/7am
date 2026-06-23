<?php

namespace App\Livewire;

use App\Models\Business;
use App\Models\MigrationDb;
use App\Models\MinimumOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MigrationIndex extends Component
{
    public $title = 'Migration Process';

    public $process = [
        '', 'user', 'business', 'coupon', 'member', 'transaction', 'setting', 'outlet',
    ];

    public $migrations;

    public function mount()
    {
        $this->getMigrations();
    }

    public function getMigrations()
    {
        $this->migrations = MigrationDb::all();
    }

    public function runningMigration($id)
    {
        $migration = MigrationDb::find($id);

        switch ($migration->process) {
            case 'user':
                $this->runningMigrationUser();
                break;
            case 'business':
                $this->runningMigrationBusiness();
                break;
            case 'coupon':
                $this->runningMigrationCoupon();
                break;
            case 'member':
                $this->runningMigrationMember();
                break;
            case 'transaction':
                $this->runningMigrationTransaction();
                break;
            case 'setting':
                $this->runningMigrationSetting();
                break;
            case 'outlet':
                $this->runningMigrationOutlet();
                break;

            default:
                return;
                break;
        }
    }

    public function oldDatabase($table = '')
    {
        return DB::connection('mysql2')->table($table);
    }

    public function runningMigrationUser()
    {
        $oldUsers = $this->oldDatabase('users')->get()->toArray();

        foreach ($oldUsers as $key => $user) {
            User::updateOrCreate(['id' => $user->id], (array) $user);
        }

        MigrationDb::where('process', 'user')->update(['status' => 1, 'running_at' => Carbon::now()]);
    }

    public function runningMigrationBusiness()
    {
        try {
            DB::beginTransaction();

            $business = $this->oldDatabase('bussinesses')->get();

            foreach ($business as $key => $item) {
                Business::updateOrCreate(['id' => $item->id], (array) $item);
            }

            MigrationDb::where('process', 'business')->update(['status' => 1, 'running_at' => Carbon::now()]);

            DB::commit();
        } catch (\Throwable $th) {
            // throw $th;
        }
    }

    public function runningMigrationCoupon() {}

    public function runningMigrationMember() {}

    public function runningMigrationTransaction()
    {
        try {
            DB::beginTransaction();
            $minimum_orders = $this->oldDatabase('minimum_orders')->get();

            foreach ($minimum_orders as $key => $item) {
                MinimumOrder::updateOrCreate(['id' => $item->id], (array) $item);
            }

            DB::commit();
        } catch (\Throwable $th) {
            // throw $th;

            DB::rollBack();
        }
    }

    public function runningMigrationSetting() {}

    public function runningMigrationOutlet() {}

    public function render()
    {
        return view('livewire.migration-index')->layout('components.layouts.app', ['title' => $this->title]);
    }
}
