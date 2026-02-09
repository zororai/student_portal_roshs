<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddHomepageSettings extends Migration
{
    public function up()
    {
        $settings = [
            // Tab Bar Menu Items
            [
                'key' => 'home_tab_1_text',
                'value' => 'Roshs Life',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Tab 1 Text',
                'description' => 'First tab menu item text',
                'order' => 1
            ],
            [
                'key' => 'home_tab_2_text',
                'value' => 'Graduation',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Tab 2 Text',
                'description' => 'Second tab menu item text',
                'order' => 2
            ],
            [
                'key' => 'home_tab_3_text',
                'value' => 'Athletics',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Tab 3 Text',
                'description' => 'Third tab menu item text',
                'order' => 3
            ],
            [
                'key' => 'home_tab_4_text',
                'value' => 'Social',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Tab 4 Text',
                'description' => 'Fourth tab menu item text',
                'order' => 4
            ],
            [
                'key' => 'home_tab_5_text',
                'value' => 'Location',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Tab 5 Text',
                'description' => 'Fifth tab menu item text',
                'order' => 5
            ],
            [
                'key' => 'home_tab_6_text',
                'value' => 'Call us',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Tab 6 Text',
                'description' => 'Sixth tab menu item text',
                'order' => 6
            ],
            [
                'key' => 'home_tab_7_text',
                'value' => 'Email',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Tab 7 Text',
                'description' => 'Seventh tab menu item text',
                'order' => 7
            ],

            // About Section
            [
                'key' => 'home_about_title',
                'value' => 'About Rose of Sharon',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'About Section Title',
                'description' => 'Title for the about section on homepage',
                'order' => 10
            ],
            [
                'key' => 'home_about_text',
                'value' => 'Then Rose Of Sharon High School will know that he is the Lord God who lives in Zion his holy mountain, Rose of Sharon High School will remain forever and foreign mountains will drip with sweet wine and the hills will flow with milk, water will fill the streambeds of Rose of Sharon High School and the fountain will burst forth from the Lord\'s temple watering the arid valleys of Rose of Sharon High School. Rose of Sharon High School will remain forever and Rose of Sharon High School will endure through all future generations',
                'type' => 'textarea',
                'group' => 'homepage',
                'label' => 'About Section Text',
                'description' => 'Description text for the about section',
                'order' => 11
            ],
            [
                'key' => 'home_facebook_embed',
                'value' => 'https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fprofile.php%3Fid%3D100094334670439&tabs=timeline&width=500&height=500&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId=699810548919387',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Facebook Page Embed URL',
                'description' => 'Facebook page plugin embed URL',
                'order' => 12
            ],

            // Director Section
            [
                'key' => 'home_director_name',
                'value' => 'Dr. Fatima Maruta',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Director Name (Homepage)',
                'description' => 'Name of the director displayed on homepage',
                'order' => 15
            ],
            [
                'key' => 'home_director_title',
                'value' => 'DIRECTOR OF EDUCATION',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Director Title (Homepage)',
                'description' => 'Title of the director on homepage',
                'order' => 16
            ],
            [
                'key' => 'home_director_bio',
                'value' => 'Dr. Fatima Maruta is a holder of several accounting qualifications that include a Bachelor\'s Degree in Accountancy from the University of Zimbabwe and Masters Degree in Business Adminstration from Bloomsburg University, PA USA. In year 2014, she was conferred with an Honorary Doctorate Degree in Humane Letters, DHL, from the International Institute of Philanthropy IIP in recognition of practical application of expertise in Humanities. In 2016 she was conferred with an Honorary Doctor of Arts Degree and an Honorary Master of Business Leadership Degree from International Women\'s University in recognition of practical application of expertise in Humanities. In the same year, she was conferred an award as Zimbabwe Top female academic leader by the Zimbabwe Leadership Awards',
                'type' => 'textarea',
                'group' => 'homepage',
                'label' => 'Director Bio (Homepage)',
                'description' => 'Director biography on homepage',
                'order' => 17
            ],
            [
                'key' => 'home_director_image',
                'value' => 'images/img2.png',
                'type' => 'image',
                'group' => 'homepage',
                'label' => 'Director Image (Homepage)',
                'description' => 'Director image on homepage',
                'order' => 18
            ],

            // Clubs Section
            [
                'key' => 'home_clubs_title',
                'value' => 'OUR CLUBS',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Clubs Section Title',
                'description' => 'Title for the clubs section',
                'order' => 20
            ],
            [
                'key' => 'home_club_1_title',
                'value' => 'DRAMA',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Club 1 Title',
                'description' => 'First club title',
                'order' => 21
            ],
            [
                'key' => 'home_club_1_text',
                'value' => 'This club forms the basis of raising facts on specified topics hence providing indispensable to people. It is also aimed at developing and sharpening communication skills and inculcating confidence within learners.',
                'type' => 'textarea',
                'group' => 'homepage',
                'label' => 'Club 1 Description',
                'description' => 'First club description',
                'order' => 22
            ],
            [
                'key' => 'home_club_1_image',
                'value' => 'images/drama.jpg',
                'type' => 'image',
                'group' => 'homepage',
                'label' => 'Club 1 Image',
                'description' => 'First club image',
                'order' => 23
            ],
            [
                'key' => 'home_club_2_title',
                'value' => 'INTERACT',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Club 2 Title',
                'description' => 'Second club title',
                'order' => 24
            ],
            [
                'key' => 'home_club_2_text',
                'value' => 'This club existed since 2012 at R.O.S.H. It is aimed at providing social support to the vulnerable and the deprived or underprivileged. It came into existence after the realization of some people in needy.',
                'type' => 'textarea',
                'group' => 'homepage',
                'label' => 'Club 2 Description',
                'description' => 'Second club description',
                'order' => 25
            ],
            [
                'key' => 'home_club_2_image',
                'value' => 'images/inreaction.jpg',
                'type' => 'image',
                'group' => 'homepage',
                'label' => 'Club 2 Image',
                'description' => 'Second club image',
                'order' => 26
            ],
            [
                'key' => 'home_club_3_title',
                'value' => 'DEBATE',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Club 3 Title',
                'description' => 'Third club title',
                'order' => 27
            ],
            [
                'key' => 'home_club_3_text',
                'value' => 'This club forms the basis of raising facts on specified topics hence providing indispensable to people. It is also aimed at developing and sharpening communication skills and inculcating confidence within learners.',
                'type' => 'textarea',
                'group' => 'homepage',
                'label' => 'Club 3 Description',
                'description' => 'Third club description',
                'order' => 28
            ],
            [
                'key' => 'home_club_3_image',
                'value' => 'images/dedate.jpg',
                'type' => 'image',
                'group' => 'homepage',
                'label' => 'Club 3 Image',
                'description' => 'Third club image',
                'order' => 29
            ],

            // Declaration Section
            [
                'key' => 'home_declaration_title',
                'value' => 'SCHOOL DECLARATION',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Declaration Section Title',
                'description' => 'Title for the school declaration section',
                'order' => 30
            ],
            [
                'key' => 'home_declaration_text',
                'value' => 'Then Rose Of Sharon High School will know that he is the Lord God who lives in Zion his holy mountain, Rose of Sharon High School will remain forever and foreign mountains will drip with sweet wine and the hills will flow with milk, water will fill the streambeds of Rose of Sharon High School and the fountain will burst forth from the Lord\'s temple watering the arid valleys of Rose of Sharon High School. Rose of Sharon High School will remain forever and Rose of Sharon High School will endure through all future generations.',
                'type' => 'textarea',
                'group' => 'homepage',
                'label' => 'Declaration Text',
                'description' => 'School declaration text',
                'order' => 31
            ],
            [
                'key' => 'home_declaration_image',
                'value' => 'images/req.jpg',
                'type' => 'image',
                'group' => 'homepage',
                'label' => 'Declaration Image',
                'description' => 'Image for school declaration section',
                'order' => 32
            ],

            // Footer Section
            [
                'key' => 'footer_vision_text',
                'value' => 'Our Vision is provide a well-groomed, enriched (in ideas) and productive learner given a firm foundation for tertiary and life challenges.',
                'type' => 'textarea',
                'group' => 'homepage',
                'label' => 'Footer Vision Text',
                'description' => 'Vision text displayed in footer',
                'order' => 40
            ],
            [
                'key' => 'footer_quick_link_1',
                'value' => 'Join Us',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Quick Link 1',
                'description' => 'First quick link text',
                'order' => 41
            ],
            [
                'key' => 'footer_quick_link_2',
                'value' => 'Maintenance',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Quick Link 2',
                'description' => 'Second quick link text',
                'order' => 42
            ],
            [
                'key' => 'footer_quick_link_3',
                'value' => 'Language Packs',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Quick Link 3',
                'description' => 'Third quick link text',
                'order' => 43
            ],
            [
                'key' => 'footer_quick_link_4',
                'value' => 'LearnPress',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Quick Link 4',
                'description' => 'Fourth quick link text',
                'order' => 44
            ],
            [
                'key' => 'footer_quick_link_5',
                'value' => 'Release Status',
                'type' => 'text',
                'group' => 'homepage',
                'label' => 'Quick Link 5',
                'description' => 'Fifth quick link text',
                'order' => 45
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('website_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }

    public function down()
    {
        $keys = [
            'home_tab_1_text', 'home_tab_2_text', 'home_tab_3_text', 'home_tab_4_text',
            'home_tab_5_text', 'home_tab_6_text', 'home_tab_7_text',
            'home_about_title', 'home_about_text', 'home_facebook_embed',
            'home_director_name', 'home_director_title', 'home_director_bio', 'home_director_image',
            'home_clubs_title', 'home_club_1_title', 'home_club_1_text', 'home_club_1_image',
            'home_club_2_title', 'home_club_2_text', 'home_club_2_image',
            'home_club_3_title', 'home_club_3_text', 'home_club_3_image',
            'home_declaration_title', 'home_declaration_text', 'home_declaration_image',
            'footer_vision_text', 'footer_quick_link_1', 'footer_quick_link_2',
            'footer_quick_link_3', 'footer_quick_link_4', 'footer_quick_link_5'
        ];

        DB::table('website_settings')->whereIn('key', $keys)->delete();
    }
}
