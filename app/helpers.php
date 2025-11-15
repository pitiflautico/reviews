<?php

if (!function_exists('skin')) {
    /**
     * Obtiene la ruta de una vista del skin actual
     *
     * @param string $path Ruta relativa de la vista
     * @return string Ruta completa con el skin
     *
     * @example skin('layouts.app') returns 'skins.listox.layouts.app'
     */
    function skin(string $path): string
    {
        $skin = config('app.skin', 'listox');
        return "skins.{$skin}.{$path}";
    }
}

if (!function_exists('skin_asset')) {
    /**
     * Obtiene la URL de un asset del skin actual
     *
     * @param string $path Ruta relativa del asset
     * @return string URL completa del asset
     *
     * @example skin_asset('css/style.css') returns '/assets/listox/css/style.css'
     */
    function skin_asset(string $path): string
    {
        $skin = config('app.skin', 'listox');
        return asset("assets/{$skin}/{$path}");
    }
}

if (!function_exists('icon')) {
    /**
     * Genera un tag de imagen para un icono del skin actual
     *
     * @param string $name Nombre del icono (sin extensión .svg)
     * @param string $alt Texto alternativo
     * @param array $attributes Atributos adicionales (class, size, etc.)
     * @return string HTML del icono
     *
     * @example icon('star', 'Rating', ['size' => 'w-6 h-6'])
     */
    function icon(string $name, string $alt = '', array $attributes = []): string
    {
        $class = $attributes['class'] ?? '';
        $size = $attributes['size'] ?? 'w-6 h-6';

        return sprintf(
            '<img src="%s" alt="%s" class="%s %s">',
            skin_asset("icons/ui/{$name}.svg"),
            $alt ?: $name,
            $size,
            $class
        );
    }
}
