<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Controller for the FeedWriter plugin
 *
 * @package   SwiftRiver
 * @author    Ushahidi Team
 * @category  Plugins
 * @copyright (c) 2008-2012 Ushahidi Inc <http://ushahidi.com>
 */

class Controller_Feedwriter extends Controller_Swiftriver
{
    protected $bucket;
    protected $droplets;
    protected $ready;

    public function before()
    {
        parent::before();
        $this->auto_render = false;
        $this->bucket = ORM::factory('bucket')
            ->where('bucket_name_url', '=', $this->request->param('name'))
            ->find();

        // If load successful, grab most recent 10 droplets and save
        if ($this->bucket->loaded())
        {
            $droplets = Model_Bucket::get_droplets($this->user->id,
                $this->bucket->id, 0, NULL, PHP_INT_MAX, FALSE, array(), 10);
            $this->droplets = $droplets['droplets'];
        }
        else
            throw new HTTP_Exception_404();
    }

    public function action_rss()
    {
        $account = $this->bucket->account->account_path;
        $name = $this->bucket->bucket_name;

        $feed = new Feedwriter_Rss;
        $feed->setTitle(($account != 'default') ? $account.' / '.$name : $name);
        $feed->setLink(URL::site($this->bucket->get_base_url(), true));
        $feed->setDescription('Drops from '.$this->bucket->bucket_name.
            ' on Swiftriver');
        $feed->setLanguage(Kohana::$config->load('feedwriter')->get('language'));
        $feed->setCopyright(Kohana::$config->load('feedwriter')->get('copyright'));

        if (count($this->droplets) > 0)
        {
            $feed->setUpdated(date(DATE_RSS,strtotime($this->
                droplets[0]['droplet_date_pub'])));
            foreach ($this->droplets as $k => $v)
            {
                $url = URL::site($this->request->param('account').'/bucket/'.
                    $this->request->param('name').'/drop/'.$v['id'], true);
                $feed->addItem(array(
                    'title' => $v['droplet_title'],
                    'guid' => $url,
                    'link' => $url,
                    'description' => $v['droplet_content'],
                    'time' => date(DATE_RSS, strtotime($v['droplet_date_pub']))
                ));
            }
        }

        $this->response->headers('Content-Type', 'text/xml');
        echo $feed->generate();
    }
}

class Feedwriter_Rss
{
    private $meta = array();
    private $items = array();

    public function __construct($title = NULL, $link = NULL)
    {
        $this->meta = array(
            'title'         => ($title) ? $title : "RSS2 Feed",
            'link'          => ($link) ? $link :
                URL::site($_SERVER["REQUEST_URI"], true),
            'description'   => "A generic RSS2 feed.",
            'language'      => 'en-us',
            'copyright'     => "Copyright (c) 2008-2012 Ushahidi Inc ".
                "<http://ushahidi.com>",
            'lastBuildDate' => "Thu, 01 Jan 1970 00:00:00 +0000",
            'generator'     => "SwiftRiver FeedWriter"
        );
    }

    public function setTitle($title)
    {
        $this->meta['title'] = $title;
    }

    public function setLink($link)
    {
        $this->meta['link'] = $link;
    }

    public function setDescription($description)
    {
        $this->meta['description'] = $description;
    }

    public function setLanguage($language)
    {
        $this->meta['language'] = $language;
    }

    public function setCopyright($copyright)
    {
        $this->meta['copyright'] = $copyright;
    }

    public function setUpdated($lastBuildDate)
    {
        $this->meta['lastBuildDate'] = $lastBuildDate;
    }

    public function addItem($params)
    {
        $this->items[] = array(
            'title'       => $params['title'],
            'guid'        => $params['guid'],
            'link'        => $params['link'],
            'description' => $params['description'],
            'pubDate'     => $params['time']
        );
    }

    public function generate()
    {
        $rss  = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $rss .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'.
            '<channel><atom:link href="'.URL::site($_SERVER["REQUEST_URI"],
            true).'" rel="self" type="application/rss+xml" />';

        foreach ($this->meta as $key => $value)
            $rss .= '<'.$key.'>'.htmlentities($value).'</'.$key.'>';

        foreach ($this->items as $key => $value)
        {
            $rss .= '<item>';
            foreach ($value as $k => $v)
                $rss .= '<'.$k.'>'.htmlentities($v).'</'.$k.'>';
            $rss .= '</item>';
        }

        $rss .= '</channel></rss>';
        return $rss;
    }
}

?>
