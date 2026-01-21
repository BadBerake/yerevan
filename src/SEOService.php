<?php

namespace App\Services;

/**
 * SEO Service - Manages meta tags, structured data, and SEO optimization
 */
class SEOService
{
    private $siteName = 'Yerevango';
    private $siteUrl = 'http://localhost:8000';
    private $defaultImage = '/assets/images/logo-icon.png';
    private $defaultDescription = 'Discover the best places, restaurants, cafes, and events in Yerevan, Armenia.';
    
    /**
     * Generate meta tags for a page
     */
    public function generateMetaTags(array $options = []): string
    {
        $title = $options['title'] ?? $this->siteName;
        $description = $options['description'] ?? $this->defaultDescription;
        $image = $options['image'] ?? $this->defaultImage;
        $url = $options['url'] ?? $_SERVER['REQUEST_URI'];
        $type = $options['type'] ?? 'website';
        
        // Full title with site name
        $fullTitle = $title === $this->siteName ? $title : "{$title} | {$this->siteName}";
        
        // Ensure absolute URL for image
        if (!str_starts_with($image, 'http')) {
            $image = $this->siteUrl . $image;
        }
        
        // Ensure absolute URL for canonical
        $canonicalUrl = $this->siteUrl . $url;
        
        $html = <<<HTML
    <!-- SEO Meta Tags -->
    <title>{$this->escape($fullTitle)}</title>
    <meta name="description" content="{$this->escape($description)}">
    <link rel="canonical" href="{$this->escape($canonicalUrl)}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{$this->escape($type)}">
    <meta property="og:url" content="{$this->escape($canonicalUrl)}">
    <meta property="og:title" content="{$this->escape($fullTitle)}">
    <meta property="og:description" content="{$this->escape($description)}">
    <meta property="og:image" content="{$this->escape($image)}">
    <meta property="og:site_name" content="{$this->siteName}">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{$this->escape($canonicalUrl)}">
    <meta name="twitter:title" content="{$this->escape($fullTitle)}">
    <meta name="twitter:description" content="{$this->escape($description)}">
    <meta name="twitter:image" content="{$this->escape($image)}">
HTML;
        
        return $html;
    }
    
    /**
     * Generate structured data (JSON-LD) for a place/restaurant
     */
    public function generatePlaceSchema(array $place): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $this->getSchemaType($place['category_name'] ?? 'Place'),
            'name' => $place['title'] ?? '',
            'description' => $place['description'] ?? '',
            'image' => $place['image_url'] ?? $this->defaultImage,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $place['address'] ?? '',
                'addressLocality' => 'Yerevan',
                'addressCountry' => 'AM'
            ],
        ];
        
        // Add coordinates if available
        if (!empty($place['latitude']) && !empty($place['longitude'])) {
            $schema['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => $place['latitude'],
                'longitude' => $place['longitude']
            ];
        }
        
        // Add rating if available
        if (!empty($place['rating_average']) && !empty($place['review_count'])) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $place['rating_average'],
                'reviewCount' => $place['review_count'],
                'bestRating' => 5,
                'worstRating' => 1
            ];
        }
        
        // Add phone if available
        if (!empty($place['phone'])) {
            $schema['telephone'] = $place['phone'];
        }
        
        // Add website if available
        if (!empty($place['website'])) {
            $schema['url'] = $place['website'];
        }
        
        return $this->wrapJsonLd($schema);
    }
    
    /**
     * Generate structured data for an event
     */
    public function generateEventSchema(array $event): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $event['title'] ?? '',
            'description' => $event['description'] ?? '',
            'image' => $event['image_url'] ?? $this->defaultImage,
            'startDate' => $event['start_date'] ?? '',
            'endDate' => $event['end_date'] ?? '',
            'eventStatus' => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'location' => [
                '@type' => 'Place',
                'name' => $event['location_name'] ?? 'Yerevan',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $event['address'] ?? '',
                    'addressLocality' => 'Yerevan',
                    'addressCountry' => 'AM'
                ]
            ],
            'organizer' => [
                '@type' => 'Organization',
                'name' => $this->siteName,
                'url' => $this->siteUrl
            ]
        ];
        
        if (!empty($event['price'])) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => $event['price'],
                'priceCurrency' => 'AMD',
                'availability' => 'https://schema.org/InStock',
                'url' => $this->siteUrl . '/event/' . ($event['id'] ?? '')
            ];
        }
        
        return $this->wrapJsonLd($schema);
    }
    
    /**
     * Generate breadcrumb schema
     */
    public function generateBreadcrumbSchema(array $breadcrumbs): string
    {
        $itemListElement = [];
        
        foreach ($breadcrumbs as $index => $crumb) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['name'],
                'item' => $this->siteUrl . $crumb['url']
            ];
        }
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement
        ];
        
        return $this->wrapJsonLd($schema);
    }
    
    /**
     * Generate review schema
     */
    public function generateReviewSchema(array $review, array $place): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'itemReviewed' => [
                '@type' => $this->getSchemaType($place['category_name'] ?? 'Place'),
                'name' => $place['title'] ?? ''
            ],
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $review['rating'] ?? 0,
                'bestRating' => 5,
                'worstRating' => 1
            ],
            'author' => [
                '@type' => 'Person',
                'name' => $review['user_name'] ?? 'Anonymous'
            ],
            'reviewBody' => $review['content'] ?? '',
            'datePublished' => $review['created_at'] ?? date('Y-m-d')
        ];
        
        return $this->wrapJsonLd($schema);
    }
    
    /**
     * Map category to schema.org type
     */
    private function getSchemaType(string $category): string
    {
        $mapping = [
            'Restaurant' => 'Restaurant',
            'Cafe' => 'CafeOrCoffeeShop',
            'Hotel' => 'Hotel',
            'Museum' => 'Museum',
            'Park' => 'Park',
            'Bar' => 'BarOrPub',
        ];
        
        return $mapping[$category] ?? 'LocalBusiness';
    }
    
    /**
     * Wrap schema array in JSON-LD script tag
     */
    private function wrapJsonLd(array $schema): string
    {
        $json = json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return "<script type=\"application/ld+json\">\n{$json}\n</script>";
    }
    
    /**
     * Escape HTML entities
     */
    private function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Generate XML sitemap
     */
    public function generateSitemap(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . $this->escape($this->siteUrl . $url['loc']) . "</loc>\n";
            
            if (!empty($url['lastmod'])) {
                $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            }
            
            $xml .= "    <changefreq>" . ($url['changefreq'] ?? 'weekly') . "</changefreq>\n";
            $xml .= "    <priority>" . ($url['priority'] ?? '0.5') . "</priority>\n";
            $xml .= "  </url>\n";
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
}
