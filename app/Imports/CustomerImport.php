<?php

namespace App\Imports;

use App\Model\Customer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToCollection, WithHeadingRow
{
    public $method = '';
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function __construct($method)
    {
        $this->method = $method;
    }
    public function collection(Collection $rows)
    {
        if($this->method == 'create'){
            foreach ($rows as $row) {
                $tag_name_arr = explode(";", $row['tag_name']);
                $line_1_arr = explode(";", $row['line_1']);
                $line_2_arr = explode(";", $row['line_2']);
                $pin_arr = explode(";", $row['pin']);
                $country_id_arr = explode(";", $row['country_id']);
                $state_id_arr = explode(";", $row['state_id']);
                $city_id_arr = explode(";", $row['city_id']);
                $saleperson_id_arr = explode(";", $row['saleperson_id']);
                $init_bal_arr = explode(";", $row['init_bal']);
                $init_bal_date_arr = explode(";", $row['init_bal_date']);
                $approved_arr = explode(";", $row['approved']);
                $tally_arr = explode(";", $row['tally']);
                $phone_arr = explode(";", $row['phone']);
                $phone_country_id_arr = explode(";", $row['phone_country_id']);
            }
        }
    }
}
