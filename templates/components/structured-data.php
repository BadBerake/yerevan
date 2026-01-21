<?php
/**
 * Component: Structured Data (JSON-LD)
 * This component generates JSON-LD schema based on the context (Place, Event, Route, etc.)
 */

if (!isset($item)) return;

$schema = [];

// Base Context
$schema['@context'] = 'https://schema.org';

// Determine Type
if (isset($item['event_date'])) {
    // Event Schema
    $schema['@type'] = 'Event';
    $schema['name'] = htmlspecialchars($item['title']);
    $schema['description'] = htmlspecialchars($item['description']);
    $schema['startDate'] = date('c', strtotime($item['event_date']));
    $schema['location'] = [
        '@type' => 'Place',
        'name' => htmlspecialchars($item['title']),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => htmlspecialchars($item['address'] ?? ''),
            'addressLocality' => 'Yerevan',
            'addressCountry' => 'AM'
        ]
    ];
    $schema['image'] = $item['image_url'];
    
    if (isset($item['ticket_price']) && $item['ticket_price'] > 0) {
        $schema['offers'] = [
            '@type' => 'Offer',
            'price' => $item['ticket_price'],
            'priceCurrency' => 'AMD',
            'availability' => 'https://schema.org/InStock',
            'url' => 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
        ];
    }
} else {
    // LocalBusiness / Restaurant / Cafe Schema
    $type = 'LocalBusiness';
    if (isset($item['category_name'])) {
        if ($item['category_name'] == 'Restaurants') $type = 'Restaurant';
        if ($item['category_name'] == 'Cafes') $type = 'Cafe';
    }
    
    $schema['@type'] = $type;
    $schema['name'] = htmlspecialchars($item['title']);
    $schema['description'] = htmlspecialchars($item['description']);
    $schema['image'] = $item['image_url'];
    $schema['address'] = [
        '@type' => 'PostalAddress',
        'streetAddress' => htmlspecialchars($item['address'] ?? ''),
        'addressLocality' => 'Yerevan',
        'addressCountry' => 'AM'
    ];
    
    if (isset($item['phone'])) {
        $schema['telephone'] = $item['phone'];
    }
    
    // Add Reviews if available
    if (isset($reviews) && !empty($reviews)) {
        $schema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => '4.8', // Placeholder or calculate if possible
            'reviewCount' => count($reviews)
        ];
        
        $schema['review'] = [];
        foreach (array_slice($reviews, 0, 3) as $r) {
            $schema['review'][] = [
                '@type' => 'Review',
                'reviewRating' => [
                    '@type' => 'Rating',
                    'ratingValue' => $r['rating']
                ],
                'author' => [
                    '@type' => 'Person',
                    'name' => htmlspecialchars($r['username'] ?? 'User')
                ],
                'reviewBody' => htmlspecialchars($r['content'])
            ];
        }
    }
}

echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
?>
