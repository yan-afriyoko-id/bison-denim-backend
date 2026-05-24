<?php

return [

    'token' => env('RAJA_ONGKIR_KEY', ''),

    'base_url' => env('RAJA_ONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),

    'origin_city_code' => env('RAJA_ONGKIR_ORIGIN_CITY_CODE', 155), // 155 = jakarta utara
    'origin_district_code' => env('RAJA_ONGKIR_ORIGIN_DISTRICT_CODE', null),

];
