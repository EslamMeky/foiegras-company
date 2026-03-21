<?php

function uploadImage($folder,$image)
{
    $image->store('/',$folder);
    $filename=$image->hashName();
    $path=$filename;
    return $path;
}

function generateRandomCode($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@^&*()';
    $charactersLength = strlen($characters);
    $randomCode = '';
    for ($i = 0; $i < $length; $i++) {
        $randomCode .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomCode;
}

/**
 * Generate a unique random code for a specific table and column.
 *
 * @param string $table The name of the table to check uniqueness.
 * @param string $column The column name to check uniqueness.
 * @param int $length The length of the random code.
 * @return string A unique random code.
 */
function generateUniqueRandomCode($table, $column, $length = 10)
{
    do {
        $code = generateRandomCode($length);
    } while (\Illuminate\Support\Facades\DB::table($table)->where($column, $code)->exists());

    return $code;
}





