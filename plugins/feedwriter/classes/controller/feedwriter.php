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
    /**
     * Bucket currently being read
     * @var Model_Bucket
     */
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
        $feed = new Feedwriter_Rss(($this->bucket->account->account_path != 'default') ? $this->bucket->account->account_path.' / '.$this->bucket->bucket_name : $this->bucket->bucket_name, 'http://'.$_SERVER['SERVER_NAME'].$this->bucket->get_base_url());
        $feed->setDescription('Drops from '.$this->bucket->bucket_name.' on Swiftriver');
        // SwiftRiver doesn't support multi-lingualism yet!
        $feed->setLanguage(Kohana::$config->load('feedwriter')->get('language'));
        $feed->setCopyright(Kohana::$config->load('feedwriter')->get('copyright'));
        if (count($this->droplets) > 0)
        {
            $feed->setUpdated(date_format(date_create($this->droplets[0]['droplet_date_pub']), DATE_RSS));
            foreach ($this->droplets as $k => $v)
            {
                $feed->addItem(array(
                    'title' => $v['droplet_title'],
                    'guid' => 'http://'.$_SERVER['SERVER_NAME'].'/'.$this->request->param('account').'/bucket/'.$this->request->param('name').'/drop/'.$v['id'],
                    'link' => 'http://'.$_SERVER['SERVER_NAME'].'/'.$this->request->param('account').'/bucket/'.$this->request->param('name').'/drop/'.$v['id'],
                    //'author' => $v['identity_name'], -- Requires email for valid
                    'description' => $v['droplet_content'],
                    'time' => date_format(date_create($v['droplet_date_pub']), DATE_RSS)
                ));
            }
        }
        //header("content-type: text/xml");
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
            'link'          => ($link) ? $link : 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],
            'description'   => "A generic RSS2 feed.",
            'language'      => 'en-us',
            'copyright'     => "Copyright (c) 2008-2012 Ushahidi Inc <http://ushahidi.com>",
            'lastBuildDate' => "Mon, 30 Sep 2002 11:00:00 GMT",
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
            'title' => $params['title'],
            'guid' => $params['guid'],
            'link' => $params['link'],
            //'author' => $params['author'], -- Requires email for valid
            'description' => $params['description'],
            'pubDate' => $params['time']
        );
    }
    
    public function generate()
    {
        $rss  = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $rss .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"><channel><atom:link href="'.'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"].'" rel="self" type="application/rss+xml" />';
        
        foreach ($this->meta as $key => $value)
        {
            $rss .= '<'.$key.'>'.htmlentities($value).'</'.$key.'>';
        }
        
        foreach ($this->items as $key => $value)
        {
            $rss .= '<item>';
            foreach ($value as $k => $v)
            {
                $rss .= '<'.$k.'>'.htmlentities($v).'</'.$k.'>';
            }
            $rss .= '</item>';
        }
        
        $rss .= '</channel></rss>';
        return $rss;
    }
}

?>

