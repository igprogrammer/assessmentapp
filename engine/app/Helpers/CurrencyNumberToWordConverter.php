<?php

namespace App\Helpers;

class CurrencyNumberToWordConverter
{


    public static function convertNumber($number, $words = 'Tanzanian Shillings')
    {
        //list($integer,$fraction) = explode(".", (string) $number);
        list($integer, $fraction) = array_pad(explode(".", (string)$number), 2, null);

        $output = " ";

        if ($integer[0] == "-") {
            $output = "negative ";
            $integer = ltrim($integer, "-");
        } else if ($integer[0] == "+") {
            $output = "positive ";
            $integer = ltrim($integer, "+");
        }

        if ($integer[0] == "0") {
            $output .= "zero";
        } else {
            $integer = str_pad($integer, 36, "0", STR_PAD_LEFT);
            $group = rtrim(chunk_split($integer, 3, " "), " ");
            $groups = explode(" ", $group);

            $groups2 = array();
            foreach ($groups as $g) {
                $groups2[] = self::convertThreeDigit($g[0], $g[1], $g[2]);
            }

            for ($z = 0; $z < count($groups2); $z++) {
                if ($groups2[$z] != "") {
                    $output .= $groups2[$z] . self::convertGroup(11 - $z) . (
                        $z < 11
                        && !array_search('', array_slice($groups2, $z + 1, -1))
                        && $groups2[11] != ''
                        && $groups[11][0] == '0'
                            ? " and "
                            : " "
                        );
                }
            }

            $output = rtrim($output, ", ");
        }

        if ($fraction > 0) {
            //$output .= " PESOS ";
            $output .= ' ' . $words . ' ';
            for ($i = 0; $i < strlen($fraction); $i++) {
                if ($fraction == 01) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and one cent only";

                        $i++;
                    endwhile;
                } else if ($fraction == 02) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and two cents only";

                        $i++;
                    endwhile;
                } else if ($fraction == 03) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and three cents only";

                        $i++;
                    endwhile;
                } else if ($fraction == 04) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and four cents only";

                        $i++;
                    endwhile;
                } else if ($fraction == 05) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and five cents only";

                        $i++;
                    endwhile;
                } else if ($fraction == 06) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and six cents only";

                        $i++;
                    endwhile;
                } else if ($fraction == 07) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " point seven cents only";

                        $i++;
                    endwhile;
                } else if ($fraction == 8 || $fraction == '08') {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and eight cents only";

                        $i++;
                    endwhile;
                } else if ($fraction == 9 || $fraction == '09') {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and nine cents only";

                        $i++;
                    endwhile;
                } else if ($fraction == 10) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and ten cents only";

                        $i++;
                    endwhile;
                } else if ($fraction == 11) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and eleven cents only";

                        $i++;
                    endwhile;
                } else if ($fraction == 12) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and twelve cents only";

                        $i++;
                    endwhile;
                } else if ($fraction == 13) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and thirteen cents only";

                        $i++;
                    endwhile;
                } else if ($fraction == 14) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fourteen cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 15) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fifteen cents only";

                        $i++;
                    endwhile;
                } else if ($fraction == 16) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and sixteen cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 17) {


                    $i = 1;
                    while ($i < 2):
                        $output .= " and seventeen cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 18) {


                    $i = 1;
                    while ($i < 2):
                        $output .= " and eighteen cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 19) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and nineteen cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 20) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and twenty cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 21) {
                    $i = 1;
                    while ($i < 2):
                        $output .= " and twenty one cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 22) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and twenty two cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 23) {
                    $i = 1;
                    while ($i < 2):
                        $output .= " and twenty three cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 24) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and twenty four cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 25) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and twenty five cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 26) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and twenty six cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 27) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and twenty seven cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 28) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and twenty eight cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 29) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and twenty nine cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 30) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and thirty cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 31) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and thirty one cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 32) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and thirty two cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 33) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and thirty three cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 34) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and thirty four cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 35) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and thirty five cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 36) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and thirty six cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 37) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and thirty seven cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 38) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and thirty eight cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 39) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and thirty nine cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 40) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fourty cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 41) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fourty one cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 42) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fourty two cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 43) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fourty three cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 44) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fourty four cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 45) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fourty five cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 46) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fourty six cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 47) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fourty seven cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 48) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fourty eight cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 49) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fourty nine cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 50) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fifty cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 51) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fifty one cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 52) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fifty two cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 53) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fifty three cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 54) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fifty four cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 55) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fifty five cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 56) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fifty six cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 57) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fifty seven cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 58) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fifty eight cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 59) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and fifty nine cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 60) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and sixty cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 61) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and sixty one cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 62) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and sixty two cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 63) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and sixty three cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 64) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and sixty four cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 65) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and sixty five cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 66) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and sixty six cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 67) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and sixty seven cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 68) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and sixty eight cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 69) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and sixty nine cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 70) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and seventy cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 71) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and seventy one cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 72) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and seventy two cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 73) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and seventy three cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 74) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and seventy four cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 75) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and seventy five cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 76) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and seventy six cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 77) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and seventy seven cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 78) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and seventy eight cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 79) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and seventy nine cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 80) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and eighty cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 81) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and eighty one cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 82) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and eighty two cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 83) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and eighty three cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 84) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and eighty four cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 85) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and eighty five cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 86) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and eighty six cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 87) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and eighty seven cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 88) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and eighty eight cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 89) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and eighty nine cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 90) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and ninety cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 91) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and ninety one cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 92) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and ninety two cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 93) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and ninety three cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 94) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and ninety four cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 95) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and ninety five cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 96) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and ninety six cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 97) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and ninety seven cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 98) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and ninety eight cents only";
                        $i++;
                    endwhile;
                } else if ($fraction == 99) {

                    $i = 1;
                    while ($i < 2):
                        $output .= " and ninety nine cents only";
                        $i++;
                    endwhile;
                }

            }

        } else {
            //$output .= " ";
            $output .= ' ' . $words . ' only';
        }

        return $output;
    }

    public static function convertThreeDigit($digit1, $digit2, $digit3)
    {
        $buffer = " ";

        if ($digit1 == "0" && $digit2 == "0" && $digit3 == "0") {
            return "";
        }

        if ($digit1 != "0") {
            $buffer .= self::convertDigit($digit1) . " hundred";
            if ($digit2 != "0" || $digit3 != "0") {
                $buffer .= " ";
            }
        }

        if ($digit2 != "0") {
            $buffer .= self::convertTwoDigit($digit2, $digit3);
        } else if ($digit3 != "0") {
            $buffer .= self::convertDigit($digit3);
        }

        return $buffer;
    }

   public static function convertTwoDigit($digit1, $digit2)
    {
        if ($digit2 == "0") {
            switch ($digit1) {
                case "1":
                    return "ten";
                case "2":
                    return "twenty";
                case "3":
                    return "thirty";
                case "4":
                    return "forty";
                case "5":
                    return "fifty";
                case "6":
                    return "sixty";
                case "7":
                    return "seventy";
                case "8":
                    return "eighty";
                case "9":
                    return "ninety";
            }
        } else if ($digit1 == "1") {
            switch ($digit2) {
                case "1":
                    return "eleven";
                case "2":
                    return "twelve";
                case "3":
                    return "thirteen";
                case "4":
                    return "fourteen";
                case "5":
                    return "fifteen";
                case "6":
                    return "sixteen";
                case "7":
                    return "seventeen";
                case "8":
                    return "eighteen";
                case "9":
                    return "nineteen";
            }
        } else {
            $temp = self::convertDigit($digit2);
            switch ($digit1) {
                case "2":
                    return "twenty $temp";
                case "3":
                    return "thirty $temp";
                case "4":
                    return "forty $temp";
                case "5":
                    return "fifty $temp";
                case "6":
                    return "sixty $temp";
                case "7":
                    return "seventy $temp";
                case "8":
                    return "eighty $temp";
                case "9":
                    return "ninety $temp";
            }
        }
    }

   public static function convertDigit($digit)
    {
        switch ($digit) {
            case "0":
                return "zero";
            case "1":
                return "one";
            case "2":
                return "two";
            case "3":
                return "three";
            case "4":
                return "four";
            case "5":
                return "five";
            case "6":
                return "six";
            case "7":
                return "seven";
            case "8":
                return "eight";
            case "9":
                return "nine";
        }

    }

   public static function convertGroup($index)
    {
        switch ($index) {
            case 11:
                return " decillion";
            case 10:
                return " nonillion";
            case 9:
                return " octillion";
            case 8:
                return " septillion";
            case 7:
                return " sextillion";
            case 6:
                return " quintrillion";
            case 5:
                return " quadrillion";
            case 4:
                return " trillion";
            case 3:
                return " billion";
            case 2:
                return " million";
            case 1:
                return " thousand";
            case 0:
                return "";
        }
    }



}
