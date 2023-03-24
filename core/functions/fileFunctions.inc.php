<?php
/*
 *
 * Copyright (c) 2017-2023. Torsten Lüders
 *
 * Part of the BPFW project. For documentation and support visit https://bpfw.org .
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 *
 */



function bpfw_copyFileUpload($filename, $tmp_name, $key, $subdirectory, $fileData, $srcIsUrl = false): bool|string
{


    if (!isset($subdirectory)) {
        echo "subdir not set bpfw_handleEventFileUpload";
        return "";
    }


    $newname = $key . "_" . str_replace(" ", "_", str_replace(".", "_", microtime())) . "_" . $filename; // me();

    $new_name = $newname;

    if (isset($key)) {
        $new_name = $key . DIRECTORY_SEPARATOR . $new_name;
    }

    $new_name = $subdirectory . DIRECTORY_SEPARATOR . $new_name;


    //$new_name = $new_name;

    $dir = dirname(UPLOADS_PATH . DIRECTORY_SEPARATOR . $new_name);

    $dir = bpfw_fix_dir($dir);

    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    // echo "name is '".$new_name."'";

    if (!$srcIsUrl) {

        if (!file_exists($tmp_name)) {
            return "";
        }

        copy($tmp_name, UPLOADS_PATH . DIRECTORY_SEPARATOR . $new_name);

    } else {

        //file_put_contents( UPLOADS_PATH.DIRECTORY_SEPARATOR.$new_name, $tmp_name);

        $ch = curl_init($tmp_name);
        $fp = fopen(UPLOADS_PATH . DIRECTORY_SEPARATOR . $new_name, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

    }

    $fileData["new_name"] = DIRECTORY_SEPARATOR . $new_name;


    // upload success now clean old uploads of same field

    /*$otherfiles = scandir($dir);

    foreach($otherfiles as $fileid_num=>$current_filename){

    if(!is_dir($dir.DIRECTORY_SEPARATOR.$current_filename)){

    if(bpfw_strStartsWith($current_filename, $key."_") && bpfw_strEndsWith($current_filename, $filename)){
    if($current_filename != $newname ){  // nicht der grade hochgeladene Eintrag,
    //   echo "ok3 $dir $current_filename";
    unlink($dir.DIRECTORY_SEPARATOR.$current_filename); // Datei löschen
    }

    }

    }
    }*/


    //bpfw_cleanAfterFileUpload($newdata, $this->GetTableName(), $foldergroup, $filedata);


    return json_encode($fileData);


}

/**
 * copy a file to the correct directory and return the data to be saved
 * @param $subdirectory string subdir in uploads folder
 * @param $key mixed key of the file.
 * @param $fileData array filedata as array as in php $_FILE (name, size, type, tmp_name, error)
 * @return bool|string
 */
function bpfw_handleFileUpload(string $subdirectory, mixed $key, array $fileData): bool|string
{

    $filename = $fileData["name"];
    //  $filetype = $filedata["type"];
    $tmp_name = $fileData["tmp_name"];
    $error = $fileData["error"];
    //  $size = $filedata["size"];

    if (empty($error)) {

        return bpfw_copyFileUpload($filename, $tmp_name, $key, $subdirectory, $fileData);

    } else {
        echo "upload Fehler: $error";
    }

    return "";
}


/**
 * copy a folder to a zip file
 * @param $source string path to folder
 * @param $dest string path  to zipfile to be created
 * @return void
 */
function bpfw_copy_dir_as_zip(string $source, string $dest): void
{

    if(!file_exists($source)) {
        echo "src $source not existing, creating!";
        mkdir($source);
    }

    if(!file_exists($dest)) {
        //echo "dst $dest not existing!";
    }

    $zip = new ZipArchive();
    $zip->open($dest, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {

        // Skip directories (they would be added automatically)
        if (!$file->isDir()) {

            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($source) + 1);

            // Add current file to archive
            $zip->addFile($filePath, $relativePath);

        }

    }

    // Zip archive will be created only after closing object
    $zip->close();


}


/**
 * get the size of a directory (includes all subdirectories)
 * @param $path string path to folder
 * @return int size in byte
 */
function bpfw_foldersize($path): int
{
    $total_size = 0;
    $files = scandir($path);

    foreach ($files as $t) {
        if (is_dir(rtrim($path, '/') . '' . $t)) {
            if ($t != "." && $t != "..") {
                $size = bpfw_foldersize(rtrim($path, '/\\') . DIRECTORY_SEPARATOR . $t);

                $total_size += $size;
            }
        } else {
            $size = filesize(rtrim($path, '/\\') . DIRECTORY_SEPARATOR . $t);
            $total_size += $size;
        }
    }
    return $total_size;
}

/**
 * count the number of files in a directory
 * @param $path string path to directory
 * @return int files found
 */
function bpfw_fileCount(string $path): int
{

    $total_count = 0;
    $files = scandir($path);

    $count = 0;

    foreach ($files as $t) {
        if (is_dir(rtrim($path, '/') . '' . $t)) {
            if ($t != "." && $t != "..") {
                $count = bpfw_fileCount(rtrim($path, '/') . '' . $t);
                $total_count += $count;
            }
        } else {
            $count = 1;
            $total_count += $count;
        }
    }

    return $total_count;

}

/**
 * formats a byte number to a more readable format string(22 KB, 33 MB etc.)
 * @param $size int size in byte
 * @return string formatted size
 */
function bpfw_format_size(int $size): string
{
    $mod = 1024;
    $units = explode(' ', 'B KB MB GB TB PB');
    for ($i = 0; $size > $mod; $i++) {
        $size /= $mod;
    }

    return round($size, 2) . ' files: ' . $units[$i];
}

/**
 * delete directory with its subdirectories
 * @param $dir string path to dir
 * @return bool success
 */
function bpfw_delete_dir(string $dir): bool
{
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? bpfw_delete_dir("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);

}



/**
 * Copy a file, or recursively copy a folder and its contents
 *
 * @param string $source Source path
 * @param string $dest Destination path
 * @return      bool     Returns TRUE on success, FALSE on failure
 */
function bpfw_copy_dir(string $source, string $dest): bool
{

    // Check for symlinks
    if (is_link($source)) {
        return symlink(readlink($source), $dest);
    }

    // Simple copy for a file
    if (is_file($source)) {
        return copy($source, $dest);
    }

    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest);
    }

    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        bpfw_copy_dir("$source/$entry", "$dest/$entry");
    }

    // Clean up
    $dir->close();
    return true;

}

/**
 * Copy a file, or recursively copy a folder and its contents
 * TODO: identical to bpfw_copy_dir
 *
 * @param string $src Source path
 * @param string $dst Destination path
 */
function bpfw_copy_directory(string $src, string $dst): void
{

    // create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($files as $file) {
        // rename files and directories
        $new = $dst . DIRECTORY_SEPARATOR . $files->getSubPathName();
        if ($file->isDir()) {
            mkdir($new);
        } else {
            copy($file, $new);
        }
    }

}