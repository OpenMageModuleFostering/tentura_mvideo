<?php

$installer = $this;

$installer->startSetup();

$installer->run("


CREATE TABLE IF NOT EXISTS {$this->getTable('mvideo')} (
  `mvideo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `key` text NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `created_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`mvideo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;


CREATE TABLE IF NOT EXISTS {$this->getTable('mvideo_for_products')} (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) NOT NULL,
  `mvideo_code` varchar(200) NOT NULL,
  `video_type` int(11) NOT NULL,
  `html_text` text NOT NULL,
  `file_name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

");

$installer->endSetup(); 