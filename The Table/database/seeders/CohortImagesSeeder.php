<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cohort;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CohortImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder copies images from the source folder to Laravel's public storage
     * and assigns them to cohorts as featured images and gallery images.
     */
    public function run()
    {
        $sourceFolder = 'C:\wamp64\www\The round table\images';
        $storageFolder = storage_path('app/public/cohorts/images');
        
        // Ensure storage directory exists
        if (!File::exists($storageFolder)) {
            File::makeDirectory($storageFolder, 0755, true);
        }

        // Get all cohorts
        $cohorts = Cohort::all();
        
        if ($cohorts->isEmpty()) {
            $this->command->info('No cohorts found. Please seed cohorts first.');
            return;
        }

        // Business/property images for real estate and business cohorts
        $businessImages = ['mb1.jpg', 'mb2.jpg', 'mb3.jpg'];
        
        // Investment images for equipment, renewable, IP cohorts
        $investmentImages = ['inv5.jpg', 'inv7.jpg'];
        
        // Video frame images for showcase/mixed use
        $videoFrames = [
            'videoframe_4633.png',
            'videoframe_6207.png',
            'videoframe_14198.png',
            'videoframe_28362.png'
        ];

        // Additional generic images
        $genericImages = [
            '1137ddc75940f4648e4da7578e561c9f.jpg',
            '1850558e0e33509c13a317dcb8e0a29d.jpg',
            '1d1c59e10dc609feb4988bd1245d765e.jpg',
            '6371eb0e5abb1bf3b103a54f488be0df.jpg',
            '6747d59f57dbf34e67778ecc5de538b5.jpg',
            '7cfdefc1bd70744bf519f2123669d747.jpg',
            '808ab685466744b319e2b60ba85d1c3e.jpg',
            '9e7921e04836312db29706f87f76fd4a.jpg',
            'new1.jpg',
            'new3.jpg',
            'new4.jpg',
            'new5.jpg'
        ];

        $this->command->info("Processing {$cohorts->count()} cohorts...");

        foreach ($cohorts as $index => $cohort) {
            // Determine which image set to use based on asset type
            $imagePool = match($cohort->asset_type) {
                'real_estate', 'business' => $businessImages,
                'equipment', 'renewable_energy', 'intellectual_property' => $investmentImages,
                default => $videoFrames,
            };

            // Select featured image (rotate through pool)
            $featuredImage = $imagePool[$index % count($imagePool)];
            $sourcePath = $sourceFolder . '\\' . $featuredImage;
            
            // Skip if source doesn't exist
            if (!File::exists($sourcePath)) {
                $this->command->warn("Source image not found: {$featuredImage}");
                continue;
            }

            // Generate unique filename
            $extension = pathinfo($featuredImage, PATHINFO_EXTENSION);
            $uniqueName = 'cohort_' . $cohort->id . '_featured_' . time() . '.' . $extension;
            $destinationPath = $storageFolder . '/' . $uniqueName;

            // Copy file to storage
            File::copy($sourcePath, $destinationPath);
            
            // Save relative path to database
            $cohort->featured_image = 'cohorts/images/' . $uniqueName;

            // Add gallery images (2-4 images per cohort)
            $galleryCount = rand(2, 4);
            $galleryPaths = [];
            $allImages = array_merge($businessImages, $investmentImages, array_slice($videoFrames, 0, 2));
            
            for ($i = 0; $i < $galleryCount; $i++) {
                $galleryImage = $allImages[($index + $i) % count($allImages)];
                $gallerySourcePath = $sourceFolder . '\\' . $galleryImage;
                
                if (!File::exists($gallerySourcePath)) {
                    continue;
                }

                $galleryUniqueName = 'cohort_' . $cohort->id . '_gallery_' . $i . '_' . time() . '.' . pathinfo($galleryImage, PATHINFO_EXTENSION);
                $galleryDestinationPath = $storageFolder . '/' . $galleryUniqueName;

                File::copy($gallerySourcePath, $galleryDestinationPath);
                $galleryPaths[] = 'cohorts/images/' . $galleryUniqueName;
                
                // Small delay to ensure unique timestamps
                usleep(10000);
            }

            $cohort->images = $galleryPaths;
            $cohort->save();

            $this->command->info("✓ Added images to: {$cohort->title} (Featured: {$featuredImage}, Gallery: {$galleryCount} images)");
        }

        $this->command->info('✅ Cohort images seeded successfully!');
    }
}
