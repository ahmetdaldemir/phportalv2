<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function getCategoryPathList()
    {
        $categories =
            DB::select("WITH RECURSIVE category_path (id, name, parent_id, path) AS
(
  SELECT id, name, parent_id, name as path
  FROM categories
  WHERE parent_id = 0 and  company_id = " . Auth::user()->company_id . " and deleted_at is null
  UNION ALL
  SELECT k.id, k.name, k.parent_id, CONCAT(cp.path, ' -> ', k.name)
  FROM category_path cp JOIN categories k
  ON cp.id = k.parent_id Where deleted_at is null
)
SELECT * FROM category_path ORDER BY path;");
        return $categories;
    }


    public function categorySeperator1($data)
    {
        if (!empty($data)) {
            return implode("/", $this->array_column_recursive1($data, 'name')) . " /";
        }
        return "";
    }

    function array_column_recursive1(array $haystack, $needle)
    {
        $found = [];
        array_walk_recursive($haystack, function ($value, $key) use (&$found, $needle) {
            if ($key == $needle)
                $found[] = $value;
        });
        return $found;
    }

    public function testParentS($category_id = 0)
    {
        $data = null;

        $x = Category::find($category_id);
        if ($x) {
            $categories = Category::where('id', $x->parent_id)->get();
//dd($categories);
            foreach ($categories as $category) {
                $data[] = [
                    'id' => $category->id,
                    'list' => $this->testParentS($category->id),
                    'name' => $category->name
                ];
            }
        }
        return $data;
    }



}
