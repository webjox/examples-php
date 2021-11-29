<?php

namespace entity;

class RevertClass
{
    /**
     * @return string
     *  Принимает на вход строку и меняет порядок букв в каждом
     *  слове на обратный с сохранением регистра и пунктуации.
     */
    public static function revertCharacters($str)
    {
        $array = explode(" ", $str);  //Split string to array of words
        foreach ($array as $item) {
            $length = mb_strlen($item);  //Length string in UTF-8
            $chars = preg_split('//u', $item); //Split word to array of chars
            for ($i = 1; $i <= $length; $i++) {
                $new_letter[$i] = $chars[$length - $i + 1]; //Revert source word
                if ($chars[$i] === "?" || $chars[$i] === "!" || $chars[$i] === ".") { // Save previous marks
                    $new_letter[$length + 1] = $chars[$i]; //
                    unset($new_letter[1]);
                }
            }
            $new_str = implode("", $new_letter); //Merge array of chars into word
            if (mb_strtolower($new_str) !== $new_str) { //Check register source word
                $new_str = mb_strtolower($new_str);
                $new_str = mb_convert_case($new_str, MB_CASE_TITLE, "UTF-8");
            }
            $revert_str[] = $new_str; //Merge array of words into source string
            $new_letter = array(); //Clear array
        }
        return implode(" ", $revert_str);
    }
}


