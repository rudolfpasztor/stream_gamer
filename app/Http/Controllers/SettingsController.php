<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Setting;

class SettingsController extends Controller
{

    public function index()
    {
        $settings = Setting::get();
        $sets;
        foreach ($settings as $setting) {
            $sets[$setting->key] =  $setting->value;
        }
        return $sets;

    }


    public function store(Request $request)
    {
        $settings_object = $request->input('settings');
        $updated_settings = array();
        foreach ($settings_object as $key => $value) {

            $setting = Setting::where('key', $key)->first();

            if($setting) {
                $setting->value = $value;
            } else {
                $setting = new Setting;
                $setting->key = $key;
                $setting->value = $value;
            }

            if($setting->save()) {
                array_push($updated_settings, $setting);
            }

        }

        $settings = Setting::get();
        $sets;
        foreach ($settings as $setting) {
            $sets[$setting->key] =  $setting->value;
        }
        return $sets;

    }

    public function get($key) {
        $setting = Setting::where('key', $key)->first();
        return $setting->value;
    }

    public function set($key, $value) {
        $setting = Setting::where('key', $key)->first();
        $setting->value = $value;
        if($setting->save()) {
            return $setting->value;
        }

    }
}
