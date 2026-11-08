<?php

namespace common\library;

use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Helpers for writing files to private storage (e.g. @uploads).
 *
 * Missing directories are created (recursively) on demand, so a fresh deployment does not
 * need the storage tree to exist beforehand. Failures are logged with the reason
 * (typically: the web server user cannot write to the parent directory) and reported as
 * `false` instead of throwing, so callers can show a normal validation/flash message.
 */
class FileStorage
{
    /** Permissions for directories created here (owner + group writable). */
    public const DIR_MODE = 0775;

    /**
     * Makes sure $dir exists and is writable, creating it and any missing parents.
     */
    public static function ensureDirectory(string $dir): bool
    {
        try {
            FileHelper::createDirectory($dir, self::DIR_MODE, true);
        } catch (\Throwable $e) {
            Yii::error("Could not create directory {$dir}: {$e->getMessage()}" . self::permissionHint($dir), __METHOD__);
            return false;
        }

        if (!is_writable($dir)) {
            Yii::error("Directory {$dir} is not writable." . self::permissionHint($dir), __METHOD__);
            return false;
        }

        return true;
    }

    /**
     * Saves an uploaded file to $path, creating the target directory first if it is missing.
     */
    public static function saveUploadedFile(UploadedFile $file, string $path): bool
    {
        if (!self::ensureDirectory(dirname($path))) {
            return false;
        }

        if (!$file->saveAs($path)) {
            Yii::error("Could not save uploaded file '{$file->name}' to {$path} (upload error code {$file->error}).", __METHOD__);
            return false;
        }

        return true;
    }

    /** Names the process user and the nearest existing parent, to make permission errors actionable. */
    private static function permissionHint(string $dir): string
    {
        $parent = $dir;
        while (!is_dir($parent) && dirname($parent) !== $parent) {
            $parent = dirname($parent);
        }

        $user = function_exists('posix_geteuid') && function_exists('posix_getpwuid')
            ? (posix_getpwuid(posix_geteuid())['name'] ?? (string) posix_geteuid())
            : get_current_user();

        return " Process user '{$user}' needs write access to {$parent}"
            . (is_writable($parent) ? '.' : ' (currently not writable).');
    }
}
