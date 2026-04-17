<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class SiteSetting extends Model
{
    use Translatable;

    protected $fillable = [
        'config_key', 'display_name', 'value', 'type' , 'details'
    ];
    protected $translatable = [
        'display_name', 'value'
    ];
    protected $appends = [
        'arr_details'
    ];

    // protected $casts = [
    //     'details' => 'array'
    // ];

        public function getArrDetailsAttribute() {
            return json_decode($this->details, true);
        }

    public static function allJson($lang = null,$group = null): array
    {
        $settings = self::withTranslation($lang ?? app()->getLocale())->get();
        $settings = $settings->translate($lang ?? app()->getLocale(), 'en');
        $nestedArray = [];

        foreach ($settings as $setting) {
            $keys = explode('.', $setting->config_key);

            $array = &$nestedArray;

            foreach ($keys as $key) {
                if (!isset($array[$key])) {
                    $array[$key] = [];
                }
                if ($key === end($keys)) {
                    $array[$key] = $setting;
                }

                $array = &$array[$key];
            }
        }


        return $group ? $nestedArray[$group] : $nestedArray;
    }
    public static function allEdit($lang='en', $group = null): array
    {
        $settings = self::withTranslation($lang)->get();
        $settings = $settings->translate($lang, 'en');
        $nestedArray = [];

        foreach ($settings as $setting) {
            $keys = explode('.', $setting->config_key);

            $array = &$nestedArray;

            if (!isset($array[$keys[0]])) {
                $array[$keys[0]] = [];
            }
            array_push($array[$keys[0]], $setting);

            // foreach ($keys as $key) {
            //     if ($key === end($keys)) {
            //         $array[$key] = $setting;
            //     }

            //     $array = &$array[$key];
            // }
        }


        return $group ? $nestedArray[$group] : $nestedArray;
    }
}
