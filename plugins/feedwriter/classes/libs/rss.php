<?php defined('SYSPATH') OR die('No direct script access');

/**
 * RSS-Lib for the FeedWriter plugin
 *
 * @package   SwiftRiver
 * @author    Ushahidi Team
 * @category  Plugins
 * @copyright (c) 2008-2012 Ushahidi Inc <http://ushahidi.com>
 */

class Feedwriter_Rss
{
    private $meta = array();
    private $items = array();

    public function __construct($title = NULL, $link = NULL)
    {
        $this->meta = array(
            'title'         => ($title) ? $title : "RSS2 Feed",
            'link'          => ($link) ? $link :
                URL::site(Kohana_Request::detect_uri(), true),
            'description'   => "A generic RSS2 feed.",
            'language'      => 'en-us',
            'copyright'     => "Copyright (c) 2008-2012 Ushahidi Inc ".
                "<http://ushahidi.com>",
            'lastBuildDate' => "Thu, 01 Jan 1970 00:00:00 +0000",
            'generator'     => "SwiftRiver FeedWriter"
        );
    }

    public function set_title($title)
    {
        $this->meta['title'] = $title;
    }

    public function set_link($link)
    {
        $this->meta['link'] = $link;
    }

    public function set_description($description)
    {
        $this->meta['description'] = $description;
    }

    public function set_language($language)
    {
        $this->meta['language'] = $language;
    }

    public function set_copyright($copyright)
    {
        $this->meta['copyright'] = $copyright;
    }

    public function set_updated($lastBuildDate)
    {
        $this->meta['lastBuildDate'] = date(DATE_RSS, strtotime($lastBuildDate));
    }

    public function add_item($params)
    {
        $this->items[] = array(
            'title'       => $params['title'],
            'guid'        => $params['guid'],
            'link'        => $params['link'],
            'description' => $params['description'],
            'pubDate'     => date(DATE_RSS, strtotime($params['time']))
        );
    }

    public function generate()
    {
        $rss  = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $rss .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'.
            '<channel><atom:link href="'.URL::site(Kohana_Request::detect_uri(),
            true).'" rel="self" type="application/rss+xml" />';

        foreach ($this->meta as $key => $value)
            $rss .= '<'.$key.'>'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'</'.$key.'>';

        foreach ($this->items as $key => $value)
        {
            $rss .= '<item>';
            foreach ($value as $k => $v)
                $rss .= '<'.$k.'>'.htmlspecialchars($v, ENT_QUOTES, 'UTF-8').'</'.$k.'>';
            $rss .= '</item>';
        }

        $rss .= '</channel></rss>';
        return $rss;
    }
}

?>
