<?php

namespace App\Services;

/**
 * Image Service - Handles image optimization, lazy loading, and WebP conversion
 */
class ImageService
{
    private $uploadsPath;
    private $publicPath;
    
    public function __construct()
    {
        $this->uploadsPath = __DIR__ . '/../public/uploads';
        $this->publicPath = '/uploads';
    }
    
    /**
     * Generate optimized image tag with lazy loading and WebP support
     */
    public function getOptimizedImage(string $src, string $alt = '', array $options = []): string
    {
        if (empty($src)) {
            $src = '/public/img/placeholder.jpg';
        }
        
        $width = $options['width'] ?? null;
        $height = $options['height'] ?? null;
        $class = $options['class'] ?? '';
        $loading = $options['loading'] ?? 'lazy';
        $sizes = $options['sizes'] ?? '';
        
        // Generate WebP version path
        $webpSrc = $this->getWebPPath($src);
        
        // Build attributes
        $attrs = [];
        if ($width) $attrs[] = "width=\"{$width}\"";
        if ($height) $attrs[] = "height=\"{$height}\"";
        if ($class) $attrs[] = "class=\"{$class}\"";
        if ($loading) $attrs[] = "loading=\"{$loading}\"";
        if ($sizes) $attrs[] = "sizes=\"{$sizes}\"";
        
        $attrString = implode(' ', $attrs);
        
        // Use picture element with WebP and fallback
        if ($webpSrc && file_exists($this->uploadsPath . $webpSrc)) {
            return <<<HTML
<picture>
    <source srcset="{$webpSrc}" type="image/webp">
    <img src="{$src}" alt="{$this->escape($alt)}" {$attrString}>
</picture>
HTML;
        }
        
        // Fallback to regular img tag
        return "<img src=\"{$src}\" alt=\"{$this->escape($alt)}\" {$attrString}>";
    }
    
    /**
     * Convert image path to WebP version
     */
    private function getWebPPath(string $src): ?string
    {
        $pathInfo = pathinfo($src);
        $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
        
        return $webpPath;
    }
    
    /**
     * Convert uploaded image to WebP format
     */
    public function convertToWebP(string $sourcePath): ?string
    {
        if (!file_exists($sourcePath)) {
            return null;
        }
        
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return null;
        }
        
        $mimeType = $imageInfo['mime'];
        
        // Create image resource based on type
        switch ($mimeType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            default:
                return null;
        }
        
        if (!$image) {
            return null;
        }
        
        // Generate WebP filename
        $pathInfo = pathinfo($sourcePath);
        $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
        
        // Convert to WebP with 80% quality
        imagewebp($image, $webpPath, 80);
        imagedestroy($image);
        
        return $webpPath;
    }
    
    /**
     * Optimize image by resizing and compressing
     */
    public function optimizeImage(string $sourcePath, int $maxWidth = 1920, int $quality = 85): bool
    {
        if (!file_exists($sourcePath)) {
            return false;
        }
        
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }
        
        [$width, $height] = $imageInfo;
        $mimeType = $imageInfo['mime'];
        
        // Skip if already smaller than max width
        if ($width <= $maxWidth) {
            return true;
        }
        
        // Calculate new dimensions
        $newWidth = $maxWidth;
        $newHeight = ($height / $width) * $newWidth;
        
        // Create image resource
        switch ($mimeType) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($sourcePath);
                break;
            default:
                return false;
        }
        
        if (!$source) {
            return false;
        }
        
        // Create resized image
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }
        
        // Resize
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save optimized image
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($resized, $sourcePath, $quality);
                break;
            case 'image/png':
                imagepng($resized, $sourcePath, 9);
                break;
            case 'image/gif':
                imagegif($resized, $sourcePath);
                break;
        }
        
        imagedestroy($source);
        imagedestroy($resized);
        
        return true;
    }
    
    /**
     * Generate responsive srcset attribute
     */
    public function generateSrcSet(string $src, array $sizes = [320, 640, 960, 1280, 1920]): string
    {
        $srcset = [];
        
        foreach ($sizes as $size) {
            $resizedPath = $this->getResizedPath($src, $size);
            if ($resizedPath) {
                $srcset[] = "{$resizedPath} {$size}w";
            }
        }
        
        return implode(', ', $srcset);
    }
    
    /**
     * Get path for resized version of image
     */
    private function getResizedPath(string $src, int $width): ?string
    {
        $pathInfo = pathinfo($src);
        return $pathInfo['dirname'] . '/' . $pathInfo['filename'] . "-{$width}w." . $pathInfo['extension'];
    }
    
    /**
     * Escape HTML
     */
    private function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
