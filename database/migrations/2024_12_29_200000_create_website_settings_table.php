<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateWebsiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, color, image, textarea
            $table->string('group')->default('general'); // general, colors, images, text
            $table->string('label');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Insert default settings
        $this->seedDefaultSettings();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('website_settings');
    }

    /**
     * Seed default website settings
     */
    private function seedDefaultSettings()
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'Rose Of Sharon High School',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Site Name',
                'description' => 'The name of the school displayed on the website',
                'order' => 1
            ],
            [
                'key' => 'site_tagline',
                'value' => 'Foundation',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Site Tagline',
                'description' => 'A short tagline for the school',
                'order' => 2
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@roshs.co.zw',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Contact Email',
                'description' => 'Main contact email address',
                'order' => 3
            ],
            [
                'key' => 'contact_phone',
                'value' => '+263 771 142 8629',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Contact Phone',
                'description' => 'Main contact phone number',
                'order' => 4
            ],
            [
                'key' => 'whatsapp_number',
                'value' => '2637711428629',
                'type' => 'text',
                'group' => 'general',
                'label' => 'WhatsApp Number',
                'description' => 'WhatsApp number (without + or spaces)',
                'order' => 5
            ],
            [
                'key' => 'address',
                'value' => '6884 Mt Madecheche Road, Zimre Park',
                'type' => 'textarea',
                'group' => 'general',
                'label' => 'School Address',
                'description' => 'Physical address of the school',
                'order' => 6
            ],

            // Theme Colors
            [
                'key' => 'primary_color',
                'value' => '#2d5016',
                'type' => 'color',
                'group' => 'colors',
                'label' => 'Primary Color',
                'description' => 'Main theme color (green)',
                'order' => 1
            ],
            [
                'key' => 'secondary_color',
                'value' => '#1a365d',
                'type' => 'color',
                'group' => 'colors',
                'label' => 'Secondary Color',
                'description' => 'Secondary theme color (blue)',
                'order' => 2
            ],
            [
                'key' => 'accent_color',
                'value' => '#d69e2e',
                'type' => 'color',
                'group' => 'colors',
                'label' => 'Accent Color',
                'description' => 'Accent color for highlights',
                'order' => 3
            ],
            [
                'key' => 'header_bg_color',
                'value' => '#ffffff',
                'type' => 'color',
                'group' => 'colors',
                'label' => 'Header Background',
                'description' => 'Background color of the header',
                'order' => 4
            ],
            [
                'key' => 'footer_bg_color',
                'value' => '#1a202c',
                'type' => 'color',
                'group' => 'colors',
                'label' => 'Footer Background',
                'description' => 'Background color of the footer',
                'order' => 5
            ],

            // Images
            [
                'key' => 'site_logo',
                'value' => 'images/logo.png',
                'type' => 'image',
                'group' => 'images',
                'label' => 'Site Logo',
                'description' => 'Main logo displayed on the website',
                'order' => 1
            ],
            [
                'key' => 'favicon',
                'value' => 'images/favicon.ico',
                'type' => 'image',
                'group' => 'images',
                'label' => 'Favicon',
                'description' => 'Small icon shown in browser tab',
                'order' => 2
            ],
            [
                'key' => 'footer_logo',
                'value' => 'images/logo.png',
                'type' => 'image',
                'group' => 'images',
                'label' => 'Footer Logo',
                'description' => 'Logo displayed in the footer',
                'order' => 3
            ],

            // Text Content
            [
                'key' => 'about_text',
                'value' => 'Then Rose Of Sharon High School will know that he is the Lord God who lives in Zion his holy mountain, Rose of Sharon High School will remain forever and foreign mountains will drip with sweet wine and the hills will flow with milk, water will fill the streambeds of Rose of Sharon High School and the fountain will burst forth from the Lord\'s temple watering the arid valleys of Rose of Sharon High School. Rose of Sharon High School will remain forever and Rose of Sharon High School will endure through all future generations',
                'type' => 'textarea',
                'group' => 'text',
                'label' => 'About Text',
                'description' => 'Main about section text on homepage',
                'order' => 1
            ],
            [
                'key' => 'vision_text',
                'value' => 'Our Vision is provide a well-groomed, enriched (in ideas) and productive learner given a firm foundation for tertiary and life challenges.',
                'type' => 'textarea',
                'group' => 'text',
                'label' => 'Vision Statement',
                'description' => 'School vision statement',
                'order' => 2
            ],
            [
                'key' => 'mission_text',
                'value' => 'To provide quality education that nurtures academic excellence, moral values, and holistic development.',
                'type' => 'textarea',
                'group' => 'text',
                'label' => 'Mission Statement',
                'description' => 'School mission statement',
                'order' => 3
            ],
            [
                'key' => 'declaration_text',
                'value' => 'Then Rose Of Sharon High School will know that he is the Lord God who lives in Zion his holy mountain, Rose of Sharon High School will remain forever and foreign mountains will drip with sweet wine and the hills will flow with milk, water will fill the streambeds of Rose of Sharon High School and the fountain will burst forth from the Lord\'s temple watering the arid valleys of Rose of Sharon High School. Rose of Sharon High School will remain forever and Rose of Sharon High School will endure through all future generations.',
                'type' => 'textarea',
                'group' => 'text',
                'label' => 'School Declaration',
                'description' => 'School declaration text',
                'order' => 4
            ],
            [
                'key' => 'director_name',
                'value' => 'Dr. Fatima Maruta',
                'type' => 'text',
                'group' => 'text',
                'label' => 'Director Name',
                'description' => 'Name of the school director',
                'order' => 5
            ],
            [
                'key' => 'director_title',
                'value' => 'DIRECTOR OF EDUCATION',
                'type' => 'text',
                'group' => 'text',
                'label' => 'Director Title',
                'description' => 'Title of the director',
                'order' => 6
            ],
            [
                'key' => 'director_bio',
                'value' => 'Dr. Fatima Maruta is a holder of several accounting qualifications that include a Bachelor\'s Degree in Accountancy from the University of Zimbabwe and Masters Degree in Business Adminstration from Bloomsburg University, PA USA. In year 2014, she was conferred with an Honorary Doctorate Degree in Humane Letters, DHL, from the International Institute of Philanthropy IIP in recognition of practical application of expertise in Humanities.',
                'type' => 'textarea',
                'group' => 'text',
                'label' => 'Director Biography',
                'description' => 'Biography of the school director',
                'order' => 7
            ],
            [
                'key' => 'copyright_text',
                'value' => 'All rights reserved | This website is made with love by Lotusdreammaker',
                'type' => 'text',
                'group' => 'text',
                'label' => 'Copyright Text',
                'description' => 'Copyright text in footer',
                'order' => 8
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('website_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}
