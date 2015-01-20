<?php
namespace Subbly\Frontend\Helpers\Layout;

/*
 * This file is part of Handlebars.php Helpers Set
 *
 * (c) Dmitriy Simushev <simushevds@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Handlebars\Context;
use Handlebars\Helper as HelperInterface;
use Handlebars\Template;

/**
 * A helper for templates extending.
 *
 * This helper works as a wrapper for one or more "override" blocks which
 * defines name of the parent template.
 *
 * Example of usage:
 * ```handlebars
 *   {{#extends parentTemplateName}}
 *     {{#override "blockName"}}
 *       Overridden block content
 *     {{/override}}
 *   {{/extends}}
 * ```
 *
 * Arguments:
 *  - "parentTemplateName": Name of the template that should be extended.
 *
 * @author Dmitriy Simushev <simushevds@gmail.com>
 */
class ExtendsHelper extends AbstractBlockHelper implements HelperInterface
{
    /**
     * The current inheritance level of the templates.
     * @var int
     */
    protected $level = 0;

    /**
     * {@inheritdoc}
     */
    public function execute(Template $template, Context $context, $args, $source)
    {
        // Get name of the parent template
        $parsed_args = $template->parseArguments($args);
        if (count($parsed_args) != 1) {
            throw new \InvalidArgumentException(
                '"extends" helper expects exactly one argument.'
            );
        }
        $parent_template = $context->get(array_shift($parsed_args));

        // Render content inside "extends" block to override blocks
        $template->render($context);

        // We need another instance of \Handlebars\Template to render parent
        // template. It can be got from Handlebars engine, so get the engine.
        $handlebars = $template->getEngine();

// dd($handlebars);
        // add Subbly's helpers
        // $handlebars->addHelper( 'products',      new \Subbly\Frontend\Helpers\ProductsHelper() );
        // $handlebars->addHelper( 'product',       new \Subbly\Frontend\Helpers\ProductHelper() );
        // $handlebars->addHelper( 'images',        new \Subbly\Frontend\Helpers\ProductImagesHelper() );
        // $handlebars->addHelper( 'image',         new \Subbly\Frontend\Helpers\ProductDefaultImageHelper() );
        // $handlebars->addHelper( 'url',           new \Subbly\Frontend\Helpers\UrlHelper() );
        // $handlebars->addHelper( 'assets',        new \Subbly\Frontend\Helpers\AssetsHelper() );
        // $handlebars->addHelper( 'price',         new \Subbly\Frontend\Helpers\PriceHelper() );
        // $handlebars->addHelper( 'compare',       new \Subbly\Frontend\Helpers\CompareHelper() );
        // $handlebars->addHelper( 'upper',         new \Subbly\Frontend\Helpers\UpperHelper() );
        // $handlebars->addHelper( 'lower',         new \Subbly\Frontend\Helpers\LowerHelper() );
        // $handlebars->addHelper( 'capitalize',    new \Subbly\Frontend\Helpers\CapitalizeHelper() );
        // $handlebars->addHelper( 'capitalizeAll', new \Subbly\Frontend\Helpers\CapitalizeAllHelper() );
        // $handlebars->addHelper( 'formatDate',    new \Subbly\Frontend\Helpers\FormatDateHelper() );
        // $handlebars->addHelper( 'truncate',      new \Subbly\Frontend\Helpers\TruncateHelper() );
        // $handlebars->addHelper( 'default',       new \Subbly\Frontend\Helpers\DefaultHelper() );

        // Render the parent template
        $this->level++;
        $buffer = $handlebars->render($parent_template, $context);
        $this->level--;

        if ($this->level == 0) {
            // The template and all its parents are rendered. Clean up the
            // storage.
            $this->blocksStorage->clear();
        }

        return $buffer;
    }
}
