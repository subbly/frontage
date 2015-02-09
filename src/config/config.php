<?php

return [
    'helpers' => [
        'products'      => 'Subbly\Frontage\Helpers\Subbly\ProductsHelper'
      , 'product'       => 'Subbly\Frontage\Helpers\Subbly\ProductHelper'
      , 'image'         => 'Subbly\Frontage\Helpers\Subbly\ProductDefaultImageHelper'
      , 'url'           => 'Subbly\Frontage\Helpers\Subbly\UrlHelper'
      , 'assets'        => 'Subbly\Frontage\Helpers\Subbly\AssetsHelper'
      , 'formatPrice'   => 'Subbly\Frontage\Helpers\Subbly\PriceHelper'
      , 'formErrors'    => 'Subbly\Frontage\Helpers\Subbly\FormErrorsHelper'
      , 'userAddresses' => 'Subbly\Frontage\Helpers\Subbly\UserAddressesHelper'
      , 'userAddress'   => 'Subbly\Frontage\Helpers\Subbly\UserAddressHelper'
      // , 'isUserLogin'   => 'Subbly\Frontage\Helpers\Subbly\UserCheckHelper'
      , 'loginFrom'     => 'Subbly\Frontage\Helpers\Post\LoginHelper'
      , 'addressFrom'   => 'Subbly\Frontage\Helpers\Post\AddressHelper'
      , 'getInput'      => 'Subbly\Frontage\Helpers\Post\GetInput' 

      , 'compare'       => 'Subbly\Frontage\Helpers\Usefull\CompareHelper'
      , 'capitalize'    => 'Subbly\Frontage\Helpers\Usefull\CapitalizeHelper'
      , 'capitalizeAll' => 'Subbly\Frontage\Helpers\Usefull\CapitalizeAllHelper'
      , 'formatDate'    => 'Subbly\Frontage\Helpers\Usefull\FormatDateHelper'
      , 'default'       => 'Subbly\Frontage\Helpers\Usefull\DefaultHelper'
    ]

  , 'dataTestSet' => [
        'name'     => 'Test PAGE'
      , 'isActive' => false
      , 'first'    => true
      , 'second'   => 'a'
      , 'other_genres' => 
        [
            'genres' => 
            [
                'yop'
              , 'test'
            ]
        ]
      , 'genres' => 
        [
              'Hip-Hop'
            , 'Rap'
            , 'Techno'
            , 'Country'
        ]
      , 'object' => [
          'key' => 'value'
        ]
      , 'cars' => 
        [
          [
            'category' => 'Foreign',
            'count' => 4,
            'list' => [
                'Toyota',
                'Kia',
                'Honda',
                'Mazda'
            ]
          ]
        , [
            'category' => 'WTF',
            'count' => 1,
            'list' => [
                'Fiat'
            ]
          ]
        , [
            'category' => 'Luxury',
            'count' => 2,
            'list' => [
                'Mercedes Benz',
                'BMW'
            ]
          ]
        , [
            'category' => 'Rich People Shit',
            'count' => 3,
            'list' => [
                'Ferrari',
                'Bugatti',
                'Rolls Royce'
            ]
          ]
      ]
  ]
];
