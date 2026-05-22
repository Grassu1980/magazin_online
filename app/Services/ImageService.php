<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * ImageService - Serviciu pentru procesarea automată a imaginilor
 * 
 * Acest serviciu gestionează:
 * - Redimensionarea imaginilor la dimensiuni setate
 * - Păstrarea proporțiilor cu padding
 * - Optimizarea și compresia imaginilor
 * - Conversia în WebP cu fallback JPG
 * - Generarea automată de thumbnail-uri
 */
class ImageService
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Folosim funcții native GD pentru procesarea imaginilor
    }

    /**
     * Procesează o imagine încărcată
     * 
     * @param \Illuminate\Http\UploadedFile $file Fișierul imagine
     * @param string $directory Directorul de salvare (ex: 'products')
     * @param bool $generateThumbnail Generează thumbnail automat
     * @return array Numele fișierelor procesate (main, thumbnail)
     */
    public function processImage($file, $directory = 'products', $generateThumbnail = true)
    {
        // Obține setările de procesare
        $settings = getImageSettings();
        
        // Validează fișierul
        $this->validateImageFile($file);
        
        // Citește imaginea folosind GD
        $imageInfo = $this->readImage($file->getRealPath());
        
        // Procesează imaginea principală
        $mainImage = $this->processMainImage($imageInfo, $settings);
        
        // Salvează imaginea principală
        $mainFilename = $this->saveImage($mainImage, $directory, $settings['format'], $settings['quality']);
        
        // Generează thumbnail dacă este cerut
        $thumbnailFilename = null;
        if ($generateThumbnail) {
            $thumbnail = $this->processThumbnail($imageInfo, 300, $settings);
            $thumbnailFilename = $this->saveImage($thumbnail, $directory . '/thumbnails', $settings['format'], $settings['quality']);
        }
        
        // Eliberează memoria
        imagedestroy($imageInfo['resource']);
        
        return [
            'main' => $mainFilename,
            'thumbnail' => $thumbnailFilename,
        ];
    }

    /**
     * Citește o imagine folosind GD
     * 
     * @param string $path Calea către fișier
     * @return array Informații despre imagine (resource, width, height, type)
     */
    private function readImage($path)
    {
        $imageInfo = getimagesize($path);
        if (!$imageInfo) {
            throw new \Exception('Nu s-a putut citi imaginea.');
        }
        
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];
        
        // Creează resursa imagine în funcție de tip
        switch ($type) {
            case IMAGETYPE_JPEG:
                $resource = imagecreatefromjpeg($path);
                break;
            case IMAGETYPE_PNG:
                $resource = imagecreatefrompng($path);
                break;
            case IMAGETYPE_WEBP:
                $resource = imagecreatefromwebp($path);
                break;
            default:
                throw new \Exception('Formatul imaginii nu este suportat.');
        }
        
        if (!$resource) {
            throw new \Exception('Nu s-a putut crea resursa imagine.');
        }
        
        return [
            'resource' => $resource,
            'width' => $width,
            'height' => $height,
            'type' => $type,
        ];
    }

    /**
     * Procesează imaginea principală
     * 
     * @param array $imageInfo Informații despre imagine
     * @param array $settings Setările de procesare
     * @return resource Imaginea procesată
     */
    private function processMainImage($imageInfo, $settings)
    {
        $maxSize = $settings['max_size'];
        $backgroundColor = $this->hexToRgb($settings['background_color']);
        
        $width = $imageInfo['width'];
        $height = $imageInfo['height'];
        
        // Calculează dimensiunile pătrate
        $newSize = min($width, $height, $maxSize);
        
        // Calculează noile dimensiuni păstrând proporțiile
        if ($width > $height) {
            $newWidth = $newSize;
            $newHeight = (int) ($height * ($newSize / $width));
        } else {
            $newHeight = $newSize;
            $newWidth = (int) ($width * ($newSize / $height));
        }
        
        // Creează imagine redimensionată
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $imageInfo['resource'], 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Creează canvas pătrat cu background color
        $canvas = imagecreatetruecolor($maxSize, $maxSize);
        $bgColor = imagecolorallocate($canvas, $backgroundColor['r'], $backgroundColor['g'], $backgroundColor['b']);
        imagefill($canvas, 0, 0, $bgColor);
        
        // Centrează imaginea pe canvas
        $x = (int) (($maxSize - $newWidth) / 2);
        $y = (int) (($maxSize - $newHeight) / 2);
        
        imagealphablending($canvas, true);
        imagecopy($canvas, $resized, $x, $y, 0, 0, $newWidth, $newHeight);
        
        // Eliberează memoria
        imagedestroy($resized);
        
        return $canvas;
    }

    /**
     * Procesează thumbnail-ul
     * 
     * @param array $imageInfo Informații despre imagine
     * @param int $size Dimensiunea thumbnail-ului
     * @param array $settings Setările de procesare
     * @return resource Thumbnail-ul procesat
     */
    private function processThumbnail($imageInfo, $size, $settings)
    {
        $backgroundColor = $this->hexToRgb($settings['background_color']);
        
        $width = $imageInfo['width'];
        $height = $imageInfo['height'];
        
        // Calculează noile dimensiuni păstrând proporțiile
        if ($width > $height) {
            $newWidth = $size;
            $newHeight = (int) ($height * ($size / $width));
        } else {
            $newHeight = $size;
            $newWidth = (int) ($width * ($size / $height));
        }
        
        // Creează imagine redimensionată
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $imageInfo['resource'], 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Creează canvas pătrat
        $canvas = imagecreatetruecolor($size, $size);
        $bgColor = imagecolorallocate($canvas, $backgroundColor['r'], $backgroundColor['g'], $backgroundColor['b']);
        imagefill($canvas, 0, 0, $bgColor);
        
        // Centrează imaginea pe canvas
        $x = (int) (($size - $newWidth) / 2);
        $y = (int) (($size - $newHeight) / 2);
        
        imagealphablending($canvas, true);
        imagecopy($canvas, $resized, $x, $y, 0, 0, $newWidth, $newHeight);
        
        // Eliberează memoria
        imagedestroy($resized);
        
        return $canvas;
    }

    /**
     * Salvează imaginea procesată
     * 
     * @param resource $image Resursa imagine
     * @param string $directory Directorul de salvare
     * @param string $format Formatul imaginii
     * @param int $quality Calitatea compresiei
     * @return string Numele fișierului salvat
     */
    private function saveImage($image, $directory, $format, $quality)
    {
        // Generează nume unic
        $filename = Str::random(40) . '.' . $format;
        $path = $directory . '/' . $filename;
        
        // Verifică dacă formatul este suportat
        $actualFormat = $this->getSupportedFormat($format);
        
        // Salvează imaginea în public/uploads pentru acces direct
        $fullPath = public_path('uploads/' . $path);
        
        // Asigură că directorul există
        $directoryPath = dirname($fullPath);
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }
        
        // Salvează cu formatul și calitatea setată
        if ($actualFormat === 'webp') {
            imagewebp($image, $fullPath, $quality);
        } else {
            imagejpeg($image, $fullPath, $quality);
        }
        
        // Verifică dimensiunea fișierului și optimizează dacă este necesar
        $maxSizeKB = setting('image_max_file_size', 2048); // Default 2 MB
        $currentSizeKB = filesize($fullPath) / 1024;
        
        if ($currentSizeKB > $maxSizeKB) {
            $this->optimizeImageSize($image, $fullPath, $actualFormat, $quality, $maxSizeKB);
        }
        
        // Eliberează memoria
        imagedestroy($image);
        
        // Returnează calea relativă pentru stocare în baza de date
        return 'uploads/' . $path;
    }
    
    /**
     * Optimizează dimensiunea imaginii pentru a se încadra în limita specificată
     * 
     * @param resource $image Resursa imagine
     * @param string $fullPath Calea completă către fișier
     * @param string $format Formatul imaginii
     * @param int $initialQuality Calitatea inițială
     * @param int $maxSizeKB Dimensiunea maximă în KB
     */
    private function optimizeImageSize($image, $fullPath, $format, $initialQuality, $maxSizeKB)
    {
        $quality = $initialQuality;
        $minQuality = 50; // Calitatea minimă acceptabilă
        
        // Încearcă să reducă calitatea progresiv
        while ($quality > $minQuality) {
            $quality -= 5;
            
            if ($format === 'webp') {
                imagewebp($image, $fullPath, $quality);
            } else {
                imagejpeg($image, $fullPath, $quality);
            }
            
            $currentSizeKB = filesize($fullPath) / 1024;
            if ($currentSizeKB <= $maxSizeKB) {
                break;
            }
        }
        
        // Dacă încă este prea mare, încearcă să redimensioneze imaginea
        if (filesize($fullPath) / 1024 > $maxSizeKB) {
            $this->resizeForFileSize($image, $fullPath, $format, $maxSizeKB);
        }
    }
    
    /**
     * Redimensionează imaginea pentru a reduce dimensiunea fișierului
     * 
     * @param resource $image Resursa imagine
     * @param string $fullPath Calea completă către fișier
     * @param string $format Formatul imaginii
     * @param int $maxSizeKB Dimensiunea maximă în KB
     */
    private function resizeForFileSize($image, $fullPath, $format, $maxSizeKB)
    {
        $currentWidth = imagesx($image);
        $currentHeight = imagesy($image);
        $scale = 0.9; // Reducere cu 10%
        
        while (filesize($fullPath) / 1024 > $maxSizeKB && $scale > 0.5) {
            $newWidth = (int) ($currentWidth * $scale);
            $newHeight = (int) ($currentHeight * $scale);
            
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $currentWidth, $currentHeight);
            
            if ($format === 'webp') {
                imagewebp($resized, $fullPath, 75);
            } else {
                imagejpeg($resized, $fullPath, 75);
            }
            
            imagedestroy($resized);
            $scale -= 0.05;
        }
    }

    /**
     * Verifică dacă formatul este suportat și returnează formatul efectiv
     * 
     * @param string $format Formatul dorit
     * @return string Formatul suportat
     */
    private function getSupportedFormat($format)
    {
        // Verifică dacă WebP este suportat
        if ($format === 'webp' && function_exists('imagewebp')) {
            return 'webp';
        }
        
        // Fallback la JPG
        return 'jpg';
    }

    /**
     * Convertește culoarea HEX în RGB
     * 
     * @param string $hex Culoarea în format HEX
     * @return array Culoarea în format RGB
     */
    private function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) === 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        
        return ['r' => $r, 'g' => $g, 'b' => $b];
    }

    /**
     * Validează fișierul imagine
     * 
     * @param \Illuminate\Http\UploadedFile $file Fișierul de validat
     * @throws \Exception Dacă fișierul nu este valid
     */
    private function validateImageFile($file)
    {
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('Formatul fișierului nu este acceptat. Formate acceptate: JPG, PNG, WebP.');
        }
        
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($file->getSize() > $maxSize) {
            throw new \Exception('Fișierul este prea mare. Dimensiune maximă: 10MB.');
        }
    }

    /**
     * Șterge o imagine și thumbnail-ul său
     * 
     * @param string $imagePath Calea imaginii
     * @return bool
     */
    public function deleteImage($imagePath)
    {
        $deleted = false;
        
        // Șterge imaginea principală din public/uploads
        if ($imagePath && file_exists(public_path($imagePath))) {
            unlink(public_path($imagePath));
            $deleted = true;
        }
        
        // Șterge thumbnail-ul
        $thumbnailPath = str_replace('uploads/products/', 'uploads/products/thumbnails/', $imagePath);
        if (file_exists(public_path($thumbnailPath))) {
            unlink(public_path($thumbnailPath));
            $deleted = true;
        }
        
        return $deleted;
    }
}
