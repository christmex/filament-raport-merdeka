<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school_years = [
            ['school_year_name' => date('Y').'/'.date('Y', strtotime('+1 year')),],
            ['school_year_name' => date('Y', strtotime('+1 year')).'/'.date('Y', strtotime('+2 year')),],
            ['school_year_name' => date('Y', strtotime('+2 year')).'/'.date('Y', strtotime('+3 year')),]
        ];
        DB::table('school_years')->insertOrIgnore($school_years);

        $assessment_method_settings = [
            ['assessment_method_setting_name' => 'Tes Lisan'],
            ['assessment_method_setting_name' => 'Penugasan'],
            ['assessment_method_setting_name' => 'Kinerja(Project, Portofolio, Rubrik)'],
            ['assessment_method_setting_name' => 'Monthly Test'],
        ];
        
        DB::table('assessment_method_settings')->insertOrIgnore($assessment_method_settings);

        $topic_settings = [
            ['topic_setting_name' => 'Topic 1'],
            ['topic_setting_name' => 'Topic 2'],
            ['topic_setting_name' => 'Topic 3'],
            ['topic_setting_name' => 'Topic 4'],
            ['topic_setting_name' => 'Topic 5'],
            ['topic_setting_name' => 'Topic 6'],
            ['topic_setting_name' => 'Topic 7'],
        ];

        DB::table('topic_settings')->insertOrIgnore($topic_settings);

        $school_terms = [
            ['school_term_name' => 'Ganjil'],
            ['school_term_name' => 'Genap'],
        ];

        DB::table('school_terms')->insertOrIgnore($school_terms);

    }
}
