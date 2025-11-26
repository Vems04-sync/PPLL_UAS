<?php
// File: functions/wa_api.php

function send_wa_otp($target, $otp)
{
    // --- Token Fonnte ---
    $token = "D6BcmLVP7rUgYZgFagvD";
    $url = "https://api.fonnte.com/send";

    // Data yang akan dikirim
    $data = array(
        'target' => $target,
        'message' => "Kode OTP Reset Password Anda: *$otp*\n\nJANGAN BERIKAN KODE INI KE SIAPAPUN."
    );

    // Membuat Header HTTP (Pengganti cURL Options)
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n" .
                "Authorization: $token\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data), // Mengubah array data menjadi format URL encoded
            'ignore_errors' => true // Agar tetap menangkap respon meski API error
        )
    );

    // Membuat Stream Context
    $context  = stream_context_create($options);

    // Eksekusi (Kirim Data)
    $result = file_get_contents($url, false, $context);

    // Cek hasil (Opsional untuk debugging)
    if ($result === FALSE) {
        // Jika gagal total (biasanya masalah koneksi internet)
        return "Error: Gagal mengirim request.";
    }

    return $result;
}
