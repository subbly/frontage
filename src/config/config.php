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
      , 'getInput'      => 'Subbly\Frontage\Helpers\Post\GetInput' 

      // Session
      , 'loginFrom'     => 'Subbly\Frontage\Helpers\Post\LoginHelper'
      , 'logoutFrom'    => 'Subbly\Frontage\Helpers\Post\LogoutHelper'

      // User
      , 'addressFrom'   => 'Subbly\Frontage\Helpers\Post\AddressHelper'
      , 'userAddresses' => 'Subbly\Frontage\Helpers\Subbly\UserAddressesHelper'
      , 'userAddress'   => 'Subbly\Frontage\Helpers\Subbly\UserAddressHelper'

      // cart
      , 'cart'          => 'Subbly\Frontage\Helpers\Subbly\CartHelper' 
      , 'hasCart'       => 'Subbly\Frontage\Helpers\Subbly\CartToggleHelper'  
      , 'cartTotal'     => 'Subbly\Frontage\Helpers\Subbly\CartTotalHelper' 
      , 'cartCount'     => 'Subbly\Frontage\Helpers\Subbly\CartCountHelper' 
      , 'cartAdd'       => 'Subbly\Frontage\Helpers\Post\CartAddHelper'
      , 'carUpdateQty'  => 'Subbly\Frontage\Helpers\Post\CartUpdateQtyHelper' 
      , 'cartRemove'    => 'Subbly\Frontage\Helpers\Post\CartRemoveHelper'
      , 'cartEmpty'     => 'Subbly\Frontage\Helpers\Post\CartDestroyHelper' 

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
