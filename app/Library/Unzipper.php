<?php


namespace App\Library;

use Illuminate\Http\JsonResponse;
use ZipArchive;

class Unzipper
{
    /**
     * Decompress/extract a zip archive using ZipArchive.
     *
     * @param $archive
     * @param $destination
     *
     * @return JsonResponse
     */
    public static function extractZipArchive($archive, $destination): JsonResponse
    {
        // Check if webserver supports unzipping.
        if ( ! class_exists('ZipArchive')) {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Error: Your PHP version does not support unzip functionality',
            ]);
        }

        $zip = new ZipArchive();

        // Check if archive is readable.
        if ($zip->open($archive) === true) {
            // Check if destination is writable
            if (is_writeable($destination.'/')) {
                $zip->extractTo($destination);
                $zip->close();

                return response()->json([
                        'status'  => 'success',
                        'message' => 'File Unzip successfully',
                ]);
            }
        }

        return response()->json([
                'status'  => 'error',
                'message' => 'Error: Cannot read .zip archive.',
        ]);
    }

}
