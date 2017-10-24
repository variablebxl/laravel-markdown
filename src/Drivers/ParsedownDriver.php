<?php
/**
 * Laravel-Markdown
 *
 * A small, lightweight and easy-to-use Laravel package for
 * handling markdown.
 *
 * @author    Andreas Indal <andreas@rocketship.se>
 * @package   andreasindal/laravel-markdown
 * @link      https://github.com/andreasindal/laravel-markdown
 * @license   MIT
 */

namespace Indal\Markdown\Drivers;

use Parsedown;

class ParsedownDriver implements MarkdownDriver
{
    protected $parser;

    /**
     * Constructs a new ParsedownDriver instance.
     *
     * @param  array  $config
     */
    public function __construct(array $config)
    {
        $this->parser = new Parsedown;

        $this->setOptions($config);
    }

    /**
     * {@inheritDoc}
     */
    public function text($text)
    {
        $parsed_text = $this->parser->text($text);
        // Add <figure> & Video wrapper
        $parsed_text = $this->parseMedia($parsed_text);
        return $parsed_text;
    }

    /**
     * {@inheritDoc}
     */
    public function line($text)
    {
        return $this->parser->line($text);
    }

    private function setOptions(array $config)
    {
        if (isset($config['urls'])) {
            $this->parser->setUrlsLinked($config['urls']);
        }

        if (isset($config['escape_markup'])) {
            $this->parser->setMarkupEscaped($config['escape_markup']);
        }

        if (isset($config['breaks'])) {
            $this->parser->setBreaksEnabled($config['breaks']);
        }
    }


    /**
     * Parse <img> and <iframe> tags to wrap them
     *
     * @param  string  $text
     * @return string
     */

    public static function parseMedia($text)
    {
        // Wrap iframes (video embed)
        $text =  preg_replace('~<iframe [^>]*>[^>]*</iframe>~', '<div class="embed-container">$0</div>', $text);
        // Remove <p> around images
        $text = preg_replace('%<p>(<img .*?/>)</p>%i', '$1', $text);
        // Add <figure> around images
        $text =  preg_replace('~<img [^>]*>~', '<figure>$0</figure>', $text);
        return $text;
    }
}
