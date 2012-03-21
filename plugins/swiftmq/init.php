<?php defined('SYSPATH') OR die('No direct script access');

/**
 * Init for the MQ plugin
 *
 * @package SwiftRiver
 * @author Ushahidi Team
 * @category Plugins
 * @copyright (c) 2008-2011 Ushahidi Inc <htto://www.ushahidi.com>
 */
class SwiftMQ_Init {
	
	private $mq_host = 'localhost';

	public function __construct() 
	{
		// After a new channel option has been added to the DB
		Swiftriver_Event::add('swiftriver.channel.option.post_save', 
		    array($this, 'publish_new_channel_option'));
		
		// Before the channel option is deleted from the DB
		Swiftriver_Event::add('swiftriver.channel.option.pre_delete',
		    array($this, 'publish_delete_channel_option'));
		
		$this->mq_host = Kohana::$config->load('mq.mq_host');
	}


	/**
	 * Publishes new channel options to the MQ mainly for the fetchers to start fetching it
	 */
	public function publish_new_channel_option()
	{
		// Get the event data
		$channel_option = & Swiftriver_Event::$data;
		
		$message = array(
			'channel' => $channel_option->channel_filter->channel,
			'river_id' => $channel_option->channel_filter->river->id,
			'key' => $channel_option->key,
			'value' => $channel_option->value
		);
		
		Kohana::$log->add(Log::DEBUG, "New channel option :message", 
			array(':message' => var_export($message, true)));
		
		try
		{
			$conn = new AMQPConnection();
			$conn->setHost($this->mq_host);
			$conn->connect();
			$chan = new AMQPChannel($conn);
			$ex = new AMQPExchange($chan);
			$ex->setName('chatter');
			$ex->setType(AMQP_EX_TYPE_TOPIC);
			$ex->setFlags(AMQP_DURABLE);
			$ex->declare();
			$routing_key = 'web.channel_option.'.$channel_option->channel_filter->channel.'.add';
			$ex->publish(json_encode($message), $routing_key, AMQP_IMMEDIATE);
			Kohana::$log->add(Log::DEBUG, "New channel option published to ".$routing_key);
		}
		catch (AMQPConnectionException $e)
		{
			// Do nothing
			Kohana::$log->add(Log::ERROR, " Connection error publishing to the MQ");
		}
	}	

	/**
	 * Publishes channel option deletion through the MQ mainly for the fetchers to update accordingly
	 */
	public function publish_delete_channel_option()
	{
		// Get the event data
		$channel_option = & Swiftriver_Event::$data;

		$message = array(
			'channel' => $channel_option->channel_filter->channel,
			'river_id' => $channel_option->channel_filter->river->id,
			'key' => $channel_option->key,
			'value' => $channel_option->value
		);
		
		Kohana::$log->add(Log::DEBUG, 
			"Deleting channel option :message", array(':message' => var_export($message, TRUE)));
		
		try
		{
			$conn = new AMQPConnection();
			$conn->setHost($this->mq_host);			
			$conn->connect();
			$chan = new AMQPChannel($conn);
			$ex = new AMQPExchange($chan);
			$ex->setName('chatter');
			$ex->setType(AMQP_EX_TYPE_TOPIC);
			$ex->setFlags(AMQP_DURABLE);
			$ex->declare();
			$routing_key = 'web.channel_option.'.$channel_option->channel_filter->channel.'.delete';
			$ex->publish(json_encode($message), $routing_key, AMQP_IMMEDIATE);
			Kohana::$log->add(Log::DEBUG, "Channel option deletion published to ".$routing_key);
		}
		catch (AMQPConnectionException $e)
		{
			// Do nothing
			Kohana::$log->add(Log::ERROR, " Connection error publishing to the MQ");
		}
	}

}

new SwiftMQ_Init;

?>