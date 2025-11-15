<?php

namespace App\Services;

class RestaurantNormalizationService
{
    /**
     * Normaliza el nombre de un restaurante
     *
     * Proceso:
     * 1. Convierte a minúsculas
     * 2. Elimina acentos
     * 3. Elimina caracteres especiales (excepto espacios y guiones)
     * 4. Normaliza espacios múltiples a uno solo
     * 5. Trim
     */
    public function normalizeName(string $name): string
    {
        // 1. Minúsculas
        $normalized = mb_strtolower($name, 'UTF-8');

        // 2. Quitar acentos
        $normalized = $this->removeAccents($normalized);

        // 3. Quitar caracteres especiales (mantener solo letras, números, espacios y guiones)
        $normalized = preg_replace('/[^a-z0-9\s\-]/', '', $normalized);

        // 4. Normalizar espacios múltiples a uno solo
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        // 5. Trim
        return trim($normalized);
    }

    /**
     * Normaliza la dirección
     *
     * Proceso:
     * 1. Convierte a minúsculas
     * 2. Normaliza espacios
     * 3. Trim
     */
    public function normalizeAddress(string $address): string
    {
        // 1. Minúsculas
        $normalized = mb_strtolower($address, 'UTF-8');

        // 2. Normalizar espacios múltiples
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        // 3. Trim
        return trim($normalized);
    }

    /**
     * Elimina acentos y diéresis de un string
     *
     * Soporta caracteres en español
     */
    private function removeAccents(string $string): string
    {
        $transliteration = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ñ' => 'n', 'ü' => 'u', 'ç' => 'c',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
            'Ñ' => 'n', 'Ü' => 'u', 'Ç' => 'c',
            'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
            'À' => 'a', 'È' => 'e', 'Ì' => 'i', 'Ò' => 'o', 'Ù' => 'u',
        ];

        return strtr($string, $transliteration);
    }
}
