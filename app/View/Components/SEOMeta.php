<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class SEOMeta extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $keywords = null,
        public ?string $image = null,
        public ?string $url = null,
        public ?string $type = 'website',
        public ?string $canonical = null,
        public string $robots = 'index, follow',
        public ?array $schema = null
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.seo-meta');
    }
}
