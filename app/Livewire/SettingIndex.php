<?php

namespace App\Livewire;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Session;

class SettingIndex extends Component
{
    public $title = 'Our Configuration';

    public $state;

    public function rules()
    {
        return [
            'state' => 'required|array',
            'state.*' => 'nullable',
        ];
    }

    public function mount()
    {

        $settings = Setting::all();

        // dd($settings);

        foreach ($settings as $setting) {
            $this->state['settings'][$setting->key] = [
                'key' => $setting->key,
                'value' => $setting->value,
                'type' => $setting->type,
            ];
        }
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            foreach ($this->state['settings'] as $key => $item) {
                Setting::updateOrCreate(
                    ['key' => $item['key']],
                    [
                        'value' => $item['value'],
                        'type' => $item['type'] ?? 'text',
                    ]
                );
            }
            DB::commit();
            Session::flash('success', 'Configuration Updated');
        } catch (\Throwable $th) {
            DB::rollBack();
            if (config('app.debug', false)) {
                throw $th;
            }
            session()->flash('error', $th->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.setting-index')->layout('components.layouts.app', ['title' => $this->title]);
    }
}
