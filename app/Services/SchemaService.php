<?php

namespace App\Services;

class SchemaService
{
    /**
     * Generate Organization schema
     */
    public function organization(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'BideshGomon',
            'alternateName' => 'বিদেশ গমন',
            'url' => config('app.url'),
            'logo' => config('app.url') . '/images/logo.png',
            'description' => 'Professional visa application and immigration services for Bangladeshi citizens.',
            'email' => 'info@bideshgomon.com',
            'telephone' => '+880-1234-567890',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Your Address',
                'addressLocality' => 'Dhaka',
                'addressRegion' => 'Dhaka Division',
                'postalCode' => '1000',
                'addressCountry' => 'BD'
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+880-1234-567890',
                'contactType' => 'Customer Service',
                'areaServed' => 'BD',
                'availableLanguage' => ['Bengali', 'English']
            ],
            'sameAs' => [
                'https://facebook.com/bideshgomon',
                'https://twitter.com/bideshgomon',
                'https://linkedin.com/company/bideshgomon'
            ]
        ];
    }

    /**
     * Generate Service schema
     */
    public function service($service): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service->name,
            'description' => $service->description,
            'provider' => [
                '@type' => 'Organization',
                'name' => 'BideshGomon',
                'url' => config('app.url')
            ],
            'serviceType' => $service->service_type ?? 'Visa Application Service',
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'Bangladesh'
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => $service->base_price ?? '0',
                'priceCurrency' => 'BDT',
                'availability' => 'https://schema.org/InStock',
                'url' => route('services.show', $service->slug)
            ]
        ];
    }

    /**
     * Generate FAQPage schema
     */
    public function faqPage(array $faqs): array
    {
        $questions = [];
        
        foreach ($faqs as $faq) {
            $questions[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                ]
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $questions
        ];
    }

    /**
     * Generate JobPosting schema
     */
    public function jobPosting($job): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $job->title,
            'description' => $job->description,
            'datePosted' => $job->created_at->toIso8601String(),
            'validThrough' => $job->expires_at?->toIso8601String(),
            'employmentType' => $job->employment_type ?? 'FULL_TIME',
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => 'BideshGomon',
                'sameAs' => config('app.url')
            ],
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $job->location ?? 'Dhaka',
                    'addressCountry' => 'BD'
                ]
            ],
            'baseSalary' => [
                '@type' => 'MonetaryAmount',
                'currency' => 'BDT',
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => $job->min_salary ?? 0,
                    'maxValue' => $job->max_salary ?? 0,
                    'unitText' => 'MONTH'
                ]
            ]
        ];
    }

    /**
     * Generate Article/BlogPosting schema
     */
    public function article($post): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => $post->excerpt ?? substr(strip_tags($post->content), 0, 160),
            'image' => $post->featured_image ?? config('app.url') . '/images/default-article.jpg',
            'datePublished' => $post->published_at?->toIso8601String() ?? $post->created_at->toIso8601String(),
            'dateModified' => $post->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $post->author->name ?? 'BideshGomon Team'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'BideshGomon',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => config('app.url') . '/images/logo.png'
                ]
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('blog.show', $post->slug)
            ]
        ];
    }

    /**
     * Generate BreadcrumbList schema
     */
    public function breadcrumbList(array $breadcrumbs): array
    {
        $items = [];
        
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['title'],
                'item' => $breadcrumb['url']
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        ];
    }

    /**
     * Generate AggregateRating schema
     */
    public function aggregateRating($itemName, $ratingValue, $reviewCount, $bestRating = 5): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $itemName,
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => $ratingValue,
                'reviewCount' => $reviewCount,
                'bestRating' => $bestRating,
                'worstRating' => 1
            ]
        ];
    }

    /**
     * Generate LocalBusiness schema
     */
    public function localBusiness(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => 'BideshGomon',
            'image' => config('app.url') . '/images/office.jpg',
            '@id' => config('app.url'),
            'url' => config('app.url'),
            'telephone' => '+880-1234-567890',
            'priceRange' => '৳৳',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Your Address',
                'addressLocality' => 'Dhaka',
                'addressRegion' => 'Dhaka Division',
                'postalCode' => '1000',
                'addressCountry' => 'BD'
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 23.8103,
                'longitude' => 90.4125
            ],
            'openingHoursSpecification' => [
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                    'opens' => '09:00',
                    'closes' => '18:00'
                ]
            ]
        ];
    }
}
