<?php

namespace Clickatell;

/**
 * Converts an UTF-8 string to a Clickatell message.
 *
 * @internal
 */
class MessageConverter
{
    /**
     * The mapping of all characters that can be safely transmitted without Unicode.
     *
     * Other characters are either replaced with a close match, or with a question mark, so we don't use them.
     *
     * The keys are UCS-2BE chars.
     *
     * As far as I know, Clickatell does not have a precise documentation
     * of how they perform conversion to the GSM 03.38 charset,
     * and consequently which characters are safe to use.
     *
     * This list is therefore the result of a manual testing of all 8-bit char codes.
     * The tests have been peformed on an iPhone 4S on Free Mobile, France.
     *
     * I could match the resulting table to a subset of Windows-1252.
     * Only the euro sign is present in the range that differentiates Windows-1252 from ISO-8859-1 (0x80 to 0x9F).
     *
     * Because there are several language-specific variants of GSM 03.38, I can only assume for now that this
     * character table is valid for other countries, but extensive testing would be required as the list of safe
     * characters could very well depend on the target country or network.
     *
     * Feedback is welcome.
     *
     * @var array
     */
    private $chars = [
        "\x00\x0A" => "\x0A", // LF
        "\x00\x0D" => "\x0D", // CR
        "\x00\x20" => "\x20", // SPACE
        "\x00\x21" => "\x21", // !
        "\x00\x22" => "\x22", // "
        "\x00\x23" => "\x23", // #
        "\x00\x24" => "\x24", // $
        "\x00\x25" => "\x25", // %
        "\x00\x26" => "\x26", // &
        "\x00\x27" => "\x27", // '
        "\x00\x28" => "\x28", // (
        "\x00\x29" => "\x29", // )
        "\x00\x2A" => "\x2A", // *
        "\x00\x2B" => "\x2B", // +
        "\x00\x2C" => "\x2C", // ,
        "\x00\x2D" => "\x2D", // -
        "\x00\x2E" => "\x2E", // .
        "\x00\x2F" => "\x2F", // /
        "\x00\x30" => "\x30", // 0
        "\x00\x31" => "\x31", // 1
        "\x00\x32" => "\x32", // 2
        "\x00\x33" => "\x33", // 3
        "\x00\x34" => "\x34", // 4
        "\x00\x35" => "\x35", // 5
        "\x00\x36" => "\x36", // 6
        "\x00\x37" => "\x37", // 7
        "\x00\x38" => "\x38", // 8
        "\x00\x39" => "\x39", // 9
        "\x00\x3A" => "\x3A", // :
        "\x00\x3B" => "\x3B", // ;
        "\x00\x3C" => "\x3C", // <
        "\x00\x3D" => "\x3D", // =
        "\x00\x3E" => "\x3E", // >
        "\x00\x3F" => "\x3F", // ?
        "\x00\x40" => "\x40", // @
        "\x00\x41" => "\x41", // A
        "\x00\x42" => "\x42", // B
        "\x00\x43" => "\x43", // C
        "\x00\x44" => "\x44", // D
        "\x00\x45" => "\x45", // E
        "\x00\x46" => "\x46", // F
        "\x00\x47" => "\x47", // G
        "\x00\x48" => "\x48", // H
        "\x00\x49" => "\x49", // I
        "\x00\x4A" => "\x4A", // J
        "\x00\x4B" => "\x4B", // K
        "\x00\x4C" => "\x4C", // L
        "\x00\x4D" => "\x4D", // M
        "\x00\x4E" => "\x4E", // N
        "\x00\x4F" => "\x4F", // O
        "\x00\x50" => "\x50", // P
        "\x00\x51" => "\x51", // Q
        "\x00\x52" => "\x52", // R
        "\x00\x53" => "\x53", // S
        "\x00\x54" => "\x54", // T
        "\x00\x55" => "\x55", // U
        "\x00\x56" => "\x56", // V
        "\x00\x57" => "\x57", // W
        "\x00\x58" => "\x58", // X
        "\x00\x59" => "\x59", // Y
        "\x00\x5A" => "\x5A", // Z
        "\x00\x5B" => "\x5B", // [
        "\x00\x5C" => "\x5C", // \
        "\x00\x5D" => "\x5D", // ]
        "\x00\x5E" => "\x5E", // ^
        "\x00\x5F" => "\x5F", // _
        "\x00\x61" => "\x61", // a
        "\x00\x62" => "\x62", // b
        "\x00\x63" => "\x63", // c
        "\x00\x64" => "\x64", // d
        "\x00\x65" => "\x65", // e
        "\x00\x66" => "\x66", // f
        "\x00\x67" => "\x67", // g
        "\x00\x68" => "\x68", // h
        "\x00\x69" => "\x69", // i
        "\x00\x6A" => "\x6A", // j
        "\x00\x6B" => "\x6B", // k
        "\x00\x6C" => "\x6C", // l
        "\x00\x6D" => "\x6D", // m
        "\x00\x6E" => "\x6E", // n
        "\x00\x6F" => "\x6F", // o
        "\x00\x70" => "\x70", // p
        "\x00\x71" => "\x71", // q
        "\x00\x72" => "\x72", // r
        "\x00\x73" => "\x73", // s
        "\x00\x74" => "\x74", // t
        "\x00\x75" => "\x75", // u
        "\x00\x76" => "\x76", // v
        "\x00\x77" => "\x77", // w
        "\x00\x78" => "\x78", // x
        "\x00\x79" => "\x79", // y
        "\x00\x7A" => "\x7A", // z
        "\x00\x7B" => "\x7B", // {
        "\x00\x7C" => "\x7C", // |
        "\x00\x7D" => "\x7D", // }
        "\x00\x7E" => "\x7E", // ~
        "\x20\xAC" => "\x80", // €
        "\x00\xA1" => "\xA1", // ¡
        "\x00\xA3" => "\xA3", // £
        "\x00\xA4" => "\xA4", // ¤
        "\x00\xA5" => "\xA5", // ¥
        "\x00\xA7" => "\xA7", // §
        "\x00\xBF" => "\xBF", // ¿
        "\x00\xC4" => "\xC4", // Ä
        "\x00\xC5" => "\xC5", // Å
        "\x00\xC6" => "\xC6", // Æ
        "\x00\xC9" => "\xC9", // É
        "\x00\xD1" => "\xD1", // Ñ
        "\x00\xD6" => "\xD6", // Ö
        "\x00\xD8" => "\xD8", // Ø
        "\x00\xDC" => "\xDC", // Ü
        "\x00\xDF" => "\xDF", // ß
        "\x00\xE0" => "\xE0", // à
        "\x00\xE4" => "\xE4", // ä
        "\x00\xE5" => "\xE5", // å
        "\x00\xE6" => "\xE6", // æ
        "\x00\xE8" => "\xE8", // è
        "\x00\xE9" => "\xE9", // é
        "\x00\xEC" => "\xEC", // ì
        "\x00\xF1" => "\xF1", // ñ
        "\x00\xF2" => "\xF2", // ò
        "\x00\xF6" => "\xF6", // ö
        "\x00\xF8" => "\xF8", // ø
        "\x00\xF9" => "\xF9", // ù
        "\x00\xFC" => "\xFC", // ü
    ];

    /**
     * @param string $message
     *
     * @return Message
     *
     * @throws ClickatellException If the message is not valid UTF-8.
     */
    public function convert($message)
    {
        $message = (string) $message;

        if (! mb_check_encoding($message, 'UTF-8')) {
            throw new ClickatellException('The message must be in UTF-8 format.');
        }

        // Convert the message to UCS-2 big endian.
        $message = mb_convert_encoding($message, 'UCS-2BE', 'UTF-8');

        $length = strlen($message);
        $result = '';
        $isSafe = true;

        for ($i = 0; $i < $length; $i += 2) {
            $char = $message[$i] . $message[$i + 1];
            if (isset($this->chars[$char])) {
                $result .= $this->chars[$char];
            } else {
                $isSafe = false;
            }
        }

        $messageResult = new Message();

        $messageResult->data = $isSafe ? $result : bin2hex($message);
        $messageResult->isUnicode = ! $isSafe;

        return $messageResult;
    }
}
