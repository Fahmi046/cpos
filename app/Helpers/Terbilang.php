<?php

namespace App\Helpers;

class Terbilang
{
    private static $angka = [
        '',
        'Satu',
        'Dua',
        'Tiga',
        'Empat',
        'Lima',
        'Enam',
        'Tujuh',
        'Delapan',
        'Sembilan',
        'Sepuluh',
        'Sebelas'
    ];

    public static function angkaTerbilang($x)
    {
        $x = (int) $x;
        if ($x < 12) {
            return self::$angka[$x];
        } elseif ($x < 20) {
            return self::angkaTerbilang($x - 10) . ' Belas';
        } elseif ($x < 100) {
            return self::angkaTerbilang(intval($x / 10)) . ' Puluh' . ($x % 10 > 0 ? ' ' . self::angkaTerbilang($x % 10) : '');
        } elseif ($x < 200) {
            return 'Seratus' . ($x - 100 > 0 ? ' ' . self::angkaTerbilang($x - 100) : '');
        } elseif ($x < 1000) {
            return self::angkaTerbilang(intval($x / 100)) . ' Ratus' . ($x % 100 > 0 ? ' ' . self::angkaTerbilang($x % 100) : '');
        } elseif ($x < 2000) {
            return 'Seribu' . ($x - 1000 > 0 ? ' ' . self::angkaTerbilang($x - 1000) : '');
        } elseif ($x < 1000000) {
            return self::angkaTerbilang(intval($x / 1000)) . ' Ribu' . ($x % 1000 > 0 ? ' ' . self::angkaTerbilang($x % 1000) : '');
        } elseif ($x < 1000000000) {
            return self::angkaTerbilang(intval($x / 1000000)) . ' Juta' . ($x % 1000000 > 0 ? ' ' . self::angkaTerbilang($x % 1000000) : '');
        } else {
            return $x;
        }
    }
}
