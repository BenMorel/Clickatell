<?php

namespace Clickatell;

/**
 * Converts unicode strings to the Clickatell charset (a modification of GSM 03.38).
 *
 * @see http://en.wikipedia.org/wiki/GSM_03.38
 * @see ftp://ftp.unicode.org/Public/MAPPINGS/ETSI/GSM0338.TXT
 *
 * @see http://support.clickatell.com/faq.php?mode=view_entry&kbid=121&kbcat
 */
class CharsetConverter
{
    /**
     * An associative array mapping UCS-2 (big endian) unicode chars to GSM 03.38 chars.
     *
     * @var array
     */
    private $map = [
        "\x00\x40" => "\x00",     // COMMERCIAL AT
        "\x00\x00" => "\x00",     // NULL (see note above)
        "\x00\xA3" => "\x01",     // POUND SIGN
        "\x00\x24" => "\x02",     // DOLLAR SIGN
        "\x00\xA5" => "\x03",     // YEN SIGN
        "\x00\xE8" => "\x04",     // LATIN SMALL LETTER E WITH GRAVE
        "\x00\xE9" => "\x05",     // LATIN SMALL LETTER E WITH ACUTE
        "\x00\xF9" => "\x06",     // LATIN SMALL LETTER U WITH GRAVE
        "\x00\xEC" => "\x07",     // LATIN SMALL LETTER I WITH GRAVE
        "\x00\xF2" => "\x08",     // LATIN SMALL LETTER O WITH GRAVE
        "\x00\xE7" => "\x09",     // LATIN SMALL LETTER C WITH CEDILLA
        "\x00\xC7" => "\x09",     // LATIN CAPITAL LETTER C WITH CEDILLA (see note above)
        "\x00\x0A" => "\x0A",     // LINE FEED
        "\x00\xD8" => "\x0B",     // LATIN CAPITAL LETTER O WITH STROKE
        "\x00\xF8" => "\x0C",     // LATIN SMALL LETTER O WITH STROKE
        "\x00\x0D" => "\x0D",     // CARRIAGE RETURN
        "\x00\xC5" => "\x0E",     // LATIN CAPITAL LETTER A WITH RING ABOVE
        "\x00\xE5" => "\x0F",     // LATIN SMALL LETTER A WITH RING ABOVE
        "\x03\x94" => "\x10",     // GREEK CAPITAL LETTER DELTA
        "\x00\x5F" => "\x11",     // LOW LINE
        "\x03\xA6" => "\x12",     // GREEK CAPITAL LETTER PHI
        "\x03\x93" => "\x13",     // GREEK CAPITAL LETTER GAMMA
        "\x03\x9B" => "\x14",     // GREEK CAPITAL LETTER LAMDA
        "\x03\xA9" => "\x15",     // GREEK CAPITAL LETTER OMEGA
        "\x03\xA0" => "\x16",     // GREEK CAPITAL LETTER PI
        "\x03\xA8" => "\x17",     // GREEK CAPITAL LETTER PSI
        "\x03\xA3" => "\x18",     // GREEK CAPITAL LETTER SIGMA
        "\x03\x98" => "\x19",     // GREEK CAPITAL LETTER THETA
        "\x03\x9E" => "\x1A",     // GREEK CAPITAL LETTER XI
        "\x00\xA0" => "\x1B",     // ESCAPE TO EXTENSION TABLE (or displayed as NBSP, see note above)
        "\x00\x0C" => "\x1B\x0A", // FORM FEED
        "\x00\x5E" => "\x1B\x14", // CIRCUMFLEX ACCENT
        "\x00\x7B" => "\x1B\x28", // LEFT CURLY BRACKET
        "\x00\x7D" => "\x1B\x29", // RIGHT CURLY BRACKET
        "\x00\x5C" => "\x1B\x2F", // REVERSE SOLIDUS
        "\x00\x5B" => "\x1B\x3C", // LEFT SQUARE BRACKET
        "\x00\x7E" => "\x1B\x3D", // TILDE
        "\x00\x5D" => "\x1B\x3E", // RIGHT SQUARE BRACKET
        "\x00\x7C" => "\x1B\x40", // VERTICAL LINE
        "\x20\xAC" => "\x1B\x65", // EURO SIGN
        "\x00\xC6" => "\x1C",     // LATIN CAPITAL LETTER AE
        "\x00\xE6" => "\x1D",     // LATIN SMALL LETTER AE
        "\x00\xDF" => "\x1E",     // LATIN SMALL LETTER SHARP S (German)
        "\x00\xC9" => "\x1F",     // LATIN CAPITAL LETTER E WITH ACUTE
        "\x00\x20" => "\x20",     // SPACE
        "\x00\x21" => "\x21",     // EXCLAMATION MARK
        "\x00\x22" => "\x22",     // QUOTATION MARK
        "\x00\x23" => "\x23",     // NUMBER SIGN
        "\x00\xA4" => "\x24",     // CURRENCY SIGN
        "\x00\x25" => "\x25",     // PERCENT SIGN
        "\x00\x26" => "\x26",     // AMPERSAND
        "\x00\x27" => "\x27",     // APOSTROPHE
        "\x00\x28" => "\x28",     // LEFT PARENTHESIS
        "\x00\x29" => "\x29",     // RIGHT PARENTHESIS
        "\x00\x2A" => "\x2A",     // ASTERISK
        "\x00\x2B" => "\x2B",     // PLUS SIGN
        "\x00\x2C" => "\x2C",     // COMMA
        "\x00\x2D" => "\x2D",     // HYPHEN-MINUS
        "\x00\x2E" => "\x2E",     // FULL STOP
        "\x00\x2F" => "\x2F",     // SOLIDUS
        "\x00\x30" => "\x30",     // DIGIT ZERO
        "\x00\x31" => "\x31",     // DIGIT ONE
        "\x00\x32" => "\x32",     // DIGIT TWO
        "\x00\x33" => "\x33",     // DIGIT THREE
        "\x00\x34" => "\x34",     // DIGIT FOUR
        "\x00\x35" => "\x35",     // DIGIT FIVE
        "\x00\x36" => "\x36",     // DIGIT SIX
        "\x00\x37" => "\x37",     // DIGIT SEVEN
        "\x00\x38" => "\x38",     // DIGIT EIGHT
        "\x00\x39" => "\x39",     // DIGIT NINE
        "\x00\x3A" => "\x3A",     // COLON
        "\x00\x3B" => "\x3B",     // SEMICOLON
        "\x00\x3C" => "\x3C",     // LESS-THAN SIGN
        "\x00\x3D" => "\x3D",     // EQUALS SIGN
        "\x00\x3E" => "\x3E",     // GREATER-THAN SIGN
        "\x00\x3F" => "\x3F",     // QUESTION MARK
        "\x00\xA1" => "\x40",     // INVERTED EXCLAMATION MARK
        "\x00\x41" => "\x41",     // LATIN CAPITAL LETTER A
        "\x03\x91" => "\x41",     // GREEK CAPITAL LETTER ALPHA
        "\x00\x42" => "\x42",     // LATIN CAPITAL LETTER B
        "\x03\x92" => "\x42",     // GREEK CAPITAL LETTER BETA
        "\x00\x43" => "\x43",     // LATIN CAPITAL LETTER C
        "\x00\x44" => "\x44",     // LATIN CAPITAL LETTER D
        "\x00\x45" => "\x45",     // LATIN CAPITAL LETTER E
        "\x03\x95" => "\x45",     // GREEK CAPITAL LETTER EPSILON
        "\x00\x46" => "\x46",     // LATIN CAPITAL LETTER F
        "\x00\x47" => "\x47",     // LATIN CAPITAL LETTER G
        "\x00\x48" => "\x48",     // LATIN CAPITAL LETTER H
        "\x03\x97" => "\x48",     // GREEK CAPITAL LETTER ETA
        "\x00\x49" => "\x49",     // LATIN CAPITAL LETTER I
        "\x03\x99" => "\x49",     // GREEK CAPITAL LETTER IOTA
        "\x00\x4A" => "\x4A",     // LATIN CAPITAL LETTER J
        "\x00\x4B" => "\x4B",     // LATIN CAPITAL LETTER K
        "\x03\x9A" => "\x4B",     // GREEK CAPITAL LETTER KAPPA
        "\x00\x4C" => "\x4C",     // LATIN CAPITAL LETTER L
        "\x00\x4D" => "\x4D",     // LATIN CAPITAL LETTER M
        "\x03\x9C" => "\x4D",     // GREEK CAPITAL LETTER MU
        "\x00\x4E" => "\x4E",     // LATIN CAPITAL LETTER N
        "\x03\x9D" => "\x4E",     // GREEK CAPITAL LETTER NU
        "\x00\x4F" => "\x4F",     // LATIN CAPITAL LETTER O
        "\x03\x9F" => "\x4F",     // GREEK CAPITAL LETTER OMICRON
        "\x00\x50" => "\x50",     // LATIN CAPITAL LETTER P
        "\x03\xA1" => "\x50",     // GREEK CAPITAL LETTER RHO
        "\x00\x51" => "\x51",     // LATIN CAPITAL LETTER Q
        "\x00\x52" => "\x52",     // LATIN CAPITAL LETTER R
        "\x00\x53" => "\x53",     // LATIN CAPITAL LETTER S
        "\x00\x54" => "\x54",     // LATIN CAPITAL LETTER T
        "\x03\xA4" => "\x54",     // GREEK CAPITAL LETTER TAU
        "\x00\x55" => "\x55",     // LATIN CAPITAL LETTER U
        "\x00\x56" => "\x56",     // LATIN CAPITAL LETTER V
        "\x00\x57" => "\x57",     // LATIN CAPITAL LETTER W
        "\x00\x58" => "\x58",     // LATIN CAPITAL LETTER X
        "\x03\xA7" => "\x58",     // GREEK CAPITAL LETTER CHI
        "\x00\x59" => "\x59",     // LATIN CAPITAL LETTER Y
        "\x03\xA5" => "\x59",     // GREEK CAPITAL LETTER UPSILON
        "\x00\x5A" => "\x5A",     // LATIN CAPITAL LETTER Z
        "\x03\x96" => "\x5A",     // GREEK CAPITAL LETTER ZETA
        "\x00\xC4" => "\x5B",     // LATIN CAPITAL LETTER A WITH DIAERESIS
        "\x00\xD6" => "\x5C",     // LATIN CAPITAL LETTER O WITH DIAERESIS
        "\x00\xD1" => "\x5D",     // LATIN CAPITAL LETTER N WITH TILDE
        "\x00\xDC" => "\x5E",     // LATIN CAPITAL LETTER U WITH DIAERESIS
        "\x00\xA7" => "\x5F",     // SECTION SIGN
        "\x00\xBF" => "\x60",     // INVERTED QUESTION MARK
        "\x00\x61" => "\x61",     // LATIN SMALL LETTER A
        "\x00\x62" => "\x62",     // LATIN SMALL LETTER B
        "\x00\x63" => "\x63",     // LATIN SMALL LETTER C
        "\x00\x64" => "\x64",     // LATIN SMALL LETTER D
        "\x00\x65" => "\x65",     // LATIN SMALL LETTER E
        "\x00\x66" => "\x66",     // LATIN SMALL LETTER F
        "\x00\x67" => "\x67",     // LATIN SMALL LETTER G
        "\x00\x68" => "\x68",     // LATIN SMALL LETTER H
        "\x00\x69" => "\x69",     // LATIN SMALL LETTER I
        "\x00\x6A" => "\x6A",     // LATIN SMALL LETTER J
        "\x00\x6B" => "\x6B",     // LATIN SMALL LETTER K
        "\x00\x6C" => "\x6C",     // LATIN SMALL LETTER L
        "\x00\x6D" => "\x6D",     // LATIN SMALL LETTER M
        "\x00\x6E" => "\x6E",     // LATIN SMALL LETTER N
        "\x00\x6F" => "\x6F",     // LATIN SMALL LETTER O
        "\x00\x70" => "\x70",     // LATIN SMALL LETTER P
        "\x00\x71" => "\x71",     // LATIN SMALL LETTER Q
        "\x00\x72" => "\x72",     // LATIN SMALL LETTER R
        "\x00\x73" => "\x73",     // LATIN SMALL LETTER S
        "\x00\x74" => "\x74",     // LATIN SMALL LETTER T
        "\x00\x75" => "\x75",     // LATIN SMALL LETTER U
        "\x00\x76" => "\x76",     // LATIN SMALL LETTER V
        "\x00\x77" => "\x77",     // LATIN SMALL LETTER W
        "\x00\x78" => "\x78",     // LATIN SMALL LETTER X
        "\x00\x79" => "\x79",     // LATIN SMALL LETTER Y
        "\x00\x7A" => "\x7A",     // LATIN SMALL LETTER Z
        "\x00\xE4" => "\x7B",     // LATIN SMALL LETTER A WITH DIAERESIS
        "\x00\xF6" => "\x7C",     // LATIN SMALL LETTER O WITH DIAERESIS
        "\x00\xF1" => "\x7D",     // LATIN SMALL LETTER N WITH TILDE
        "\x00\xFC" => "\x7E",     // LATIN SMALL LETTER U WITH DIAERESIS
        "\x00\xE0" => "\x7F"      // LATIN SMALL LETTER A WITH GRAVE
    ];

    /**
     * An array of latin1 chars that are used "as is" by Clickatell.
     *
     * @var array
     */
    private $clickatellLatin1Chars = [
        "\x0c", "\x5b", "\x5c", "\x5d", "\x5e", "\x5f", "\x7b", "\x7c", "\x7d",
        "\x7e", "\xa0", "\xa1", "\xa3", "\xa4", "\xa5", "\xa7", "\xbf",
        "\xc4", "\xc5", "\xc6", "\xc7", "\xc9", "\xd1", "\xd6", "\xd8",
        "\xdc", "\xdf", "\xe0", "\xe4", "\xe5", "\xe8", "\xe9", "\xec",
        "\xf1", "\xf2", "\xf6", "\xf8", "\xf9", "\xfc"
    ];

    /**
     * The final Clickatell map, built dynamically from the two arrays above.
     *
     * @var array
     */
    private $clickatellMap = [];

    /**
     * Class constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->clickatellMap = $this->map;

        foreach ($this->clickatellLatin1Chars as $char) {
            $this->clickatellMap["\0" . $char] = $char;
        }
    }

    /**
     * Converts an UCS-2 string to the Clickatell 7-bit GSM format.
     *
     * @param string $string The string to convert, validated as UCS-2 big endian.
     *
     * @return string|null The converted string, or null if any character can't be converted.
     */
    public function convert($string)
    {
        $length = strlen($string);
        $result = '';

        for ($i = 0; $i < $length; $i += 2) {
            $char = substr($string, $i, 2);
            if (isset($this->clickatellMap[$char])) {
                $result .= $this->clickatellMap[$char];
            }
            else {
                return null;
            }
        }

        return $result;
    }
}
