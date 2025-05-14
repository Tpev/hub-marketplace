<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\MedicalDevice;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Carbon;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate sitemap.xml file with all key routes';

    public function handle()
    {
        $sitemap = Sitemap::create();

        // Static Pages
        $sitemap->add(Url::create(url('/'))); // fallback to actual root

        $sitemap->add(Url::create(route('medical_devices.index')));
        $sitemap->add(Url::create(route('blog.index')));

 BlogPost::all()->each(function ($post) use ($sitemap) {
    $sitemap->add(
        Url::create(route('blog.show', $post->slug))
            ->setLastModificationDate($post->updated_at)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            ->setPriority(0.7)
    );
});


        // Medical Devices
        \App\Models\MedicalDevice::query()->get()->each(function ($device) use ($sitemap) {
            $sitemap->add(
                Url::create(route('medical_devices.show', $device))
                    ->setLastModificationDate($device->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8)
            );
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('✅ sitemap.xml generated successfully!');
    }
}
