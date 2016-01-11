<?php

namespace Common\Core\Twig\Extensions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Contains all Forkcms filters for Twig
 *
 * @author <thijs.dp@gmail.com>
 */

use Twig_SimpleFilter;
use Twig_SimpleFunction;

class TwigFilters
{
    /**
     * //http://twig.sensiolabs.org/doc/advanced.html#id2
     * returns a collection of Twig SimpleFilters
     *
     * @return array
     */
    public static function getFilters(&$twig, $app)
    {
        $app .= '\Core\Engine\TemplateModifiers';
        $twig->addFilter(new Twig_SimpleFilter('geturlforblock', $app.'::getURLForBlock'));
        $twig->addFilter(new Twig_SimpleFilter('geturlforextraid', $app.'::getURLForExtraId'));
        $twig->addFilter(new Twig_SimpleFilter('getpageinfo', $app.'::getPageInfo'));
        $twig->addFilter(new Twig_SimpleFilter('highlight', $app.'::highlightCode'));
        $twig->addFilter(new Twig_SimpleFilter('profilesetting', $app.'::profileSetting'));
        $twig->addFilter(new Twig_SimpleFilter('formatcurrency', $app.'::formatCurrency'));
        $twig->addFilter(new Twig_SimpleFilter('usersetting', $app.'::userSetting'));
        $twig->addFilter(new Twig_SimpleFilter('uppercase', $app.'::uppercase'));
        $twig->addFilter(new Twig_SimpleFilter('trans', $app.'::trans'));
        $twig->addFilter(new Twig_SimpleFilter('rand', $app.'::random'));
        $twig->addFilter(new Twig_SimpleFilter('formatfloat', $app.'::formatFloat'));
        $twig->addFilter(new Twig_SimpleFilter('truncate', $app.'::truncate'));
        $twig->addFilter(new Twig_SimpleFilter('camelcase', $app.'::toCamelCase'));
        $twig->addFilter(new Twig_SimpleFilter('snakeCase', $app.'::snakeCase'));
        $twig->addFilter(new Twig_SimpleFilter('stripnewlines', $app.'::stripNewlines'));
        $twig->addFilter(new Twig_SimpleFilter('formatnumber', $app.'::formatNumber'));
        $twig->addFilter(new Twig_SimpleFilter('tolabel', $app.'::toLabel'));
        $twig->addFilter(new Twig_SimpleFilter('cleanupplaintext', $app.'::cleanupPlainText'));

        // PHP exposed functions

        $twig->addFilter(new Twig_SimpleFilter('urlencode', 'urlencode'));
        $twig->addFilter(new Twig_SimpleFilter('striptags', 'strip_tags'));
        $twig->addFilter(new Twig_SimpleFilter('sprintf', 'sprintf'));
        $twig->addFilter(new Twig_SimpleFilter('addslashes', 'addslashes'));
        $twig->addFilter(new Twig_SimpleFilter('count', 'count'));
        $twig->addFilter(new Twig_SimpleFilter('is_array', 'is_array'));

        // Deprecated functions

        $twig->addFilter(new Twig_SimpleFilter('spoondate', $app.'::spoonDate'));
        $twig->addFilter(new Twig_SimpleFilter('formatdate', $app.'::formatDate'));
        $twig->addFilter(new Twig_SimpleFilter('formattime', $app.'::formatTime'));
        $twig->addFilter(new Twig_SimpleFilter('timeago', $app.'::timeAgo'));
        $twig->addFilter(new Twig_SimpleFilter('formatdatetime', $app.'::formatDateTime'));

        // Filters with raw html output

        $twig->addFunction(new Twig_SimpleFunction(
            'getmainnavigation',
            $app.'::getMainNavigation'
        ));
        $twig->addFunction(new Twig_SimpleFunction(
            'geturl',
            $app.'::getURL'
        ));

        $twig->addFilter(new Twig_SimpleFilter(
            'getnavigation',
            $app.'::getNavigation',
            array('is_safe' => array('html'))
        ));
        $twig->addFilter(new Twig_SimpleFilter(
            'getsubnavigation',
            $app.'::getSubNavigation',
            array('is_safe' => array('html'))
        ));
        $twig->addFilter(new Twig_SimpleFilter(
            'parsewidget',
            $app.'::parseWidget',
            array('is_safe' => array('html'))
        ));
    }
}
