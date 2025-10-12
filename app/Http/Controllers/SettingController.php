<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ContentTypes\Checkbox;
use App\Http\Controllers\ContentTypes\Coordinates;
use App\Http\Controllers\ContentTypes\File;
use App\Http\Controllers\ContentTypes\Image;
use App\Http\Controllers\ContentTypes\MultipleCheckbox;
use App\Http\Controllers\ContentTypes\MultipleImage;
use App\Http\Controllers\ContentTypes\Password;
use App\Http\Controllers\ContentTypes\Relationship;
use App\Http\Controllers\ContentTypes\SelectMultiple;
use App\Http\Controllers\ContentTypes\Text;
use App\Http\Controllers\ContentTypes\Timestamp;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class SettingController extends Controller
{
    public function index()
    {
        $data['settings'] = Setting::all();
        return view('module.settings.index', $data);
    }


    public function store(Request $request)
    {
        $setting = new Setting();
        $setting->key = $request->key;
        $setting->value = $request->value;
        $setting->display_name = $request->display_name;
        $setting->type = $request->type;
        $setting->category = $request->category;
        $setting->company_id = Auth::user()->company_id;
        $setting->user_id = Auth::user()->id;
        $setting->save();
        return redirect()->back();
    }

    public function update(Request $request)
    {
        $settings = Setting::all();
        foreach ($settings as $setting) {

            $content = $this->getContentBasedOnType($request, 'settings', (object)[
                'type' => $setting->type,
                'field' => str_replace('.', '_', $setting->key),
                'category' => $setting->category,
            ], $setting->details);

            if ($setting->type == 'image' && $content == null) {
                continue;
            }

            if ($setting->type == 'file' && $content == null) {
                continue;
            }

            $key = preg_replace('/^' . Str::slug($setting->category) . './i', '', $setting->key);

            $setting->category = [Str::slug($setting->category), $key][0];
            $setting->key = [Str::slug($setting->category), $key][1];
            $setting->value = $content;
            $setting->save();
        }

        return redirect()->back();
    }
    public function getContentBasedOnType(Request $request, $slug, $row, $options = null)
    {

        switch ($row->type) {
            /********** PASSWORD TYPE **********/
            case 'password':
                return (new Password($request, $slug, $row, $options))->handle();
            /********** CHECKBOX TYPE **********/
            case 'checkbox':
                return (new Checkbox($request, $slug, $row, $options))->handle();
            /********** MULTIPLE CHECKBOX TYPE **********/
            case 'multiple_checkbox':
                return (new MultipleCheckbox($request, $slug, $row, $options))->handle();
            /********** FILE TYPE **********/
            case 'file':
                return (new File($request, $slug, $row, $options))->handle();
            /********** MULTIPLE IMAGES TYPE **********/
            case 'multiple_images':
                return (new MultipleImage($request, $slug, $row, $options))->handle();
            /********** SELECT MULTIPLE TYPE **********/
            case 'select_multiple':
                return (new SelectMultiple($request, $slug, $row, $options))->handle();
            /********** IMAGE TYPE **********/
            case 'image':
                return (new Image($request, $slug, $row, $options))->handle();
            /********** DATE TYPE **********/
            case 'date':
                /********** TIMESTAMP TYPE **********/
            case 'timestamp':
                return (new Timestamp($request, $slug, $row, $options))->handle();
            /********** COORDINATES TYPE **********/
            case 'coordinates':
                return (new Coordinates($request, $slug, $row, $options))->handle();
            /********** RELATIONSHIPS TYPE **********/
            case 'relationship':
                return (new Relationship($request, $slug, $row, $options))->handle();
            /********** ALL OTHER TEXT TYPE **********/
            default:
                return (new Text($request, $slug, $row, $options))->handle();
        }
    }

}
