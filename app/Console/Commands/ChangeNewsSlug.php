<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Models\Redirect;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ChangeNewsSlug extends Command
{
    protected $signature = 'change_news_slug {oldSlug} {newSlug}';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $oldSlug = $this->argument('oldSlug');
        $newSlug = $this->argument('newSlug');

        if ($oldSlug === $newSlug)
        {
            $this->error('The slugs are equal');
            return 1;
        }

        $redirect = Redirect::query()
            -> where('old_slug', route('news_item', ['slug' => $oldSlug], false))['path']
            -> where('new_slug', route('news_item', ['slug' => $newSlug], false))['path']
            ->first();
        if ($redirect !== null)
        {
            $this->error('The same request for redirect had already been made');
            return 1;
        }

        $news = News::where('slug', $oldSlug)->first();
        if ($news === null)
        {
            $this->error('The news wasn\'t found');
            return 1;
        }

        DB::transaction(function() use ($news, $newSlug) {
            Redirect::where('old_slug', route('news_item', ['slug' => $newSlug], false))['path']->delete();
            $news->slug = $newSlug;
            $news->save();
        });

        return 0;
    }
}
