<?php
// app/Services/DeviceDetectionService.php
namespace App\Services;

class DeviceDetectionService
{
    public static function detectDevice($userAgent)
    {
        $userAgent = strtolower($userAgent);
        
        // Mobile devices
        $mobileKeywords = [
            'mobile', 'android', 'iphone', 'ipod', 'blackberry', 
            'windows phone', 'nokia', 'samsung', 'htc', 'lg',
            'motorola', 'sony', 'xiaomi', 'huawei', 'oppo', 'vivo'
        ];
        
        // Tablet devices
        $tabletKeywords = [
            'ipad', 'tablet', 'kindle', 'nexus 7', 'nexus 9', 
            'nexus 10', 'surface', 'galaxy tab'
        ];
        
        // Check for tablet first (more specific)
        foreach ($tabletKeywords as $keyword) {
            if (strpos($userAgent, $keyword) !== false) {
                return 'tablet';
            }
        }
        
        // Check for mobile
        foreach ($mobileKeywords as $keyword) {
            if (strpos($userAgent, $keyword) !== false) {
                return 'mobile';
            }
        }
        
        // Default to desktop
        return 'desktop';
    }
    
    public static function detectBrowser($userAgent)
    {
        $userAgent = strtolower($userAgent);
        
        if (strpos($userAgent, 'chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'edge') !== false) {
            return 'Edge';
        } elseif (strpos($userAgent, 'opera') !== false) {
            return 'Opera';
        } elseif (strpos($userAgent, 'msie') !== false || strpos($userAgent, 'trident') !== false) {
            return 'Internet Explorer';
        }
        
        return 'Other';
    }
    
    public static function detectOS($userAgent)
    {
        $userAgent = strtolower($userAgent);
        
        if (strpos($userAgent, 'windows') !== false) {
            return 'Windows';
        } elseif (strpos($userAgent, 'macintosh') !== false || strpos($userAgent, 'mac os') !== false) {
            return 'macOS';
        } elseif (strpos($userAgent, 'linux') !== false) {
            return 'Linux';
        } elseif (strpos($userAgent, 'android') !== false) {
            return 'Android';
        } elseif (strpos($userAgent, 'iphone') !== false || strpos($userAgent, 'ipad') !== false) {
            return 'iOS';
        }
        
        return 'Other';
    }
}