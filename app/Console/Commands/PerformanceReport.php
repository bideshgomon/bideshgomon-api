<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PerformanceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:report 
                          {--hours=24 : Number of hours to analyze}
                          {--limit=10 : Number of results to show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a performance report from Telescope data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $limit = $this->option('limit');
        
        $this->info("Performance Report - Last {$hours} hours");
        $this->newLine();

        // Slow Queries
        $this->info('🐌 Slowest Database Queries:');
        $slowQueries = DB::table('telescope_entries')
            ->where('type', 'query')
            ->where('created_at', '>=', now()->subHours($hours))
            ->orderByDesc(DB::raw("CAST(JSON_EXTRACT(content, '$.duration') AS DECIMAL(10,2))"))
            ->limit($limit)
            ->get();

        if ($slowQueries->isEmpty()) {
            $this->line('  No queries recorded');
        } else {
            foreach ($slowQueries as $query) {
                $content = json_decode($query->content, true);
                $duration = $content['duration'] ?? 0;
                $sql = substr($content['sql'] ?? '', 0, 80);
                $this->line("  {$duration}ms - {$sql}...");
            }
        }
        
        $this->newLine();

        // Slow Requests
        $this->info('🐢 Slowest HTTP Requests:');
        $slowRequests = DB::table('telescope_entries')
            ->where('type', 'request')
            ->where('created_at', '>=', now()->subHours($hours))
            ->orderByDesc(DB::raw("CAST(JSON_EXTRACT(content, '$.duration') AS DECIMAL(10,2))"))
            ->limit($limit)
            ->get();

        if ($slowRequests->isEmpty()) {
            $this->line('  No requests recorded');
        } else {
            foreach ($slowRequests as $request) {
                $content = json_decode($request->content, true);
                $duration = $content['duration'] ?? 0;
                $method = $content['method'] ?? '';
                $uri = $content['uri'] ?? '';
                $this->line("  {$duration}ms - {$method} {$uri}");
            }
        }

        $this->newLine();

        // Exceptions
        $this->info('⚠️  Recent Exceptions:');
        $exceptions = DB::table('telescope_entries')
            ->where('type', 'exception')
            ->where('created_at', '>=', now()->subHours($hours))
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        if ($exceptions->isEmpty()) {
            $this->line('  No exceptions 🎉');
        } else {
            foreach ($exceptions as $exception) {
                $content = json_decode($exception->content, true);
                $class = $content['class'] ?? 'Unknown';
                $message = substr($content['message'] ?? '', 0, 60);
                $this->line("  {$class}: {$message}...");
            }
        }

        $this->newLine();
        $this->info("✅ Report complete! Visit /telescope for detailed analysis.");

        return Command::SUCCESS;
    }
}
