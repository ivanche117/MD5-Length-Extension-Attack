<?php

function md5_length_extension_file($secret_length, $file_path, $append_data) {
    $data = @file_get_contents($file_path);

    if ($data === false) {
        die("Failed to read file: $file_path");
    }

    $md5_ctx = new MD5State();
    $state = split_md5_hash(md5_file($file_path));

    $md5_ctx->setState($state);

    list($padding, $display_padding) = md5_padding($secret_length + strlen($data));

    $md5_ctx->update($append_data);

    $new_hash = $md5_ctx->final();

    $extended_data = $data . $display_padding . $append_data;

    return array($extended_data, $new_hash, $display_padding);
}



function md5_padding($message_length) {
    $padding = chr(0x80);
    $padding .= str_repeat(chr(0x00), (56 - ($message_length + 1) % 64) % 64);
    $padding .= pack('V2', $message_length << 3, 0);

    $display_padding = str_repeat("0", (56 - ($message_length + 1) % 64) % 64);
    $display_padding .= str_repeat("0", 8);

    return array($padding, $display_padding);
}



function split_md5_hash($hash) {
    return array(
    hexdec(substr($hash, 0, 8)),
    hexdec(substr($hash, 8, 8)),
    hexdec(substr($hash, 16, 8)),
    hexdec(substr($hash, 24, 8))
    );
}

class MD5State {
    private $state;
    private $buffer;
    private $count;

    public function __construct() {
        $this->state = array(0x67452301, 0xefcdab89, 0x98badcfe, 0x10325476);
        $this->buffer = '';
        $this->count = 0;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function update($data) {
        $this->buffer .= $data;
        $this->count += strlen($data);
    }

    public function final() {
        list($padding, ) = md5_padding($this->count);
        $this->update($padding);

        $this->transform($this->buffer);

        return sprintf('%08x%08x%08x%08x', $this->state[0], $this->state[1], $this->state[2], $this->state[3]);
    }

    private function transform($block) {
        $x = array_values(unpack('V16', $block));

        $a = $this->state[0];
        $b = $this->state[1];
        $c = $this->state[2];
        $d = $this->state[3];

        // MD5 rounds
        $this->md5_rounds($a, $b, $c, $d, $x);

        $this->state[0] = ($this->state[0] + $a) & 0xffffffff;
        $this->state[1] = ($this->state[1] + $b) & 0xffffffff;
        $this->state[2] = ($this->state[2] + $c) & 0xffffffff;
        $this->state[3] = ($this->state[3] + $d) & 0xffffffff;
    }

    private function md5_rounds(&$a, &$b, &$c, &$d, $x) {
        $a = $this->F($a, $b, $c, $d, $x[0], 7, 0xd76aa478);
        $d = $this->F($d, $a, $b, $c, $x[1], 12, 0xe8c7b756);
        $c = $this->F($c, $d, $a, $b, $x[2], 17, 0x242070db);
        $b = $this->F($b, $c, $d, $a, $x[3], 22, 0xc1bdceee);

        $a = $this->G($a, $b, $c, $d, $x[1], 5, 0xf61e2562);
        $d = $this->G($d, $a, $b, $c, $x[6], 9, 0xc040b340);
        $c = $this->G($c, $d, $a, $b, $x[11], 14, 0x265e5a51);
        $b = $this->G($b, $c, $d, $a, $x[0], 20, 0xe9b6c7aa);

        $a = $this->H($a, $b, $c, $d, $x[5], 4, 0xd62f105d);
        $d = $this->H($d, $a, $b, $c, $x[8], 11, 0x02441453);
        $c = $this->H($c, $d, $a, $b, $x[11], 16, 0xd8a1e681);
        $b = $this->H($b, $c, $d, $a, $x[14], 23, 0xe7d3fbc8);

        $a = $this->I($a, $b, $c, $d, $x[0], 6, 0x21e1cde6);
        $d = $this->I($d, $a, $b, $c, $x[7], 10, 0xc33707d6);
        $c = $this->I($c, $d, $a, $b, $x[14], 15, 0xf4d50d87);
        $b = $this->I($b, $c, $d, $a, $x[5], 21, 0x455a14ed);
    }

    private function F($a, $b, $c, $d, $x, $s, $ac) {
        $a = ($a + (($b & $c) | (~$b & $d)) + $x + $ac) & 0xffffffff;
        return (($a << $s) | ($a >> (32 - $s))) & 0xffffffff;
    }

    private function G($a, $b, $c, $d, $x, $s, $ac) {
        $a = ($a + (($b & $d) | ($c & ~$d)) + $x + $ac) & 0xffffffff;
        return (($a << $s) | ($a >> (32 - $s))) & 0xffffffff;
    }

    private function H($a, $b, $c, $d, $x, $s, $ac) {
        $a = ($a + ($b ^ $c ^ $d) + $x + $ac) & 0xffffffff;
        return (($a << $s) | ($a >> (32 - $s))) & 0xffffffff;
    }

    private function I($a, $b, $c, $d, $x, $s, $ac) {
        $a = ($a + ($c ^ ($b | ~$d)) + $x + $ac) & 0xffffffff;
        return (($a << $s) | ($a >> (32 - $s))) & 0xffffffff;
    }
}


    $file_path = "path/to/your/logs.txt";
    $secret_length = strlen("secret");
    $append = "\n\n2023-06-18 15:30:00: User7 performed action D";

    list($extended_data, $new_hash, $display_padding) = md5_length_extension_file($secret_length, $file_path, $append);

    $data = @file_get_contents($file_path);
    if ($extended_data !== false && $new_hash !== false && $display_padding !== false) {
        $display_extended_data = $extended_data;



    $original_hash = md5_file($file_path);
    echo "<p><strong>Original data: </strong>" . $data . "</p>";
    echo "<p><strong>Original hash: </strong>" . $original_hash . "</p>";

    echo "<p style='width:500px;'> <strong>Extended Message (Hex):<br></strong>" . bin2hex($extended_data) . "</p>";
    echo "<p><strong>Extended Message (Plaintext):<br></strong>" . $display_extended_data . "</p>";
    echo "<p><strong>New Hash:</strong>" . $new_hash . "</p>";
    }

    $state = split_md5_hash(md5_file($file_path));

    $md5_ctx = new MD5State();
    $md5_ctx->setState($state);
    $md5_ctx->update($append);

    $verification_hash = $md5_ctx->final();

    echo "<p><strong>Verification Hash: </strong>" . $verification_hash . "</p>";
    echo "<p><strong>Match: </strong>" . ($verification_hash === $new_hash ? "True" : "False") . "</p>";
?>
