<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Atom-Lib for the FeedWriter plugin
 *
 * @package   SwiftRiver
 * @author    Ushahidi Team
 * @category  Plugins
 * @copyright (c) 2008-2012 Ushahidi Inc <http://ushahidi.com>
 */

class Feedwriter_Atom
{
    private $meta = array();
    private $author = array();
    private $items = array();

    public function __construct($title = NULL, $link = NULL)
    {
        $this->meta = array(
            'link'      => ($link) ? $link :
                URL::site(Kohana_Request::detect_uri(), true),
            'title'     => ($title) ? $title : "Atom Feed",
            'subtitle'  => "A generic Atom feed.",
            'id'        => ($link) ? $link :
                URL::site(Kohana_Request::detect_uri(), true),
            'rights'    => "Copyright (c) 2008-2012 Ushahidi Inc ".
                "<http://ushahidi.com>",
            'updated'   => "Thu, 01 Jan 1970 00:00:00 +0000",
            'generator' => "SwiftRiver FeedWriter",
            'logo'      => URL::site('media/img/logo-swiftriver.png', true)
        );

        $this->author = array(
            'name' => 'SwiftRiver',
            'uri'  => URL::site('', true)
        );
    }

    public function set_title($title)
    {
        $this->meta['title'] = $title;
    }

    public function set_link($link)
    {
        $this->meta['link'] = $link;
        $this->meta['id'] = $link;
    }

    public function set_description($description)
    {
        $this->meta['subtitle'] = $description;
    }

    public function set_copyright($copyright)
    {
        $this->meta['rights'] = $copyright;
    }

    public function set_updated($updated)
    {
        $this->meta['updated'] = date(DATE_ATOM, strtotime($updated));
    }
    
    public function set_author($author, $uri)
    {
        $this->author['name'] = $author;
        $this->author['uri'] = $uri;
    }

    public function add_item($params)
    {
        $this->items[] = array(
            'title'   => $params['title'],
            'id'      => $params['id'],
            'link'    => $params['link'],
            'content' => $params['content'],
            'updated' => date(DATE_ATOM, strtotime($params['time'])),
            'author'  => $params['author']
        );
    }

    public function generate()
    {
        $atom  = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $atom .= '<feed xmlns="http://www.w3.org/2005/Atom"><link href="'.
            URL::site(Kohana_Request::detect_uri(), true).'" rel="self" />';

        foreach ($this->meta as $key => $value)
        {
            if ($key != 'link')
                $atom .= '<'.$key.'>'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'</'.$key.'>';
            else
                $atom .= '<'.$key.' href="'.$value.'" />';
        }

        $atom .= '<author>';
        foreach ($this->author as $key => $value)
            $atom .= '<'.$key.'>'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'</'.$key.'>';
        $atom .= '</author>';

        foreach ($this->items as $key => $value)
        {
            $atom .= '<entry>';
            foreach ($value as $k => $v)
            {
                switch ($k)
                {
                    case 'author':
                        $atom .= '<'.$k.'><name>'.htmlspecialchars($v, ENT_QUOTES, 'UTF-8').'</name></'.$k.'>';
                        break;
                    case 'link':
                        $atom .= '<'.$k.' href="'.$v.'" />';
                        break;
                    case 'content':
                        $atom .= '<'.$k.' type="html">'.htmlspecialchars($v, ENT_QUOTES, 'UTF-8').'</'.$k.'>';
                        break;
                    default:
                        $atom .= '<'.$k.'>'.htmlspecialchars($v, ENT_QUOTES, 'UTF-8').'</'.$k.'>';
                }
            }
            $atom .= '</entry>';
        }

        $atom .= '</feed>';
        return $atom;
    }
}

?>
