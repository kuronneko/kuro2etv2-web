<?php

namespace App\Services;

class File2eService
{

    static function convertStringToHex($text)
    {
        $charzedInput = str_split($text); // Split the input into an array of characters
        $hexInputContainer = '';

        foreach ($charzedInput as $char) {
            if ($char == "\n") {
                $hexInputContainer .= ';';
            }
            $hexInputContainer .= dechex(ord($char)); // Convert the character to its hexadecimal representation
        }

        return $hexInputContainer;
    }


    static function saveTextToHex($input)
    {
        $inputCutFirstPart = ''; // store first cut
        $inputCutSecondPart = ''; // store second cut
        $secondInputBuffered = ''; // join parts
        $secondHexInputContainer = '';

        $hexInput = File2eService::convertStringToHex($input);
        $halfLength = strlen($hexInput) / 2;

        $inputCutFirstPart = substr($hexInput, 0, $halfLength); // cut input
        $inputCutSecondPart = substr($hexInput, $halfLength); // second cut
        $secondInputBuffered = $inputCutSecondPart . $inputCutFirstPart; // Store input with the second part first and the first part second

        $secondHexInputContainer = File2eService::convertStringToHex($secondInputBuffered); // String to hex again and store in hex container
        $secondHexInputContainer = strrev($secondHexInputContainer); // Reverse the text

        return $secondHexInputContainer;
    }

    static function convertHexToString($hex)
    {
        $sb = '';
        $temp = '';

        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            // Grab the hex in pairs
            $output = substr($hex, $i, 2);
            // Convert hex to decimal
            $decimal = hexdec($output);
            // Convert the decimal to a character
            $sb .= chr($decimal);
            $temp .= $decimal;
        }

        return $sb;
    }

    static function loadHexToString($input)
    {
        $inputCutFirstPart = ''; // Store first cut
        $inputCutSecondPart = ''; // Store second cut
        $inputBuffered = strrev($input); // Reverse the input
        $inputConvertToString = File2eService::convertHexToString($inputBuffered); // Convert hexadecimal input to string

        // Check the length of the input to determine the cutting points
        $inputLength = strlen($inputConvertToString);
        $halfLength = $inputLength / 2;

        if ($inputLength % 2 == 0) {
            $inputCutFirstPart = substr($inputConvertToString, 0, $halfLength); // Cut input
            $inputCutSecondPart = substr($inputConvertToString, $halfLength); // Second cut
        } else {
            $inputCutFirstPart = substr($inputConvertToString, 0, $halfLength); // Cut input
            $inputCutSecondPart = substr($inputConvertToString, $halfLength - 1); // Second cut
        }

        return File2eService::convertHexToString($inputCutSecondPart . $inputCutFirstPart); // Join inputs and convert from hex to string again
    }
}
