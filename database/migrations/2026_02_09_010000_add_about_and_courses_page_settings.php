<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAboutAndCoursesPageSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $settings = [
            // About Page Settings
            [
                'key' => 'about_page_title',
                'value' => 'About us',
                'type' => 'text',
                'group' => 'pages',
                'label' => 'About Page Title',
                'description' => 'Main title for the About page',
                'order' => 1
            ],
            [
                'key' => 'about_director_title',
                'value' => 'DIRECTOR OF EDUCATION',
                'type' => 'text',
                'group' => 'pages',
                'label' => 'Director Title (About Page)',
                'description' => 'Title of the director on About page',
                'order' => 2
            ],
            [
                'key' => 'about_director_bio',
                'value' => 'Dr. Fatima Maruta is a holder of several accounting qualifications that include a Bachelor\'s Degree in Accountancy from the University of Zimbabwe and Masters Degree in Business Adminstration from Bloomsburg University, PA USA. In year 2014, she was conferred with an Honorary Doctorate Degree in Humane Letters, DHL, from the International Institute of Philanthropy IIP in recognition of practical application of expertise in Humanities.',
                'type' => 'textarea',
                'group' => 'pages',
                'label' => 'Director Biography (About Page)',
                'description' => 'Biography text for the director',
                'order' => 3
            ],
            [
                'key' => 'about_director_image',
                'value' => 'images/img2.png',
                'type' => 'image',
                'group' => 'pages',
                'label' => 'Director Image',
                'description' => 'Image of the director',
                'order' => 4
            ],
            [
                'key' => 'about_awards_title',
                'value' => 'Award winning school',
                'type' => 'text',
                'group' => 'pages',
                'label' => 'Awards Section Title',
                'description' => 'Title for the awards section',
                'order' => 5
            ],
            [
                'key' => 'about_awards_text',
                'value' => 'In 2020 was conferred with an award Top female business and Community Development Leadership of the year. In 2020 was conferred with an honorary Doctorate of Entrepreneurship, Innovation Marverick Leadership Degree. In 2020 was conferred with a Philanthropic and Humanitarian Organisation of the year named among the Zimbabwe Top 100 business brands of the year.',
                'type' => 'textarea',
                'group' => 'pages',
                'label' => 'Awards Description',
                'description' => 'Description of awards and achievements',
                'order' => 6
            ],
            [
                'key' => 'about_awards_image',
                'value' => 'images/award.png',
                'type' => 'image',
                'group' => 'pages',
                'label' => 'Awards Image',
                'description' => 'Image for the awards section',
                'order' => 7
            ],
            [
                'key' => 'about_awards_additional',
                'value' => 'In 2021 I was conferred with the Honorary Commissionership with the Honour and Status of Commissioner. In 2021 I was conferred with the Honorary Doctorate of Diplomacy and International Relations Degree of a Doctor of Diplomacy and International Relations, Hon. DIR',
                'type' => 'textarea',
                'group' => 'pages',
                'label' => 'Additional Awards Text',
                'description' => 'Additional awards information',
                'order' => 8
            ],
            [
                'key' => 'about_school_section_title',
                'value' => 'Our School',
                'type' => 'text',
                'group' => 'pages',
                'label' => 'School Section Title',
                'description' => 'Title for the school gallery section',
                'order' => 9
            ],
            [
                'key' => 'about_gallery_image_1',
                'value' => 'images/p1.png',
                'type' => 'image',
                'group' => 'pages',
                'label' => 'Gallery Image 1',
                'description' => 'First gallery image',
                'order' => 10
            ],
            [
                'key' => 'about_gallery_caption_1',
                'value' => 'Our \'A\' level',
                'type' => 'text',
                'group' => 'pages',
                'label' => 'Gallery Caption 1',
                'description' => 'Caption for first gallery image',
                'order' => 11
            ],
            [
                'key' => 'about_gallery_image_2',
                'value' => 'images/p2.png',
                'type' => 'image',
                'group' => 'pages',
                'label' => 'Gallery Image 2',
                'description' => 'Second gallery image',
                'order' => 12
            ],
            [
                'key' => 'about_gallery_caption_2',
                'value' => 'Our Admin Stuff and students',
                'type' => 'text',
                'group' => 'pages',
                'label' => 'Gallery Caption 2',
                'description' => 'Caption for second gallery image',
                'order' => 13
            ],
            [
                'key' => 'about_gallery_image_3',
                'value' => 'images/p3.png',
                'type' => 'image',
                'group' => 'pages',
                'label' => 'Gallery Image 3',
                'description' => 'Third gallery image',
                'order' => 14
            ],
            [
                'key' => 'about_gallery_caption_3',
                'value' => 'Intermediate Accounting',
                'type' => 'text',
                'group' => 'pages',
                'label' => 'Gallery Caption 3',
                'description' => 'Caption for third gallery image',
                'order' => 15
            ],
            [
                'key' => 'about_history_title',
                'value' => 'Academy History',
                'type' => 'text',
                'group' => 'pages',
                'label' => 'History Section Title',
                'description' => 'Title for the history section',
                'order' => 16
            ],
            [
                'key' => 'about_history_text_1',
                'value' => 'Then Rose Of Sharon High School will know that he is the Lord God who lives in Zion his holy mountain, Rose of Sharon High School will remain forever and foreign mountains will drip with sweet wine and the hills will flow with milk,',
                'type' => 'textarea',
                'group' => 'pages',
                'label' => 'History Text Part 1',
                'description' => 'First part of history text',
                'order' => 17
            ],
            [
                'key' => 'about_history_text_2',
                'value' => 'water will fill the streambeds of Rose of Sharon High School and the fountain will burst forth from the Lord\'s temple watering the arid valleys of Rose of Sharon High School. Rose of Sharon High School will remain forever and Rose of Sharon High School will endure through all future generations',
                'type' => 'textarea',
                'group' => 'pages',
                'label' => 'History Text Part 2',
                'description' => 'Second part of history text',
                'order' => 18
            ],

            // Courses Page Settings
            [
                'key' => 'courses_page_title',
                'value' => 'Our Subjects',
                'type' => 'text',
                'group' => 'pages',
                'label' => 'Courses Page Title',
                'description' => 'Main title for the Courses page',
                'order' => 19
            ],
            [
                'key' => 'courses_intro_text',
                'value' => 'Explore our comprehensive range of subjects designed to provide quality education and prepare students for their future.',
                'type' => 'textarea',
                'group' => 'pages',
                'label' => 'Courses Introduction',
                'description' => 'Introduction text for courses page',
                'order' => 20
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('website_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $keys = [
            'about_page_title', 'about_director_title', 'about_director_bio', 'about_director_image',
            'about_awards_title', 'about_awards_text', 'about_awards_image', 'about_awards_additional',
            'about_school_section_title', 'about_gallery_image_1', 'about_gallery_caption_1',
            'about_gallery_image_2', 'about_gallery_caption_2', 'about_gallery_image_3', 
            'about_gallery_caption_3', 'about_history_title', 'about_history_text_1', 
            'about_history_text_2', 'courses_page_title', 'courses_intro_text'
        ];

        DB::table('website_settings')->whereIn('key', $keys)->delete();
    }
}
