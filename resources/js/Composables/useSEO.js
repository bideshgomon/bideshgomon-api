import { usePage } from '@inertiajs/vue3'

export function useSEO() {
    const page = usePage()

    const generateSchema = {
        organization() {
            return {
                '@context': 'https://schema.org',
                '@type': 'Organization',
                'name': 'Bidesh Gomon',
                'alternateName': 'বিদেশ গমন',
                'url': window.location.origin,
                'logo': `${window.location.origin}/images/logo.png`,
                'description': 'Professional visa application and immigration services for Bangladeshi citizens.',
                'address': {
                    '@type': 'PostalAddress',
                    'addressCountry': 'BD',
                    'addressLocality': 'Dhaka',
                    'addressRegion': 'Dhaka Division'
                },
                'contactPoint': {
                    '@type': 'ContactPoint',
                    'telephone': '+880-1910-638075',
                    'contactType': 'Customer Service',
                    'areaServed': 'BD',
                    'availableLanguage': ['Bengali', 'English']
                },
                'sameAs': [
                    'https://facebook.com/bideshgomon',
                    'https://twitter.com/bideshgomon',
                    'https://linkedin.com/company/bideshgomon'
                ]
            }
        },

        service(service) {
            return {
                '@context': 'https://schema.org',
                '@type': 'Service',
                'name': service.name,
                'description': service.description,
                'provider': {
                    '@type': 'Organization',
                    'name': 'Bidesh Gomon',
                    'url': window.location.origin
                },
                'serviceType': service.service_type || 'Visa Application Service',
                'areaServed': {
                    '@type': 'Country',
                    'name': 'Bangladesh'
                },
                'offers': {
                    '@type': 'Offer',
                    'price': service.base_price || '0',
                    'priceCurrency': 'BDT',
                    'availability': 'https://schema.org/InStock'
                }
            }
        },

        faq(faqs) {
            const questions = faqs.map(faq => ({
                '@type': 'Question',
                'name': faq.question,
                'acceptedAnswer': {
                    '@type': 'Answer',
                    'text': faq.answer
                }
            }))

            return {
                '@context': 'https://schema.org',
                '@type': 'FAQPage',
                'mainEntity': questions
            }
        },

        breadcrumb(items) {
            const listItems = items.map((item, index) => ({
                '@type': 'ListItem',
                'position': index + 1,
                'name': item.title,
                'item': item.url
            }))

            return {
                '@context': 'https://schema.org',
                '@type': 'BreadcrumbList',
                'itemListElement': listItems
            }
        },

        article(post) {
            return {
                '@context': 'https://schema.org',
                '@type': 'Article',
                'headline': post.title,
                'description': post.excerpt || post.description,
                'image': post.featured_image || `${window.location.origin}/images/default-article.jpg`,
                'datePublished': post.published_at || post.created_at,
                'dateModified': post.updated_at,
                'author': {
                    '@type': 'Person',
                    'name': post.author?.name || 'Bidesh Gomon Team'
                },
                'publisher': {
                    '@type': 'Organization',
                    'name': 'Bidesh Gomon',
                    'logo': {
                        '@type': 'ImageObject',
                        'url': `${window.location.origin}/images/logo.png`
                    }
                }
            }
        },

        localBusiness() {
            return {
                '@context': 'https://schema.org',
                '@type': 'LocalBusiness',
                'name': 'BideshGomon',
                'image': `${window.location.origin}/images/office.jpg`,
                'url': window.location.origin,
                'telephone': '+880-1910-638075',
                'priceRange': '৳৳',
                'address': {
                    '@type': 'PostalAddress',
                    'streetAddress': 'Your Address',
                    'addressLocality': 'Dhaka',
                    'addressRegion': 'Dhaka Division',
                    'postalCode': '1000',
                    'addressCountry': 'BD'
                },
                'geo': {
                    '@type': 'GeoCoordinates',
                    'latitude': 23.8103,
                    'longitude': 90.4125
                },
                'openingHoursSpecification': [
                    {
                        '@type': 'OpeningHoursSpecification',
                        'dayOfWeek': ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                        'opens': '09:00',
                        'closes': '18:00'
                    }
                ]
            }
        }
    }

    const getMetaTags = (options = {}) => {
        const defaults = {
            title: 'BideshGomon - Your Trusted Bangladesh Visa & Immigration Partner',
            description: 'Professional visa application services for Bangladeshi citizens. Student visas, work permits, tourism visas, and immigration assistance for USA, UK, Canada, Australia, and 50+ countries.',
            keywords: 'visa application Bangladesh, immigration services Bangladesh, student visa, work permit, tourism visa, BideshGomon, visa consultant Dhaka',
            image: '/images/og-image.jpg',
            type: 'website'
        }

        return { ...defaults, ...options }
    }

    const getCanonicalUrl = () => {
        return window.location.href.split('?')[0]
    }

    const getFullImageUrl = (image) => {
        if (!image) return `${window.location.origin}/images/og-image.jpg`
        return image.startsWith('http') ? image : `${window.location.origin}${image}`
    }

    return {
        generateSchema,
        getMetaTags,
        getCanonicalUrl,
        getFullImageUrl
    }
}
