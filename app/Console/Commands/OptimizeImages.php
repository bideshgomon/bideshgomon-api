<?php

namespace App\Console\Commands;

use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize 
                            {directory=images : The directory to optimize images in}
                            {--quality=85 : Quality level (1-100)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize and convert images to WebP format';

    /**
     * Execute the console command.
     */
    public function handle(ImageService $imageService)
    {
        $directory = $this->argument('directory');
        $quality = (int) $this->option('quality');

        $this->info("Starting image optimization in: {$directory}");

        $count = $imageService->batchOptimize($directory, $quality);

        $this->info("Optimized {$count} images successfully!");

        return Command::SUCCESS;
    }
}
