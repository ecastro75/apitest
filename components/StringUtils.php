<?php

namespace app\components;

use yii\helpers\StringHelper;

/**
 * This is a class for several String Utils methods
 */
class StringUtils extends StringHelper
{
	/**
	 * Checks if a String is not empty ("") and not null
	 * @param  string $string the String to check, may be null
	 * @return boolean
	 */
	public static function isNotEmpty($string)
	{
		if ($string === null) return false;
		if (strlen($string) === 0) return false;
		return true;
	}

	/**
	 * Checks if a String is not empty (""), not null and not whitespace only
	 * @param  string $string the String to check, may be null
	 * @return boolean
	 */
	public static function isNotBlank($string)
	{
		if ($string === null) return false;
		if (trim($string) === "") return false;
		return true;
	}

	/**
	 * Compares two Strings, returning true if they are equal.
	 * nulls are handled without exceptions. Two null
     * references are considered to be equal. The comparison is case sensitive.
	 * @param  string $string_1 the first String, may be null
	 * @param  string $string_2 the second String, may be null
	 * @return boolean
	 */
	public static function isEquals($string_1, $string_2)
	{
		return $string_1 === null ? $string_2 === null : $string_1 === $string_2;
	}

	/**
	 * Allow to generate a random string by a custom user length
	 * @param  integer $length limit of random strign
	 * @return string
	 */
	public static function generateRandomString($length = 10)
	{
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	/**
	 * Calculate the difference between two dates and return by a specific format
	 * @param  string $date_1
	 * @param  string $date_2
	 * @param  string $differenceFormat the return format [D = Days, M = Months, Y = Years, H = Hours, I = Minutes, S = Seconds]
	 * @return integer
	 */
	public static function getDateDifference($date_1, $date_2, $differenceFormat = '%a')
	{
		$datetime1 = date_create($date_1);
    	$datetime2 = date_create($date_2);
    	$interval = date_diff($datetime1, $datetime2);

    	return (int) $interval->format($differenceFormat);
	}
}