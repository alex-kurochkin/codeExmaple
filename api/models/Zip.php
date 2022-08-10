<?php

declare(strict_types=1);

namespace api\models;

use api\models\common\Uuid;
use Exception;
use RuntimeException;
use ZipArchive;

class Zip
{
    /**
     * @param string $filename internal zipped file name
     * @param string $content
     * @return string
     * @throws Exception
     */
    public static function compress(string $filename, string $content): string
    {
        $tmpFileName = '/tmp/' . Uuid::uuid4();
        $zip = new ZipArchive();

        if (!$zip->open($tmpFileName, ZipArchive::CREATE)) {
            throw new RuntimeException('Can\'t create tmp zip file: ' . $tmpFileName);
        }

        $zip->addFromString($filename, $content);
        $zip->close();

        $zipped = file_get_contents($tmpFileName);
        unlink($tmpFileName);

        return $zipped;
    }
}