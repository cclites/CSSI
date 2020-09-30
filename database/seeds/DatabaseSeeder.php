<?php

use App\Models\State;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('states')->truncate();
        State::create(['title' => 'Alaska', 'code' => 'AK']);
        State::create(['title' => 'Alabama', 'code' => 'AL']);
        State::create(['title' => 'American Samoa', 'code' => 'AS']);
        State::create(['title' => 'Arizona', 'code' => 'AZ']);
        State::create(['title' => 'Arkansas', 'code' => 'AR']);
        State::create(['title' => 'California', 'code' => 'CA']);
        State::create(['title' => 'Colorado', 'code' => 'CO']);
        State::create(['title' => 'Connecticut', 'code' => 'CT']);
        State::create(['title' => 'Delaware', 'code' => 'DE']);
        State::create(['title' => 'District of Columbia', 'code' => 'DC']);
        State::create(['title' => 'Federated States of Micronesia', 'code' => 'FM']);
        State::create(['title' => 'Florida', 'code' => 'FL']);
        State::create(['title' => 'Georgia', 'code' => 'GA']);
        State::create(['title' => 'Guam', 'code' => 'GU']);
        State::create(['title' => 'Hawaii', 'code' => 'HI']);
        State::create(['title' => 'Idaho', 'code' => 'ID']);
        State::create(['title' => 'Illinois', 'code' => 'IL']);
        State::create(['title' => 'Indiana', 'code' => 'IN']);
        State::create(['title' => 'Iowa', 'code' => 'IA']);
        State::create(['title' => 'Kansas', 'code' => 'KS']);
        State::create(['title' => 'Kentucky', 'code' => 'KY']);
        State::create(['title' => 'Louisiana', 'code' => 'LA']);
        State::create(['title' => 'Maine', 'code' => 'ME']);
        State::create(['title' => 'Marshall Islands', 'code' => 'MH']);
        State::create(['title' => 'Maryland', 'code' => 'MD']);
        State::create(['title' => 'Massachusetts', 'code' => 'MA']);
        State::create(['title' => 'Michigan', 'code' => 'MI']);
        State::create(['title' => 'Minnesota', 'code' => 'MN']);
        State::create(['title' => 'Mississippi', 'code' => 'MS']);
        State::create(['title' => 'Missouri', 'code' => 'MO']);
        State::create(['title' => 'Montana', 'code' => 'MT']);
        State::create(['title' => 'Nebraska', 'code' => 'NE']);
        State::create(['title' => 'Nevada', 'code' => 'NV']);
        State::create(['title' => 'New Hampshire', 'code' => 'NH']);
        State::create(['title' => 'New Jersey', 'code' => 'NJ']);
        State::create(['title' => 'New Mexico', 'code' => 'NM']);
        State::create(['title' => 'New York', 'code' => 'NY']);
        State::create(['title' => 'North Carolina', 'code' => 'NC']);
        State::create(['title' => 'North Dakota', 'code' => 'ND']);
        State::create(['title' => 'Northern Mariana Islands', 'code' => 'MP']);
        State::create(['title' => 'Ohio', 'code' => 'OH']);
        State::create(['title' => 'Oklahoma', 'code' => 'OK']);
        State::create(['title' => 'Oregon', 'code' => 'OR']);
        State::create(['title' => 'Palau', 'code' => 'PW']);
        State::create(['title' => 'Pennsylvania', 'code' => 'PA']);
        State::create(['title' => 'Puerto Rico', 'code' => 'PR']);
        State::create(['title' => 'Rhode Island', 'code' => 'RI']);
        State::create(['title' => 'South Carolina', 'code' => 'SC']);
        State::create(['title' => 'South Dakota', 'code' => 'SD']);
        State::create(['title' => 'Tennessee', 'code' => 'TN']);
        State::create(['title' => 'Texas', 'code' => 'TX']);
        State::create(['title' => 'Utah', 'code' => 'UT']);
        State::create(['title' => 'Vermont', 'code' => 'VT']);
        State::create(['title' => 'Virgin Islands', 'code' => 'VI']);
        State::create(['title' => 'Virginia', 'code' => 'VA']);
        State::create(['title' => 'Washington', 'code' => 'WA']);
        State::create(['title' => 'West Virginia', 'code' => 'WV']);
        State::create(['title' => 'Wisconsin', 'code' => 'WI']);
        State::create(['title' => 'Wyoming', 'code' => 'WY']);
    }
}
