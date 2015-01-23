<?php
namespace Subbly\Frontage\Helpers\Subbly;

use Handlebars\Context;
use Handlebars\Helper;
use Handlebars\Template;
use Subbly\Frontage\Helpers\CustomHelper;

class ProductsHelper extends CustomHelper
{
  /**
   * Execute the helper
   *
   * @param \Handlebars\Template $template The template instance
   * @param \Handlebars\Context  $context  The current context
   * @param array                $args     The arguments passed the the helper
   * @param string               $source   The source
   *
   * @return mixed
   */
  public function execute( Template $template, Context $context, $args, $source )
  {
    $props = $this->parseProps( $args, $context );
    $args  = $this->parseArgs( $args );

    // Products query
    // ----------------
    
    $productsOptions = [
      //   'includes' => [ 'images', 'categories' ]
      // , 'limit'    => 2
      // , 'offset'   => 0
      // , 'order_by' => ['created_at' => 'desc']
        'where'    => [
           // ['quantity', '=', 1]
            ['status', '!=', 'draft']
          , ['status', '!=', 'hidden']
        ]
    ];

    // Includes
    // ----------------

    if( 
      isset( $props['includes'] ) 
      && is_array( $props['includes'] )
    )
      $productsOptions['includes'] = $props['includes'];

    // Where
    // ----------------

    if( 
      isset( $props['where'] ) 
      && is_array( $props['where'] )
    ) {
      // $productsOptions['where'] = [];

      foreach( $props['where'] as $condition )
      {
        if( 
          is_array( $condition )
          && count( $condition ) == 3
        )
          $productsOptions['where'][] = $condition;
      }
    }

    // Order By
    // ----------------

    if( 
      isset( $props['order_by'] ) 
      && is_array( $props['order_by'] )
    ) {
      $productsOptions['order_by'] = [];

      foreach( $props['order_by'] as $order )
      {
        if( 
          is_array( $order )
          && count( $order ) == 2
        )
          $productsOptions['order_by'][ $order[0] ] = $order[1];
      }
    }

    // Offset & limit
    // ----------------

    if( isset( $props['limit'] ) && is_integer( $props['limit'] ) )
      $productsOptions['limit'] = $props['limit'];

    $offset = null;

    if( 
        isset( $props['offset'] ) 
        && is_integer( $props['offset'] ) 
        && isset( $productsOptions['limit'] ) 
    )
      $productsOptions['offset'] = $props['offset'];

    if(
      !isset( $productsOptions['offset'] )
      && isset( $props['page'] ) 
      && is_integer( $props['page'] ) 
    ) {
        $offset = ( (int) $props['page'] - 1 ) * $productsOptions['limit'];
        $offset = $offset < 0 ? 0 : $offset;
    }

    $offset ?: 0;

    $productsOptions['offset'] = $offset;

    // Categories
    // ----------------

    $hasCategories = (
      is_array( $props )
      &&  
      (
            array_key_exists( 'category', $props )
        ||  array_key_exists( 'subcategory', $props ) 
      )
    );

    if( $hasCategories )
    {
      $categoriesOptions = [];

      if( isset( $props['category'] ) )
        $categoriesOptions[] = ['slug', $props['category'] ];

      if( isset( $props['subcategory'] ) )
        $categoriesOptions[] = ['slug', $props['subcategory'] ];

      if( count( $categoriesOptions ) > 0 )
      {
        $categories = \Subbly\Subbly::api('subbly.category')->all([
            'has'      => [
              'translations' => $categoriesOptions
            ]
        ]);
      }
      else
      {
        $hasCategories = false;
      }
    }

    if( 
        $hasCategories 
      && count( $categories )
    ) {
      $productsOptions['has']['categories'] = [];

      foreach( $categories as $category ) 
      {
        $productsOptions['has']['categories'][] = ['category_id', $category->id];
      }
    }

    // Get products
    // -----------------
    $products = \Subbly\Subbly::api('subbly.product')->all( $productsOptions )->toArray();

    $context->push( ['products' => $products]);
    
    $tmp    = $products;
    $buffer = '';

    if( !$tmp) 
    {
      $template->setStopToken('else');
      $template->discard();
      $template->setStopToken(false);
      $buffer = $template->render($context);
    }
    elseif( is_array( $tmp ) || $tmp instanceof \Traversable )
    {
      $isList = is_array($tmp) && (array_keys($tmp) === range(0, count($tmp) - 1));
      $index = 0;
      $lastIndex = $isList ? (count($tmp) - 1) : false;

      foreach( $tmp as $key => $var ) 
      {
        $specialVariables = array(
            '@index' => $index,
            '@first' => ($index === 0),
            '@last' => ($index === $lastIndex),
        );
        if (!$isList) {
            $specialVariables['@key'] = $key;
        }
        $context->pushSpecialVariables($specialVariables);
        $context->push($var);
        $template->setStopToken('else');
        $template->rewind();
        $buffer .= $template->render($context);
        $context->pop();
        $context->popSpecialVariables();
        $index++;
      }

      $template->setStopToken(false);
    }

    return $buffer;
  }
}
