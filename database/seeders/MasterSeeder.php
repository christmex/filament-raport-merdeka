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
            ['topic_setting_name' => 'Topic 1', 'sort_order' => 1],
            ['topic_setting_name' => 'Topic 2', 'sort_order' => 2],
            ['topic_setting_name' => 'Topic 3', 'sort_order' => 3],
            ['topic_setting_name' => 'Topic 4', 'sort_order' => 4],
            ['topic_setting_name' => 'Topic 5', 'sort_order' => 5],
            ['topic_setting_name' => 'Topic 6', 'sort_order' => 6],
            ['topic_setting_name' => 'Topic 7', 'sort_order' => 7],
        ];

        DB::table('topic_settings')->insertOrIgnore($topic_settings);

        $school_terms = [
            ['school_term_name' => 'Ganjil'],
            ['school_term_name' => 'Genap'],
        ];

        DB::table('school_terms')->insertOrIgnore($school_terms);

        $classrooms = [
            ['classroom_name' => 'Matthew 1', 'school_level' => 1],
            ['classroom_name' => 'Matthew 2', 'school_level' => 1],
            ['classroom_name' => 'Matthew 3', 'school_level' => 1],
            ['classroom_name' => 'Mark 1', 'school_level' => 2],
            ['classroom_name' => 'Mark 2', 'school_level' => 2],
            ['classroom_name' => 'Mark 3', 'school_level' => 2],
            ['classroom_name' => 'Luke 1', 'school_level' => 3],
            ['classroom_name' => 'Luke 2', 'school_level' => 3],
            ['classroom_name' => 'Luke 3', 'school_level' => 3],
            ['classroom_name' => 'John 1', 'school_level' => 4],
            ['classroom_name' => 'John 2', 'school_level' => 4],
            ['classroom_name' => 'John 3', 'school_level' => 4],
            ['classroom_name' => 'Acts 1', 'school_level' => 5],
            ['classroom_name' => 'Acts 2', 'school_level' => 5],
            ['classroom_name' => 'Acts 3', 'school_level' => 5],
            ['classroom_name' => 'Romans 1', 'school_level' => 6],
            ['classroom_name' => 'Romans 2', 'school_level' => 6],
            ['classroom_name' => 'Romans 3', 'school_level' => 6],
            ['classroom_name' => 'Joshua 1', 'school_level' => 7],
            ['classroom_name' => 'Joshua 2', 'school_level' => 7],
            ['classroom_name' => 'Joshua 3', 'school_level' => 7],
            ['classroom_name' => 'Caleb 1', 'school_level' => 8],
            ['classroom_name' => 'Caleb 2', 'school_level' => 8],
            ['classroom_name' => 'Caleb 3', 'school_level' => 8],
            ['classroom_name' => 'Moses 1', 'school_level' => 9],
            ['classroom_name' => 'Moses 2', 'school_level' => 9],
            ['classroom_name' => 'Moses 3', 'school_level' => 9],
            ['classroom_name' => 'Jacob 1', 'school_level' => 10],
            ['classroom_name' => 'Jacob 2', 'school_level' => 10],
            ['classroom_name' => 'Jacob 3', 'school_level' => 10],
            ['classroom_name' => 'Isaac 1 IPA', 'school_level' => 11],
            ['classroom_name' => 'Isaac 2 IPA', 'school_level' => 11],
            ['classroom_name' => 'Isaac 3 IPA', 'school_level' => 11],
            ['classroom_name' => 'Isaac 1 IPS', 'school_level' => 11],
            ['classroom_name' => 'Isaac 2 IPS', 'school_level' => 11],
            ['classroom_name' => 'Isaac 3 IPS', 'school_level' => 11],
            ['classroom_name' => 'Abraham 1 IPA', 'school_level' => 12],
            ['classroom_name' => 'Abraham 2 IPA', 'school_level' => 12],
            ['classroom_name' => 'Abraham 3 IPA', 'school_level' => 12],
            ['classroom_name' => 'Abraham 1 IPS', 'school_level' => 12],
            ['classroom_name' => 'Abraham 2 IPS', 'school_level' => 12],
            ['classroom_name' => 'Abraham 3 IPS', 'school_level' => 12],
        ];

        DB::table('classrooms')->insertOrIgnore($classrooms);

        $subjects = [
            ['subject_name' => 'Matematika'],
            ['subject_name' => 'Maths'],
            ['subject_name' => 'Bahasa Indonesia'],
            ['subject_name' => 'Bahasa Inggris'],
            ['subject_name' => 'IPA'],
            ['subject_name' => 'IPS'],
            ['subject_name' => 'Geografi'],
            ['subject_name' => 'Sejarah Peminatan XI IPA'],
            ['subject_name' => 'Sejarah'],
        ];

        DB::table('subjects')->insertOrIgnore($subjects);

        $school_settings = [
            [
                'school_name_prefix' => 'SDS Kristen',
                'school_name_suffix' => '1',
                'school_address' => 'Jl.Laksamana Kawasan Industri No.1, Batam center',
                'school_principal_name' => 'Yanthi',
                'school_progress_report_date' => date('d').' '.date('M').' '.date('Y'),
            ],
        ];

        DB::table('school_settings')->insertOrIgnore($school_settings);

    }
}
