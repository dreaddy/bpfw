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



/**
 * @return bool current user is an admin
 */
function bpfw_isAdmin(): bool
{

    $usertype = bpfw_getCurrentUsertype();

    return $usertype <= USERTYPE_ADMIN && $usertype >= 0;

    // return ($_SESSION['userType'] <= 1);

}

/**
 * @return bool is developer
 */
function bpfw_isDeveloper(): bool
{

    $usertype = bpfw_getCurrentUsertype();

    return $usertype <= USERTYPE_DEVELOPER && $usertype >= 0;

    // return ($_SESSION['userType'] <= 1);

}


/** @noinspection PhpUnused */
/**
 * @return array array of all default user ranks and their name
 * @throws Exception
 */
function bpfw_getUsertypeArray(): array
{

    return array(
        USERTYPE_CONSULTANT => __("Customer"),
        USERTYPE_SUPERADMIN => __("Developer"),
        USERTYPE_ADMIN => __("Administrator"),
        USERTYPE_CUSTOMER => __("No login"),
    );

}


function bpfw_hasConsultantPermissions(): bool
{

    $usertype = bpfw_getCurrentUsertype();

    return $usertype <= USERTYPE_CONSULTANT;

    // return ($_SESSION['userType'] <= 1);

}


/**
 * user has at least the given rank
 * @param $minUserRank int rank (0=superadmin, higher = less permissions)
 * @return bool
 */
function bpfw_hasPermission(int $minUserRank): bool
{
    $usertype = bpfw_getCurrentUsertype();

    return $usertype <= $minUserRank;
}

/**
 * get usertype/rank of current user
 * @return int
 */
function bpfw_getUsertype(): int
{
    return bpfw_getCurrentUsertype();
}

/**
 * get all userdata. contains the data of the userModel like username, rank and userId
 * @return array|null
 */
function bpfw_getUser() : array | null
{
    if (bpfw_isLoggedIn()) {
        return $_SESSION['userdata'];
    }
    return null;
}

/**
 * gets the username
 * @return ?string
 */
function bpfw_getUserName()
{
    if (bpfw_isLoggedIn()) {
        return $_SESSION['userdata']['username'];
    }
    return null;
}

/**
 * Userid of logged in User
 * @return mixed
 */
function bpfw_getUserId(): mixed
{
    if (bpfw_isLoggedIn()) {
        return $_SESSION['userdata']['userId'];
    }
    return -1;
}

/**
 * Is the visiting user logged in?
 * @return bool is logged in
 */
function bpfw_isLoggedIn(): bool
{
    return !empty($_SESSION['userdata']);
}


function bpfw_getUserTypeString($type): string
{

    return match ($type) {
        USERTYPE_CUSTOMER => "Kunde",
        USERTYPE_CONSULTANT => "Berater",
        USERTYPE_ADMIN => "Admin",
        USERTYPE_SUPERADMIN => "Superadmin",
        default => "Gast",
    };

}

/**
 * get a salutation array for comboboxes etc.
 *
 * @return array
 * @throws Exception
 */
function bpfw_getSalutationArray(): array
{

    return array(
        SALUTATION_MALE => __(SALUTATION_MALE),
        SALUTATION_FEMALE => __(SALUTATION_FEMALE),
        SALUTATION_NONE => __(SALUTATION_NONE)
    );

}


function bpfw_getSalutation_n($salutation): string
{

    if ($salutation == SALUTATION_NONE || $salutation == __(SALUTATION_NONE)) return "";

    return (SALUTATION_MALE == $salutation || __(SALUTATION_MALE) == $salutation) ? "Herrn" : "Frau";

}


function bpfw_getSalutationFull($salutation, $lastname = ""): string
{
    if ($salutation == SALUTATION_NEUTRAL) return __(SALUTATION_NEUTRAL);
    if ($salutation == SALUTATION_NONE) return __(SALUTATION_NEUTRAL);

    return ((SALUTATION_MALE == $salutation || __(SALUTATION_MALE) == $salutation) ? "Sehr geehrter" : "Sehr geehrte") . " " . __($salutation) . " " . $lastname;

}


//$variables["payer.salutation_n"]      = bpfw_getSalutation_n($payer->salutation); //(SALUTATION_MALE == $payer->salutation)?"Herrn":"Frau";

//$variables["payer.salutationFull"]      = bpfw_getSalutationFull($payer->salutation); // ((SALUTATION_MALE == $payer->salutation)?"Sehr geehrter":"Sehr geehrte")    . " " . $payer->salutation . " " . $payer->lastname;


/**
 * Generates a strong password of N length containing at least one lower case letter,
 * one uppercase letter, one digit, and one special character. The remaining characters
 * in the password are chosen at random from those four sets.
 *
 * The available characters in each set are user-friendly - there are no ambiguous
 * characters such as i, l, 1, o, 0, etc. This, coupled with the $add_dashes option,
 * makes it much easier for users to manually type or speak their passwords.
 *
 * Note: the $add_dashes option will increase the length of the password by floor(sqrt(N)) characters.
 * @param $length int length of password
 * @param $add_dashes bool
 * @param $available_sets string string out of "luds" (lowercase, uppercase, numbers, special chars). defines the set the password can contain.
 * @return string
 */
function bpfw_generatePassword(int $length = 9, bool $add_dashes = false, string $available_sets = 'luds'): string
{

    $sets = array();
    if (str_contains($available_sets, 'l'))
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
    if (str_contains($available_sets, 'u'))
        $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    if (str_contains($available_sets, 'd'))
        $sets[] = '23456789';
    if (str_contains($available_sets, 's'))
        $sets[] = '!@#$%&*?';
    $all = '';
    $password = '';
    foreach ($sets as $set) {
        $password .= $set[array_rand(str_split($set))];
        $all .= $set;
    }
    $all = str_split($all);
    for ($i = 0; $i < $length - count($sets); $i++)
        $password .= $all[array_rand($all)];
    $password = str_shuffle($password);
    if (!$add_dashes)
        return $password;
    $dash_len = floor(sqrt($length));
    $dash_str = '';
    while (strlen($password) > $dash_len) {
        $dash_str .= substr($password, 0, $dash_len) . '-';
        $password = substr($password, $dash_len);
    }
    $dash_str .= $password;
    return $dash_str;

}


/**
 *  Signature to Image: A supplemental script for Signature Pad that
 *  generates an image of the signature’s JSON output server-side using PHP.
 *
 * @project ca.thomasjbradley.applications.signaturetoimage
 * @author Thomas J Bradley <hey@thomasjbradley.ca>
 * @link http://thomasjbradley.ca/lab/signature-to-image
 * @link http://github.com/thomasjbradley/signature-to-image
 * @copyright Copyright MMXI–, Thomas J Bradley
 * @license New BSD License
 * @version 1.1.0
 */

/**
 *  Accepts a signature created by signature pad in Json format
 *  Converts it to an image resource
 *  The image resource can then be changed into png, jpg whatever PHP GD supports
 *
 *  To create a nicely anti-aliased graphic the signature is drawn 12 times its original size then shrunken
 *
 * @param array|string $json
 * @param array $options OPTIONAL; the options for image creation
 *    imageSize => array(width, height)
 *    bgColour => array(red, green, blue) | transparent
 *    penWidth => int
 *    penColour => array(red, green, blue)
 *    drawMultiplier => int
 *
 * @return resource
 * @noinspection DuplicatedCode
 */
function bpfw_signaturepad_sigJsonToImage(array|string $json, array $options = array())
{

    $defaultOptions = array(
        'imageSize' => array(450, 150)
    , 'bgColour' => array(0xff, 0xff, 0xff)
    , 'penWidth' => 2
    , 'penColour' => array(0x14, 0x53, 0x94)
    , 'drawMultiplier' => 12
    );

    $options = array_merge($defaultOptions, $options);

    $img = imagecreatetruecolor($options['imageSize'][0] * $options['drawMultiplier'], $options['imageSize'][1] * $options['drawMultiplier']);

    if ($options['bgColour'] == 'transparent') {
        imagesavealpha($img, true);
        $bg = imagecolorallocatealpha($img, 0, 0, 0, 127);
    } else {
        $bg = imagecolorallocate($img, $options['bgColour'][0], $options['bgColour'][1], $options['bgColour'][2]);
    }

    $pen = imagecolorallocate($img, $options['penColour'][0], $options['penColour'][1], $options['penColour'][2]);
    imagefill($img, 0, 0, $bg);

    if (is_string($json))
        $json = json_decode(stripslashes($json));

    foreach ($json as $v) {
        bpfw_signaturepad_drawThickLine($img, $v->lx * $options['drawMultiplier'], $v->ly * $options['drawMultiplier'], $v->mx * $options['drawMultiplier'], $v->my * $options['drawMultiplier'], $pen, $options['penWidth'] * ($options['drawMultiplier'] / 2));
    }

    $imgDest = imagecreatetruecolor($options['imageSize'][0], $options['imageSize'][1]);

    if ($options['bgColour'] == 'transparent') {
        imagealphablending($imgDest, false);
        imagesavealpha($imgDest, true);
    }

    imagecopyresampled($imgDest, $img, 0, 0, 0, 0, $options['imageSize'][0], $options['imageSize'][0], $options['imageSize'][0] * $options['drawMultiplier'], $options['imageSize'][0] * $options['drawMultiplier']);
    imagedestroy($img);

    return $imgDest;

}

/**
 *  Draws a thick line
 *  Changing the thickness of a line using imagesetthickness doesn't produce as nice of result
 *
 * @param resource $img
 * @param int $startX
 * @param int $startY
 * @param int $endX
 * @param int $endY
 * @param $color
 * @param int $thickness
 *
 * @return void
 * @noinspection DuplicatedCode
 */
function bpfw_signaturepad_drawThickLine($img, int $startX, int $startY, int $endX, int $endY, $color, int $thickness): void
{

    $angle = (atan2(($startY - $endY), ($endX - $startX)));

    $dist_x = $thickness * (sin($angle));
    $dist_y = $thickness * (cos($angle));

    $p1x = ceil(($startX + $dist_x));
    $p1y = ceil(($startY + $dist_y));
    $p2x = ceil(($endX + $dist_x));
    $p2y = ceil(($endY + $dist_y));
    $p3x = ceil(($endX - $dist_x));
    $p3y = ceil(($endY - $dist_y));
    $p4x = ceil(($startX - $dist_x));
    $p4y = ceil(($startY - $dist_y));

    $array = array(0 => $p1x, $p1y, $p2x, $p2y, $p3x, $p3y, $p4x, $p4y);
    imagefilledpolygon($img, $array, (count($array) / 2), $color);

}

/**
 * @return int usertype of current user
 */
function bpfw_getCurrentUsertype() : int
{

    $usertype = USERTYPE_GUEST;


    if (isset($_SESSION['usertype'])) {
        $usertype = $_SESSION['usertype'];
    }


    if (isset($_SESSION['userdata']->user->type)) {
        $usertype = $_SESSION['userdata']->user->type;
    }

    return $usertype;

}