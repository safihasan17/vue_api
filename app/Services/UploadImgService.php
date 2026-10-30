<?php

namespace App\Services;

class UploadImgService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function upload($img_file, $path = 'upload', $img_name = null)
    {

        if (!is_dir(public_path($path))) {
            mkdir(public_path($path), 0755, true);
        }

        if ($img_name != null) {
            $img_name = $img_name . '.' . $img_file->getClientOriginalExtension();
        } else {
            $img_name = time() . '.' . $img_file->getClientOriginalExtension();
        }


        $img_file->move(public_path($path), $img_name);
        $img_full_path = $path . "/" . $img_name;

        return $img_full_path;
    }
}
