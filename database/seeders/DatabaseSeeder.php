<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(QuestionAnswerSeeder::class);
        $this->call(HowItWorkSeeder::class);
        $this->call(TermsAndCondition::class);
        $this->call(PrivacyPolicy::class);
        $this->call(PrepareContract::class);
        $this->call(HomeContent::class);
        $this->call(PricesContent::class);
        $this->call(SettingSeeder::class);
        $this->call(HelpCenter::class);
        $this->call(WhoSeeder::class);
        $this->call(GeneralSection::class);
        $this->call(MetaData::class);
        $this->call(ArticleSection::class);
        $this->call(EmailRecoveryPasswordSeeder::class);
    }
}
