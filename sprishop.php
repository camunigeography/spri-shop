<?php

# Bugs:
#!# Specials in the front page list don't show if their target table has a title__JOIN...
#!# Items marked as visible='N' should not necessarily generate a 404
#!# Images need to be denied, as the generator can be bypassed; consider a mod_rewrite rule which will deal with that automatically

# Future developments:
#!# Nightly flagging by e-mail of items out of stock
#!# Pagination and search needed


# Online shop application
require_once ('frontControllerApplication.php');
class sprishop extends frontControllerApplication
{
	# Function to assign defaults additional to the general application defaults
	public function defaults ()
	{
		# Specify available arguments as defaults or as NULL (to represent a required argument)
		$defaults = array (
			'applicationName'				=> 'Museum Shop',
			'hostname'						=> 'localhost',
			'database'						=> 'sprishop',
			'table'							=> 'shop',
			'div'							=> 'sprishop',
			'administrators'				=> true,
			'useEditing'					=> true,
			'imageStoreRoot'				=> '/images/shop',
			'sectionsImages'				=> '/_sections/',
			'imageGenerationStub'			=> '/images/generator',
			'showPublisherLinks'			=> true,
			'imageResizeTo'					=> 200,
			'tabUlClass'					=> 'tabsflat',
			'enableShoppingCart'			=> false,
			'enablePaymentWorkflow'			=> true,
			'shoppingCartPaymentUrl'		=> false,
			'shoppingCartSharedSecret'		=> false,
			'shoppingCartClientId'			=> false,
			'shoppingCartOrderPrefix'		=> false,
			'shoppingCartVatCode'			=> false,
			'shoppingCartOrderDescription'	=> 'Payment for online shop order no. %s',	// %s can be used to state the order number
		);
		
		# Return the defaults
		return $defaults;
	}
	
	
	# Function to assign supported actions
	public function actions ()
	{
		# Define available tasks
		$actions = array (
			'home' => array (
				'description' => false,
				'url' => '',
				'icon' => 'house',
				'tab' => 'Home',
				'droplist' => true,
			),
			'ordering' => array (
				'description' => false,
				'url' => 'ordering/',
				'tab' => 'How to order',
				'droplist' => true,
			),
			'listing' => array (
				'description' => false,
				'url' => '%1/',
				'usetab' => 'home',
				'droplist' => true,
			),
			'basket' => array (
				'description' => 'Basket',
				'tab' => 'Basket',
				'icon' => 'basket',
				'url' => 'basket/',
				'enableIf' => $this->settings['enableShoppingCart'],
			),
			'checkout' => array (
				'description' => 'Checkout',
				'usetab' => 'basket',
				'url' => 'checkout/',
				'enableIf' => $this->settings['enableShoppingCart'],
			),
			'callback' => array (
				'description' => false,
				'usetab' => 'basket',
				'url' => 'callback/',
				'enableIf' => $this->settings['enableShoppingCart'],
			),
			'orders' => array (
				'description' => false,
				'tab' => 'Orders',
				'icon' => 'wand',
				'url' => 'orders/',
				'administrator' => true,
				'enableIf' => $this->settings['enableShoppingCart'],
			),
		);
		
		# Return the actions
		return $actions;
	}
	
	
	# Database structure definition
	public function databaseStructure ()
	{
		# Define the SQL
		$sql = "
			CREATE TABLE `administrators` (
			  `username` varchar(255) COLLATE utf8_unicode_ci PRIMARY KEY NOT NULL COMMENT 'Username',
			  `active` enum('','Yes','No') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Yes' COMMENT 'Currently active?',
			  `privilege` enum('Administrator','Restricted administrator') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Administrator' COMMENT 'Administrator level'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='System administrators';
			
			CREATE TABLE `settings` (
			  `id` int(11) PRIMARY KEY NOT NULL COMMENT 'Automatic key (ignored)',
			  `feedbackRecipient` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Feedback recipient e-mail',
			  `introductionHtml` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Introductory text',
			  `orderingHtml` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Ordering page'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Settings';
			
			CREATE TABLE `books` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `grouping__JOIN__sprishop___booksSubtypes__reserved` int(11) DEFAULT NULL COMMENT 'Grouping',
			  `author__JOIN__sprishop___authors__reserved` int(11) NOT NULL,
			  `author2__JOIN__sprishop___authors__reserved` int(11) DEFAULT NULL,
			  `author3__JOIN__sprishop___authors__reserved` int(11) DEFAULT NULL,
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `pricePerUnit` float(5,2) NOT NULL DEFAULT '0.00',
			  `priceIncludesVat` enum('N','Y','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
			  `photographFilename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `stockAvailable` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `stockIdealLevel` int(10) UNSIGNED DEFAULT NULL,
			  `stockMinimumLevel` int(10) UNSIGNED DEFAULT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `descriptionLong` text COLLATE utf8_unicode_ci,
			  `visible` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `publisher__JOIN__sprishop___publishers__reserved` int(11) DEFAULT NULL,
			  `publicationDate` int(4) NOT NULL DEFAULT '0',
			  `isbn` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `pages` int(11) DEFAULT NULL,
			  `binding` enum('Hardback','Paperback') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Hardback',
			  `edition` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `childrens` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `cards` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `pricePerUnit` float(5,2) NOT NULL DEFAULT '0.00',
			  `priceIncludesVat` enum('N','Y','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `photographFilename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `stockAvailable` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `stockIdealLevel` int(10) UNSIGNED DEFAULT NULL,
			  `stockMinimumLevel` int(10) UNSIGNED DEFAULT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `descriptionLong` text COLLATE utf8_unicode_ci,
			  `visible` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `type__JOIN__sprishop___cardTypes__reserved` int(11) DEFAULT NULL,
			  `sizeWidthInCm` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `sizeHeightInCm` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Blank',
			  `colour` enum('Colour','Black & white','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Colour',
			  `numberPerPack` int(10) UNSIGNED NOT NULL DEFAULT '0'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `clothing` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `title__JOIN__sprishop___clothingTypes__reserved` int(11) NOT NULL,
			  `pricePerUnit` float(5,2) NOT NULL DEFAULT '0.00',
			  `priceIncludesVat` enum('N','Y','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `stockAvailable` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `stockIdealLevel` int(10) UNSIGNED DEFAULT NULL,
			  `stockMinimumLevel` int(10) UNSIGNED DEFAULT NULL,
			  `visible` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `size__JOIN__sprishop___sizes__reserved` int(11) DEFAULT NULL,
			  `colour__JOIN__sprishop___colours__reserved` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `material__JOIN__sprishop___materials__reserved` int(11) DEFAULT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `collectibles` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `pricePerUnit` float(5,2) NOT NULL,
			  `priceIncludesVat` enum('N','Y','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `photographFilename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `stockAvailable` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `stockIdealLevel` int(10) UNSIGNED DEFAULT NULL,
			  `stockMinimumLevel` int(10) UNSIGNED DEFAULT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `descriptionLong` text COLLATE utf8_unicode_ci,
			  `visible` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `type__JOIN__sprishop___jewelleryTypes__reserved` int(11) DEFAULT NULL,
			  `sizeInCm` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `jewellery` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `pricePerUnit` float(5,2) NOT NULL,
			  `priceIncludesVat` enum('N','Y','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `photographFilename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `stockAvailable` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `stockIdealLevel` int(10) UNSIGNED DEFAULT NULL,
			  `stockMinimumLevel` int(10) UNSIGNED DEFAULT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `descriptionLong` text COLLATE utf8_unicode_ci,
			  `visible` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `type__JOIN__sprishop___jewelleryTypes__reserved` int(11) DEFAULT NULL,
			  `sizeInCm` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `maps` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `pricePerUnit` float(5,2) NOT NULL DEFAULT '0.00',
			  `priceIncludesVat` enum('N','Y','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
			  `photographFilename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `stockAvailable` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `stockIdealLevel` int(10) UNSIGNED DEFAULT NULL,
			  `stockMinimumLevel` int(10) UNSIGNED DEFAULT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `descriptionLong` text COLLATE utf8_unicode_ci,
			  `visible` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `publisher__JOIN__sprishop___publishers__reserved` int(11) DEFAULT NULL,
			  `scaleAs1To` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `mapDate` date DEFAULT NULL,
			  `publicationDate` date NOT NULL,
			  `isbn` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `sizeFoldedHeightInCm` int(10) UNSIGNED DEFAULT NULL,
			  `sizeFoldedWidthInCm` int(10) UNSIGNED DEFAULT NULL,
			  `sizeUnfoldedHeightInCm` int(10) UNSIGNED DEFAULT NULL,
			  `sizeUnfoldedWidthInCm` int(10) UNSIGNED DEFAULT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `mugs` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `pricePerUnit` float(5,2) NOT NULL DEFAULT '0.00',
			  `priceIncludesVat` enum('N','Y','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `photographFilename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `stockAvailable` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `stockIdealLevel` int(10) UNSIGNED DEFAULT NULL,
			  `stockMinimumLevel` int(10) UNSIGNED DEFAULT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `descriptionLong` text COLLATE utf8_unicode_ci,
			  `visible` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `multimedia` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `pricePerUnit` float(5,2) NOT NULL DEFAULT '0.00',
			  `priceIncludesVat` enum('N','Y','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `photographFilename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `stockAvailable` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `stockIdealLevel` int(10) UNSIGNED DEFAULT NULL,
			  `stockMinimumLevel` int(10) UNSIGNED DEFAULT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `descriptionLong` text COLLATE utf8_unicode_ci,
			  `visible` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `format__JOIN__sprishop___multimediaTypes__reserved` int(11) DEFAULT NULL,
			  `colour` enum('Colour','Black & white','Not applicable','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Colour',
			  `publisher__JOIN__sprishop___publishers__reserved` int(11) NOT NULL,
			  `performer` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `producer` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `productionDate` date DEFAULT NULL,
			  `publicationDate` date DEFAULT NULL,
			  `lengthMinutes` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `technicalDetails` text COLLATE utf8_unicode_ci,
			  `isbn` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `posters` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `pricePerUnit` float(5,2) NOT NULL DEFAULT '0.00',
			  `priceIncludesVat` enum('N','Y','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `photographFilename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `stockAvailable` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `stockIdealLevel` int(10) UNSIGNED DEFAULT NULL,
			  `stockMinimumLevel` int(10) UNSIGNED DEFAULT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `descriptionLong` text COLLATE utf8_unicode_ci,
			  `visible` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `artist` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `type` enum('Poster','Picture','Photograph','Facsimile reproduction','Calendar','Black frame','Black ash frame') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Poster',
			  `colour` enum('Colour','Black & white','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Colour',
			  `sizeWidthInCm` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `sizeHeightInCm` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `publisher` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `dateCreated` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `stamps` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `pricePerUnit` float(5,2) NOT NULL DEFAULT '0.00',
			  `priceIncludesVat` enum('N','Y','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `photographFilename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `stockAvailable` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `stockIdealLevel` int(10) UNSIGNED DEFAULT NULL,
			  `stockMinimumLevel` int(10) UNSIGNED DEFAULT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `descriptionLong` text COLLATE utf8_unicode_ci,
			  `visible` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `stationery` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `pricePerUnit` float(5,2) NOT NULL DEFAULT '0.00',
			  `priceIncludesVat` enum('N','Y','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `photographFilename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `stockAvailable` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `stockIdealLevel` int(10) UNSIGNED DEFAULT NULL,
			  `stockMinimumLevel` int(10) UNSIGNED DEFAULT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `descriptionLong` text COLLATE utf8_unicode_ci,
			  `visible` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `toys` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `pricePerUnit` float(5,2) NOT NULL DEFAULT '0.00',
			  `priceIncludesVat` enum('N','Y','Unknown') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `photographFilename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `stockAvailable` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `stockIdealLevel` int(10) UNSIGNED DEFAULT NULL,
			  `stockMinimumLevel` int(10) UNSIGNED DEFAULT NULL,
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `descriptionLong` text COLLATE utf8_unicode_ci,
			  `visible` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
			  `heightInCm` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `relatedInstitution` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `safety` text COLLATE utf8_unicode_ci,
			  `material` text COLLATE utf8_unicode_ci
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `_authors` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `surname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `forname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `dateOfBirth` date DEFAULT NULL,
			  `dateOfDeath` date DEFAULT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `_booksSubtypes` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT COMMENT 'Automatic key',
			  `subtypeName` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name of book subtype',
			  `subtypeUrlSlug` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'URL key for this subtype',
			  `featured__JOIN__sprishop__books__reserved` int(11) NOT NULL COMMENT 'Featured item',
			  UNIQUE KEY `subtypeUrlSlug` (`subtypeUrlSlug`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Book subtypes';
			
			CREATE TABLE `_cardTypes` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `_clothingTypes` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `photographFilename` varchar(85) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `title` varchar(85) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `description` text COLLATE utf8_unicode_ci NOT NULL,
			  `descriptionLong` text COLLATE utf8_unicode_ci
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `_colours` (
			  `hexCode` varchar(255) COLLATE utf8_unicode_ci PRIMARY KEY NOT NULL,
			  `commonName` varchar(255) COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `_jewelleryTypes` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `_materials` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `_multimediaTypes` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `_publishers` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `_sizes` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			
			CREATE TABLE `__announcement` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT COMMENT 'Unique key',
			  `announcementText` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Accouncement text',
			  `startDate` date NOT NULL,
			  `endDate` date NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Announcement';
			
			CREATE TABLE `__featured` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
			  `section__JOIN__sprishop____sectionData__reserved` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `itemnumber` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `priority` tinyint(3) UNSIGNED NOT NULL DEFAULT '0'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Featured items';
			
			CREATE TABLE `__sectionData` (
			  `id` varchar(85) COLLATE utf8_unicode_ci PRIMARY KEY NOT NULL DEFAULT '',
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Title (on front page)',
			  `singular` varchar(85) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `plural` varchar(85) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `description` text COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Sections';
			
			CREATE TABLE `__themes` (
			  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT COMMENT 'Automatic key',
			  `theme__JOIN__sprishop____themeTypes__reserved` varchar(85) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Theme',
			  `type__JOIN__sprishop____sectionData__reserved` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Section',
			  `number` int(11) NOT NULL COMMENT 'Item number in section table'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Items to be added to thematic groupings';
			
			CREATE TABLE `__themeTypes` (
			  `id` varchar(85) COLLATE utf8_unicode_ci PRIMARY KEY NOT NULL,
			  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
			  `singular` varchar(85) COLLATE utf8_unicode_ci NOT NULL,
			  `plural` varchar(85) COLLATE utf8_unicode_ci NOT NULL,
			  `description` text COLLATE utf8_unicode_ci
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Available themes';
			
			CREATE TABLE `__variationHeadings` (
			  `columnName` varchar(255) COLLATE utf8_unicode_ci PRIMARY KEY NOT NULL,
			  `englishNameOrNull` varchar(255) COLLATE utf8_unicode_ci NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Variation headings';
		;";
		
		if ($this->settings['enableShoppingCart']) {
			$sql .= "
				CREATE TABLE `shoppingcart` (
				  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT COMMENT 'Unique key',
				  `session` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'User session number',
				  `provider` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Shop product provider',
				  `item` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Item number',
				  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Automatic timestamp',
				  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'URL of item',
				  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name of item',
				  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Image of item',
				  `price` float(8,2) NOT NULL COMMENT 'Price each',
				  `total` int(11) NOT NULL COMMENT 'Number of items required',
				  `maximumAvailable` int(11) NOT NULL COMMENT 'Maxmimum number of items of this type available',
				  `orderId` int(11) DEFAULT NULL COMMENT 'Order number'
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Shopping cart session information (do not edit)';
				
				CREATE TABLE `shoppingcartOrders` (
				  `id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT COMMENT 'Order no.',
				  `collectionDetails` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Collection details',
				  `comments` text COLLATE utf8_unicode_ci COMMENT 'Any other comments',
				  `sundries` text COLLATE utf8_unicode_ci COMMENT 'Sundries (if any)',
				  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name',
				  `address` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Address',
				  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'E-mail address',
				  `telephone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Telephone',
				  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				  `status` enum('unfinalised','finalised','shipped','returned','lost','ignore') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Status of order'
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Shopping cart inquiry form';
			;";
		}
  		
		# Return the SQL
		return $sql;
	}
	
	
	# Additional processing
	public function mainPreActions ()
	{
		# Enable tabs if an administrator
		if ($this->userIsAdministrator) {
			$this->settings['disableTabs'] = false;
		}
		
	}
	
	
	# Additional processing
	public function main ()
	{
		# Load required libraries
		require_once ('timedate.php');
		
		# Get the product types and section data
		$this->sections = $this->getSections ();
		$this->productTypes = $this->getProductTypes ();
		
		# Get the type and product id from the query string
		$this->arguments['type'] = ((isSet ($_GET['type']) && isSet ($this->productTypes[$_GET['type']])) ? $_GET['type'] : NULL);
		$this->arguments['page'] = (isSet ($_GET['page']) ? $_GET['page'] : NULL);
		$this->arguments['id'] = (isSet ($_GET['id']) ? $_GET['id'] : NULL);
		$this->arguments['orderby'] = (isSet ($_GET['orderby']) ? $_GET['orderby'] : NULL);
		
		# Get the grouping (if any), e.g. 'youngreaders', etc. under 'books'
		$this->subtypes = $this->getSubtypes ($this->arguments['type']);
		
		# Show the droplist
		if (isSet ($this->actions[$this->action]['droplist']) && $this->actions[$this->action]['droplist']) {
			echo $this->productTypeDroplist ();
		}
		
		# Set the How to order text
		$this->howToOrderText = "\n<p>See <a href=\"{$this->baseUrl}/order/\"><strong>how to order</strong></a> when you have decided what you wish to purchase.</p>";
		
		# Include administrator as feedback recipient
		if ($this->action != 'settings') {	// Except on the settings page itself
			$this->settings['feedbackRecipient'] = array ($this->settings['feedbackRecipient'], $this->settings['administratorEmail']);
		}
		
		# Load the shopping cart library with the specified settings
		if ($this->settings['enableShoppingCart']) {
			$shoppingCartSettings = array (
				'name'					=> $this->settings['applicationName'],
				'provider'				=> __CLASS__,
				'database'				=> $this->settings['database'],		// Shop database
				'administrators'		=> $this->administrators,
				'dateLimitations'		=> true,
				'requireUser'			=> false,
				'confirmationEmail'		=> true,
				'enablePaymentWorkflow'	=> $this->settings['enablePaymentWorkflow'],
				'paymentUrl'			=> $this->settings['shoppingCartPaymentUrl'],
				'sharedSecret'			=> $this->settings['shoppingCartSharedSecret'],
				'clientId'				=> $this->settings['shoppingCartClientId'],
				'orderPrefix'			=> $this->settings['shoppingCartOrderPrefix'],
				'vatCode'				=> $this->settings['shoppingCartVatCode'],
				'orderDescription'		=> $this->settings['shoppingCartOrderDescription'],
			);
			require_once ('shoppingCart.php');
			$this->shoppingCart = new shoppingCart ($this->databaseConnection, $this->baseUrl, $shoppingCartSettings, $userData = array (), $this->userIsAdministrator);
		}
		
	}
	
	
	
	# Function to show a list of product types
	public function home ()
	{
		# Compile the HTML
		$html  = $this->settings['introductionHtml'];
		$html .= $this->howToOrderText;
		$html .= $this->announcementText ();
		$html .= $this->featuredProducts ();
		$html .= $this->productTypeBoxes ();
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to get the section metadata
	private function getSections ()
	{
		# Get the data
		$sectionData = $this->databaseConnection->select ($this->settings['database'], '__sectionData', array (), array (), true, 'title');
		foreach ($sectionData as $section => $attributes) {
			$sectionData[$section]['type'] = 'section';
		}
		
		# Get the themes data
		$themes = $this->getThemes ();
		
		# Merge in the themes data, with the sections taking priority, and re-alphabetising (this assumes that the keys and names have the same alphabetical orders)
		$sectionData += $themes;
		ksort ($sectionData);
		
		# Return the list
		return $sectionData;
	}
	
	
	# Function to get the thematic groupings (with only those that actually have items assigned being picked up)
	private function getThemes ()
	{
		# Get the data
		$query = "SELECT
				theme__JOIN__sprishop____themeTypes__reserved as theme,
				__themeTypes.*,
				'theme' AS type
			FROM sprishop.__themes
			LEFT JOIN __themeTypes ON theme__JOIN__sprishop____themeTypes__reserved = __themeTypes.id
			GROUP BY theme
		;";
		if (!$data = $this->databaseConnection->getData ($query, 'sprishop.__themes')) {
			return array ();
		}
		
		# Return the data
		return $data;
	}
	
	
	# Function to get the product types
	private function getProductTypes ()
	{
		# Arrange the titles as an array
		$types = array ();
		foreach ($this->sections as $type => $attributes) {
			$types[$type] = $attributes['title'];
		}
		
		# Return the list
		return $types;
	}
	
	
	# Function to get the grouping (if any), e.g. 'youngreaders', etc. under 'books'
	private function getSubtypes ($type)
	{
		# End if no type
		if (!$type) {return array ();}
		
		# Look for a table of subtypes for this type
		$table = "_{$type}Subtypes";
		if (!$this->databaseConnection->tableExists ($this->settings['database'], $table)) {
			return array ();
		}
		
		# Get the subtypes for this table
		$query = "SELECT
			{$table}.*,
			photographFilename
			FROM `{$this->settings['database']}`.`{$table}`
			LEFT JOIN {$type} ON featured__JOIN__{$this->settings['database']}__{$type}__reserved = {$type}.id
			ORDER BY subtypeName
		;";
		$data = $this->databaseConnection->getData ($query, "{$this->settings['database']}.{$table}");
		
		# Rekey by groupingUrlSlug; unfortunately the ID is also required so we couldn't use getPairs
		$subtypes = array ();
		foreach ($data as $id => $subtype) {
			$subtypeUrlSlug = $subtype['subtypeUrlSlug'];
			$subtypes[$subtypeUrlSlug] = $subtype;
		}
		
		# Return the groupings
		return $subtypes;
	}
	
	
	# Function to show the ordering page
	public function ordering ()
	{
		# Define the HTML
		$html  = '<h2>How to order</h2>';
		if ($this->userIsAdministrator) {
			$html .= "\n<p class=\"actions alignright\"><a href=\"{$this->baseUrl}/settings.html\"><img src=\"/images/icons/pencil.png\" alt=\"\"> Edit text</a></p>";
		}
		$html .= $this->settings['orderingHtml'];
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to create a product type list
	private function productTypeDroplist ()
	{
		# Create the list
		$values = array ();
		$values["{$this->baseUrl}/"] = 'Introduction';
		$values["{$this->baseUrl}/order/"] = 'Ordering information';
		foreach ($this->productTypes as $type => $description) {
			$location = "{$this->baseUrl}/{$type}/";
			$values[$location] = $description;
		}
		
		# If no type selected, select the first in the list
		$selected = $this->arguments['type'];
		if (!$this->arguments['type']) {
			foreach ($this->productTypes as $type => $description) {
				$selected = $type;
				break;
			}
		}
		
		# Assign the selected item
		$selected = "{$this->baseUrl}/{$selected}/";
		
		# Create the list
		pureContent::jumplistProcessor ();
		$html = pureContent::htmlJumplist ($values, $selected, $this->baseUrl . '/', $name = 'jumplist', $parentTabLevel = 0, $class = 'ultimateform jumplist alignright', 'Go to:');
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to create a product type list
	private function productTypeBoxes ()
	{
		# Create the list
		$items = array ();
		foreach ($this->productTypes as $type => $description) {
			$imageFile = "{$this->settings['imageStoreRoot']}{$this->settings['sectionsImages']}{$type}.jpg";
			$imageHtml = (is_readable ($_SERVER['DOCUMENT_ROOT'] . $imageFile) ? "<img src=\"{$imageFile}\" alt=\"{$description}\">" : '<span class="blank"></span>');
			$items[] = "<a href=\"{$this->baseUrl}/{$type}/\">{$imageHtml} {$description}</a>";
		}
		$html = "\n" . application::htmlUl ($items, 0, 'categories');
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to create a list of featured products
	private function featuredProducts ()
	{
		# Get the featured products list, so that we then know what tables to join to (dynamic table joins are not possible)
		$items = $this->databaseConnection->select ($this->settings['database'], '__featured', array (), array (), false, 'priority');
		
		# Loop through each item and get its data
		$featuredItems = array ();
		foreach ($items as $index => $item) {
			
			# Get the data for this item, adding in the column name
			$query = "SELECT
					id,title,pricePerUnit,photographFilename,
					'{$item['section__JOIN__sprishop____sectionData__reserved']}' AS ___section
				FROM {$this->settings['database']}.{$item['section__JOIN__sprishop____sectionData__reserved']}
				WHERE id = {$item['itemnumber']}
				LIMIT 1;
			;";
			$data = $this->databaseConnection->getOne ($query);
			if (!$data) {continue;}
			
			# Add in the section name
			$featuredItems[$index] = $data;
		}
		
		# End if there are no featured items
		if (!$featuredItems) {return NULL;}
		
		# Create each item as a list item
		foreach ($featuredItems as $item) {
			$list[] = "<a href=\"{$this->baseUrl}/{$item['___section']}/{$item['id']}/\">{$this->productTypes[$item['___section']]}: {$item['title']} - &pound;{$item['pricePerUnit']}</a>";
		}
		
		# Compile the HTML
		$html  = application::htmlUl ($list, 0, 'featureditems');
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to get announcement text
	private function announcementText ()
	{
		# Determine if there is an announcement available
		$query = "SELECT
			announcementText
			FROM {$this->settings['database']}.__announcement
			WHERE
				startDate <= CAST(NOW() AS DATE) AND
				endDate >= CAST(NOW() AS DATE)
			LIMIT 1;";
		if (!$data = $this->databaseConnection->getOne ($query)) {return false;}
		
		# Compile the HTML
		$html  = "\n" . '<p class="warning"><img src="/images/general/alert.gif" width="15" height="15" alt="!" border="0" /> ' . htmlspecialchars ($data['announcementText']) . '</p>';
		
		# Return the HTML
		return $html;
	}
	
	
	
	# Function to show a product listing
	public function listing ()
	{
		# Start the HTML
		$html  = '';
		
		# Validate whether this type exists, and handle the result up the chain
		if (!isSet ($this->sections[$this->arguments['type']])) {
			$html .= "\n<p>There is no such section. Please use the selection box to select an available section.</p>";
			application::sendHeader (404);
			echo $html;
			return false;
		}
		
		# Check for a product grouping
		$grouping = false;
		if ($this->subtypes) {
			
			# If no grouping is requested, show the list available
			if (!isSet ($_GET['grouping'])) {
				$html .= "\n<p>Please choose a category:</p>";
				$html .= $this->subtypesTabs (false, 'sublist', $this->arguments['type']);
				echo $html;
				return;
			}
			
			# If the group is not valid, throw a 404
			if (!isSet ($this->subtypes[$_GET['grouping']])) {
				$this->page404 ();
				return;
			}
			$grouping = $_GET['grouping'];
		}
		
		# Add tabs for this grouping if required
		if ($grouping) {
			$html .= "<br />" . $this->subtypesTabs ($grouping);
		}
		
		# Get the product data
		$dataGrouped = $this->getProductData ($this->arguments['type'], $grouping, $this->arguments['id'], $this->arguments['orderby']);
		
		# If there is no data, say there are no items of this type available at present
		if (!$dataGrouped) {
			$html .= "\n<p><strong>We regret that there are no {$this->sections[$this->arguments['type']]['plural']} available " . ($grouping ? ' in this grouping' : ' in our catalogue') . " at present.</strong></p>";
			$html .= "\n<p>Please use the list above to select other items.</p>";
			echo $html;
			return;
		}
		
		# Show the How to order text
		$html .= $this->howToOrderText;
		
		# Loop through each type within the data (which will only be one group unless using a thematic grouping)
		$isThematicType = ($this->sections[$this->arguments['type']]['type'] == 'theme');
		foreach ($dataGrouped as $type => $data) {
			
			# Clean up the data
			foreach ($data as $item => $attributes) {
				
				# Add the admin link for this item
				if ($this->userIsAdministrator) {
					$data[$item]['admin'] = "\n" . '<p class="actions right"><a href="' . $this->baseUrl . '/data/' . $type . '/' . $attributes['id'] . '/edit.html"><img src="/images/icons/pencil.png" alt="*" /> Edit</a></p>';
				}
				
				# Ensure a dot at the end of the description
				#!# Allow ! or ?
				$attributes['description'] = trim ($attributes['description']);
				if (substr ($attributes['description'], -1) != '.') {$attributes['description'] .= '.';}	// Ensure the description ends with a dot
				$data[$item]['description'] = $attributes['description'];
			}
			
			# Reorganise some of the item components (irrespective of whether they become multiselect)
			#!# These should be moved back towards a fuller model
			foreach ($data as &$item) {
				$item = $this->compileItemComponents ($item, $type, $grouping);
			}
			
			# Add the shopping cart controls
			if ($this->settings['enableShoppingCart']) {
				foreach ($data as &$item) {
					$item['shoppingCartControlsHtml'] = $this->shoppingCart->controls ($item['id'], $this->baseUrl . $item['fragment'], $item['title'], $item['pricePerUnit'], $item['photographPath'], $item['stockAvailableNumeric'], false);
				}
			}
			
			# Perform grouping by title
			$data = $this->groupByTitle ($data);
			
			# Count the items
			$items = count ($data);
			
			# Create the HTML
			if (!$isThematicType) {
				if (!$this->arguments['id']) {
					#!# Need a consistency check that $this->sections[$type] always exists - it could have been forgotten
					$html .= "\n<p>There " . ($items == 1 ? 'is' : 'are') . " {$items} " . $this->sections[$type][($items == 1 ? 'singular' : 'plural')] . ' available' . ($grouping ? ' in this grouping' : '') . ':</p>';
					$html .= "\n<div id=\"parameters\">";
					$html .= "\n</div>";
				} else {
					$html .= application::htmlUl (array ("<a href=\"{$this->baseUrl}/{$type}/\">Back to <strong>{$this->sections[$type]['plural']}</strong></a> or use the list above to go to a different section"));
				}
			}
			
			# Admin link
			if (!$isThematicType) {
				if ($this->userIsAdministrator) {
					$html .= "\n<p class=\"actions alignright\"><a href=\"" . $this->baseUrl . '/data/' . $type . "/add.html\"><img src=\"/images/icons/add.png\" alt=\"\"> Add new item</a></p>";
				}
			}
			
			# End if no data
			if (!$data) {
				$this->page404 ();
				return;
			}
			
			# Create the HTML for each item
			foreach ($data as $item) {
				$html .= $this->itemHtml ($item, $type, ($this->arguments['id']));
			}
			
			# If the type is not found, throw a 404
			if ($item['visible'] == 'N') {
				$this->page404 ();
				return;
			}
		}
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to add grouping tabs
	private function subtypesTabs ($selected = false, $cssClass = 'tabsflat small', $includeImagesType = false)
	{
		# Create the tabs
		$tabs = array ();
		foreach ($this->subtypes as $key => $grouping) {
			$imageHtml = ($includeImagesType ? '<span>' . $this->imageHtml ($grouping['photographFilename'], 'Title', $includeImagesType) . '</span> ' : false);
			$tabs[$key] = "<a href=\"{$this->baseUrl}/{$this->arguments['type']}/{$key}.html\">{$imageHtml}" . htmlspecialchars ($grouping['subtypeName']) . '</a>';
		}
		
		# Compile the HTML
		$html  = application::htmlUl ($tabs, false, $cssClass, true, false, false, false, $selected);
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to group the data by the 'title'
	private function groupByTitle ($data)
	{
		# Loop each item
		$groups = array ();
		foreach ($data as $itemKey => $item) {
			
			# Get the title
			$title = $item['title'];
			
			# Loop through each item attribute
			foreach ($item as $key => $value) {
				
				# Add each attribute to the reordered master array
				$groups[$title][$key][$itemKey] = $value;
			}
		}
		
		# Loop through each group to enable collapsing of items which are the same
		foreach ($groups as $itemName => $attributes) {
			
			# Loop through each attribute
			foreach ($attributes as $attributeKey => $attributeValues) {
				
				# Merge where the same value is used for all items
				if (count ($temporaryAttributeValues = array_unique ($attributeValues)) == 1) {
					sort ($temporaryAttributeValues);	// Reset key name to '0'
					$groups[$itemName][$attributeKey] = $temporaryAttributeValues[0];
				}
			}
		}
		
		# Return the data
		return $groups;
	}
	
	
	# Wrapper function to get the product data for a section (real/thematic)
	private function getProductData ($type, $grouping = false, $id = NULL, $orderby = NULL)
	{
		# Determine if this type is a theme
		$isThematicType = ($this->sections[$this->arguments['type']]['type'] == 'theme');
		if ($isThematicType) {
			
			# Look up the items in this theme
			$items = $this->databaseConnection->select ($this->settings['database'], '__themes', array ('theme__JOIN__sprishop____themeTypes__reserved' => $this->arguments['type']));
			
			# Group by type
			$types = array ();
			foreach ($items as $item) {
				$type = $item['type__JOIN__sprishop____sectionData__reserved'];
				$number = $item['number'];
				$types[$type][] = $number;
			}
			
			# Get each group's items
			$dataGrouped = array ();
			foreach ($types as $type => $items) {
				sort ($items);
				if ($itemsThisSection = $this->getSectionData ($type, $grouping, $items, $orderby)) {
					$dataGrouped[$type] = $itemsThisSection;
				}
			}
			ksort ($dataGrouped);
			
		} else {
			
			# Get the data from standard sections, and make it a single nested array
			$dataGrouped[$type] = $this->getSectionData ($type, $grouping, $id, $orderby);
		}
		
		# return the data
		return $dataGrouped;
	}
	
	
	
	# Get the data for a section
	private function getSectionData ($type, $grouping = false, $id = NULL, $orderby = NULL)
	{
		# Get the fields
		/*
		foreach ($fields as $field) {
			if (strpos ($field, '__JOIN__') !== false) {
				list ($name, $ignoreJoin, $targetDatabase, $targetTable, $ignoreReserved) = explode ('__', $field, 5);
				#!# NB assumes that the target table's key is always called id
				$fieldsSql[] = "{$name}.*";
				$joinSql[] = "LEFT OUTER JOIN {$targetDatabase}.{$targetTable} ON {$type}.{$field} = {$targetTable}.id";
			} else {
				$fieldsSql[] = $field;
			}
		}
		
		# Construct a query (NB $type has already been sanitised)
		$query = "
			SELECT " . '*' . implode (' ', $fieldsSql) . "
			FROM {$this->settings['database']}.{$type}
			" . implode ("\n", $joinSql) . ';';
		*/
		
		# Add in generic order-by types
		$orderbyTypes = array ('title' => 'title', 'price' => 'pricePerUnit', );
		
		# Construct a query for the relevant type
		#!# Ignore 'a ', 'an ', 'the ' in the sorting at this point and ignore '"' at the start
		switch ($type) {
			
			# Books
			case 'books':
				#!# Last {$type}.id id is used to force the ID used to be that from the main table, not any linked table
				$query = "
					SELECT
						{$type}.*,
						_authors.id authorId, _authors.surname authorSurname, _authors.forname authorForename, _authors.dateOfBirth authorDateOfBirth, _authors.dateOfDeath authorDateOfDeath, 
						_publishers.name publisherName, _publishers.url publisherUrl,
						books.id id
					FROM books
					LEFT OUTER JOIN _authors ON books.author__JOIN__sprishop___authors__reserved = _authors.id
					/*LEFT OUTER JOIN _authors ON books.author2__JOIN__sprishop___authors__reserved = _authors.id*/
					/*LEFT OUTER JOIN _authors ON books.author3__JOIN__sprishop___authors__reserved = _authors.id*/
					LEFT OUTER JOIN _publishers ON books.publisher__JOIN__sprishop___publishers__reserved = _publishers.id
					";
				$orderbyTypes += array ('author' => 'author__JOIN__sprishop___authors__reserved', 'date' => 'publicationDate', 'publisher' => 'publisherName');
				break;
			case 'cards':
				$query = "
					SELECT {$type}.*, _cardTypes.name
					FROM {$type}
					LEFT OUTER JOIN sprishop._cardTypes ON {$type}.type__JOIN__sprishop___cardTypes__reserved = _cardTypes.id
					";
				break;
			case 'clothing':
				$query = "
					SELECT
						{$type}.id, {$type}.pricePerUnit, {$type}.priceIncludesVat, {$type}.stockAvailable, {$type}.stockIdealLevel, {$type}.stockMinimumLevel, {$type}.visible,
						_clothingTypes.title AS title, _clothingTypes.photographFilename, _clothingTypes.description, _clothingTypes.descriptionLong,
						_colours.hexCode hexCode, _colours.commonName colour,
						_sizes.name size,
						_materials.name material
					FROM {$type}
					LEFT OUTER JOIN sprishop._clothingTypes ON {$type}.title__JOIN__sprishop___clothingTypes__reserved = _clothingTypes.id
					LEFT OUTER JOIN sprishop._sizes ON {$type}.size__JOIN__sprishop___sizes__reserved = _sizes.id
					LEFT OUTER JOIN sprishop._colours ON {$type}.colour__JOIN__sprishop___colours__reserved = _colours.hexCode
					LEFT OUTER JOIN sprishop._materials ON {$type}.material__JOIN__sprishop___materials__reserved = _materials.id
					";
				break;
			case 'jewellery':
			case 'collectibles':
				$query = "
					SELECT {$type}.*, _jewelleryTypes.name
					FROM {$type}
					LEFT OUTER JOIN sprishop._jewelleryTypes ON {$type}.type__JOIN__sprishop___jewelleryTypes__reserved = _jewelleryTypes.id
					";
				break;
			case 'multimedia':
				$query = "
					SELECT {$type}.*, _multimediaTypes.name multimediaFormat, _publishers.name publisherName, _publishers.url publisherUrl
					FROM {$type}
					LEFT OUTER JOIN _multimediaTypes ON {$type}.format__JOIN__sprishop___multimediaTypes__reserved = _multimediaTypes.id
					LEFT OUTER JOIN _publishers ON {$type}.publisher__JOIN__sprishop___publishers__reserved = _publishers.id
					";
				break;
			case 'maps':
				$query = "
					SELECT {$type}.*, _publishers.name publisherName, _publishers.url publisherUrl
					FROM {$type}
					LEFT OUTER JOIN _publishers ON {$type}.publisher__JOIN__sprishop___publishers__reserved = _publishers.id
					";
				break;
			case 'mugs':
			case 'posters':
			case 'stamps':
			case 'stationery':
			case 'toys':
				$query = "
					SELECT {$type}.*
					FROM {$type}
					";
				break;
		}
		
		# For a particular item, limit to that item
		$limit = '';
		if ($id) {
			if (is_array ($id)) {
				$where[] = "{$type}.id IN(" . implode (',', $id) . ")";
			} else {
				$where[] = "{$type}.id = '{$id}'";
				$limit = ' LIMIT 1';
			}
		}
		
		# Ensure visibility for main listings; this is not done for individual items as they can then be marked as 404s
		if (!$id || is_array ($id)) {
			$where[] = "visible = 'Y'";
		}
		
		# Add grouping limitation
		if ($grouping) {
			$groupingId = $this->subtypes[$grouping]['id'];
			$where[] = "grouping__JOIN__{$this->settings['database']}___{$type}Subtypes__reserved = '{$groupingId}'";
		}
		
		# Compile the where clause(s)
		$query .= 'WHERE ' . implode (' AND ', $where) . ' ';
		
		# Add ordering and pagination for item listings
		if (!$id) {
			# Validate and add ordering - take the column name if the orderby exists, or remove the supplied orderby if it doesn't
			$orderbyColumn = (isSet ($orderbyTypes[$this->arguments['orderby']]) ? $orderbyTypes[$this->arguments['orderby']] : NULL);
			foreach ($orderbyTypes as $orderbyType) {
				$firstOrderbyType = $orderbyType;
				break;
			}
			if ($this->arguments['orderby']) {
				$query .= 'ORDER BY ' . $this->databaseConnection->trimSql ($orderbyColumn);
			} else {
				$query .= 'ORDER BY ' . $this->databaseConnection->trimSql ($firstOrderbyType);
				$this->arguments['orderby'] = $firstOrderbyType;
			}
			
			# Add limit if stated
			# Paginate if pagination data supplied
			if ($limit) {
				$query .= $limit;
			}
		}
		
		# Complete the SQL
		$query .= ';';
		
		# Get the data
		$data = $this->databaseConnection->getData ($query);
		
		# Return the data
		return $data;
	}
	
	
	# Function to return HTML for an item (this is specifically the presentation layer which formats the raw data)
	private function itemHtml ($item, $type, $full = false)
	{
		# Adjust the multiselect components box
		$multiselectComponents = $this->compileMultiselectComponents ($item, $labels /* returned by reference */);
		
		# Determine whether there can be a link to more information
		$moreInfo = (!$multiselectComponents && !$full && $item['descriptionLong']);
		
		/* #!# Links to more info disabled for now */
		$moreInfo = false;
		
		# Assign the link code
		$link['start'] = ($moreInfo ? "<a href=\"{$this->baseUrl}/{$type}/{$item['id']}/\">" : '');
		$link['end'] = ($moreInfo ? '</a>' : '');
		
		# Compile the attributes HTML and supress others
		$attributes = array (
			'name' => 'Type: <strong>%text</strong>',
			'colour' => '%text',
			'size' => 'Size: %text',
			'dateCreated' => 'Created on %text',
			'numberPerPack' => 'Number per pack: %text',
			'message' => 'Message: %text',
			'stockAvailable' => NULL,
			'visible' => NULL,
			'stockIdealLevel' => NULL,
			'stockMinimumLevel' => NULL,
			'type' => NULL,	// Is a __JOIN__ derivative
		);
		$attributesHtml = array ();
		foreach ($attributes as $attribute => $value) {
			# Skip items to be supressed
			if ($value === NULL) {continue;}
			
			# Add the attribute to the list
			if (isSet ($item[$attribute]) && ($item[$attribute]) && !is_array ($item[$attribute])) {
				$attributesHtml[] = str_replace ('%text', $item[$attribute], $value);
			}
		}
		$attributesHtml = (isSet ($attributesHtml) ? application::htmlUl ($attributesHtml) : '');
		
		# Define the item ID (or the first in the group)
		if (is_array ($item['id'])) {
			$itemIdCopy = $item['id'];
			$itemId = array_shift ($itemIdCopy);
		} else {
			$itemId = $item['id'];
		}
		
		# Define the page layout
		$html  = "\n<div class=\"item\" id=\"item{$itemId}\">";
		if ($this->userIsAdministrator && isSet ($item['admin']) && !$multiselectComponents) {$html .= $item['admin'];}
		$html .= "\n\t<h2>" . ($moreInfo ? $link['start'] . $item['title'] . $link['end'] : $item['title']) . '</h2>';
		if (!is_array ($item['photographFilename'])) {$html .= "\n\t" . $item['photographFilename'];}
		$html .= "\n\t<div class=\"info\">";
		if (isSet ($item['_author'])) {$html .= "\n\t\t<h4>By " . $item['_author'] . '</h4>';}
		$html .= "\n\t\t" . application::formatTextBlock (application::makeClickableLinks ((($full && $item['descriptionLong']) ? (is_array ($item['descriptionLong']) ? '<em>Descriptions for each variation of this item below.</em>' : $item['descriptionLong']) : (is_array ($item['description']) ? '<em>Descriptions for each variation of this item below.</em>' : $item['description'])), false, false, $target = false), $paragraphClass = 'description');
		$html .= $attributesHtml;
		if (isSet ($item['publisherCompiled']) && ($item['publisherCompiled'])) {$html .= "\n\t\t<p class=\"publisher\">Published: " . (is_array ($item['publisherCompiled']) ? 'See options below' : "{$item['publisherCompiled']}") . '</p>';}
		$html .= "\n\t\t<p class=\"price\">Price: " . (is_array ($item['pricePerUnit']) ? 'See options below' : "{$item['pricePerUnitFormatted']}") . '</p>';
		$html .= "\n\t\t<p class=\"stock\">Availability: " . (is_array ($item['stockAvailable']) ? 'See options below' : $item['stockAvailable']) . '</p>';
		if ($multiselectComponents) {$html .= "\n<p>Variations available:<br />" . application::htmlTable ($multiselectComponents, $labels, 'lines small compressed', false, true, true) . '</p>';}
		# Add a button to the longer description if there is one and not in single-item mode
		if ($moreInfo) {$html .= "\n\t\t" . "<p class=\"moreinfo\">{$link['start']}More information ..{$link['end']}</p>";}
		$html .= "\n\t</div>";
		if ($this->settings['enableShoppingCart']) {
			if (!$multiselectComponents) {
				$html .= $item['shoppingCartControlsHtml'];
			}
		}
		$html .= "\n</div>";
		
		# Return the HTML
		return $html;
	}
	
	
	# Function to adjust item component presentation
	private function compileItemComponents ($item, $type, $grouping)
	{
		# Add the item path
		$item['path'] = '/' . $type . '/' . $item['id'] . '/';
		$item['fragment'] = '/' . $type . '/' . ($grouping ? "{$grouping}.html" : '') . '#item' . $item['id'];
		
		# Determine whether stock is available
		$item['stockAvailableNumeric'] = $item['stockAvailable'];
		$item['stockAvailable'] = ($item['stockAvailable'] ? 'In stock' : 'We regret this item is temporarily <strong>out of stock</strong>');
		
		# Construct the author's details where relevant
		$authorDetails = '';
		if (isSet ($item['authorForename'])) {
			$item['_author']  = "{$item['authorForename']} {$item['authorSurname']}";
			#!# Add date and time
			# $authorDetails .= timedate::formatDate ($item['authorDateOfBirth']);
			# $authorDetails .= timedate::dateBirthDeath ($item['authorDateOfBirth'], $item['authorDateOfDeath']);
		}
		
		# Combine the VAT indicator into the price
		$item['pricePerUnitFormatted'] = $item['pricePerUnit'];
		if (array_key_exists ('priceIncludesVat', $item)) {
			if ($item['priceIncludesVat'] == 'N') {
				$item['pricePerUnitFormatted'] .= ' &nbsp;<span class="vat">(VAT not chargeable)</span>';
			}
			unset ($item['priceIncludesVat']);
		}
		
		# Combine width and height
		if ((array_key_exists ('sizeWidthInCm', $item)) && (array_key_exists ('sizeHeightInCm', $item))) {
			$item['size'] = $item['sizeWidthInCm'] . 'cm (width) x ' . $item['sizeHeightInCm'] . 'cm (height)';
			unset ($item['sizeWidthInCm'], $item['sizeHeightInCm']);
		}
		
		# Remove 'not applicable' from CD colour
		if (isSet ($item['colour']) && ($item['colour'] == 'Not applicable')) {
			$item['colour'] = '';
		}
		
		# Convert the photograph filename to a photograph, and get the photograph path
		$item['photographFilename'] = $this->imageHtml ($item['photographFilename'], $item['title'], $type, $item['photographPath']);
		
		# Convert publisher and publication date
		$item['publisherCompiled'] = (isSet ($item['publicationDate']) ? ((strpos ($item['publicationDate'], '-') !== false) ? timedate::formatDate ($item['publicationDate']) : $item['publicationDate']) . ' by ' : '') . (isSet ($item['publisherName']) ? (($this->settings['showPublisherLinks'] && $item['publisherUrl']) ? '<a' . (substr_count ($item['publisherUrl'], $_SERVER['SERVER_NAME']) ? '' : ' target="_blank"') . " href=\"{$item['publisherUrl']}\">{$item['publisherName']}</a>" : $item['publisherName']) : '');
		if (isSet ($item['publisher__JOIN__sprishop___publishers__reserved'])) {unset ($item['publisher__JOIN__sprishop___publishers__reserved']);}
		if (isSet ($item['publisherName'])) {unset ($item['publisherName']);}
		if (isSet ($item['publicationDate'])) {unset ($item['publicationDate']);}
		
		# If the key name is a join, then substitute out the name from the join info
		foreach ($item as $key => $values) {
			if (strpos ($key, '__JOIN__') !== false) {
				list ($presented, $discard) = explode ('__JOIN__', $key, 2);
				#!# Unfortunately this gets moved to the end of the array not the original location
				$item[$presented] = $values;
				unset ($item[$key]);
			}
		}
		
		# Supress others
		$attributes = array (/*'stockAvailable', 'visible', */ 'stockIdealLevel', 'stockMinimumLevel', 'type', 'format', );
		foreach ($attributes as $attribute) {
			if (array_key_exists ($attribute, $item)) {
				unset ($item[$attribute]);
			}
		}
		
		# Return the cleaned item
		return $item;
	}
	
	
	# Function to adjust the multiselect box
	private function compileMultiselectComponents ($item, &$labels)
	{
		# Remove temporary variables
		unset ($item['path']);
		unset ($item['fragment']);
		unset ($item['pricePerUnitFormatted']);
		unset ($item['photographPath']);
		unset ($item['stockAvailableNumeric']);
		
		# Obtain multiselect column names
		$multiselectColumnNames = $this->multiselectColumnNames ($item);
		
		# Construct the multiselection box
		foreach ($item as $name => $value) {
			if (array_key_exists ($name, $multiselectColumnNames)) {
				
				# Compile multiselect items into a single array and use the corrected column names
				if ($multiselectColumnNames[$name] != NULL) {
					foreach ($value as $valueKey => $valueItem) {
						$multiselectComponents[$valueKey][$multiselectColumnNames[$name]] = $valueItem;
					}
				}
			}
		}
		
		# Ensure there are some multiselect components
		if (!isSet ($multiselectComponents)) {return false;}
		
		# Colour swatch character
		$colourSwatchCharacter = '&#x25A9;';
		
		# Loop through each
		foreach ($multiselectComponents as $key => $values) {
			
			# Convert hexCode & colour into a single field; NB See Unicode Entity References at http://www.theorem.ca/~mvcorks/cgi-bin/unicode.pl.cgi?start=25A0&end=25FF
			if ((array_key_exists ('colour', $multiselectComponents[$key])) && (array_key_exists ('hexCode', $multiselectComponents[$key]))) {
				$multiselectComponents[$key]['colour'] = "<span style=\"color: #{$values['hexCode']}\">{$colourSwatchCharacter}</span> {$values['colour']}";
				unset ($multiselectComponents[$key]['hexCode']);
			}
			
			# Supress the ID
			if (isSet ($multiselectComponents[$key]['id'])) {
				unset ($multiselectComponents[$key]['id']);
			}
			
			# Convert prices and VAT
			#!# Need to use the pre-converted names, as the converted names are not hardcoded ...
			if (array_key_exists ('Price each', $multiselectComponents[$key])) {
				# Make bold
				$multiselectComponents[$key]['Price each'] = "<strong>{$multiselectComponents[$key]['Price each']}</strong>";
				
				# Move the price to the end
				$temporary = $multiselectComponents[$key]['Price each'];
				unset ($multiselectComponents[$key]['Price each']);
				$multiselectComponents[$key]['Price each'] = $temporary;
				
				# Move the shoppingCartControlsHtml to the end
				if ($this->settings['enableShoppingCart']) {
					$temporary = $multiselectComponents[$key]['shoppingCartControlsHtml'];
					unset ($multiselectComponents[$key]['shoppingCartControlsHtml']);
					$multiselectComponents[$key]['shoppingCartControlsHtml'] = $temporary;
				}
			}
		}
		
		# Set labels
		$labels = array ();
		if ($this->settings['enableShoppingCart']) {
			$labels['shoppingCartControlsHtml'] = '';
		}
		
		# Return
		return $multiselectComponents;
	}
	
	
	# Function to create HTML for the image
	private function imageHtml ($photographFilename, $title, $type, &$imagePath = false)
	{
		# Load required library
		require_once ('image.php');
		
		# Define the default image HTML
		$imageHtml = "<span class=\"noimage\">Sorry, no image available at present.</span>";
		
		# Check if there is a photograph
		if ($photographFilename) {
			
			#!# Remove this switching code when all data migrated
			if ($type == 'clothing') {$type = '_clothingTypes';}
			$imagePath = $this->settings['imageStoreRoot'] . '/' . $type . '/' . $photographFilename;
			if (is_readable ($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
				
				# Determine the base size to scale to
				$baseSize = $this->settings['imageResizeTo'];
				
				# Get its original size
				list ($width, $height, $imageType, $imageAttributes) = getimagesize ($_SERVER['DOCUMENT_ROOT'] . $imagePath);
				
				# Obtain the image height and width when scaled
				list ($width, $height) = image::scaledImageDimensions ($width, $height, $baseSize);
				
				# Create the HTML
				$thumbnailImagePath = ($this->settings['imageGenerationStub'] ? "{$this->settings['imageGenerationStub']}?{$width}," : '') . $imagePath;
				$imageHtml = '<img class="mainimage" src="' . $thumbnailImagePath . "\" alt=\"{$title}\" width=\"{$width}\" height=\"{$height}\" />";
			}
		}
		
		# Return the HTML
		return $imageHtml;
	}
	
	
	# Function to convert column headings
	private function multiselectColumnNames ($item)
	{
		# Obtain keys for any attribute values in the item which are multiselect components (i.e. there is a type choice)
		$names = array ();
		foreach ($item as $name => $value) {
			if (is_array ($value)) {
				$names[$name] = $name;
			}
		}
		
		# Obtain the column name substitutions
		$sectionData = $this->databaseConnection->select ($this->settings['database'], '__variationHeadings');
		
		# Loop through to perform substitutions if section headings are supplied
		foreach ($names as $underlying => $visible) {
			if (isSet ($sectionData[$visible])) {
				$names[$underlying] = $sectionData[$visible]['englishNameOrNull'];
				
				# Convert the string 'NULL' to the special NULL type
				if ($names[$underlying] == 'NULL') {
					$names[$underlying] = NULL;
				}
			}
		}
		
		# Return the headings
		return $names;
	}
	
	
	# Admin editing section, substantially delegated to the sinenomine editing component
	public function editing ($attributes = array (), $deny = false, $sinenomineExtraSettings = array ())
	{
		# Define sinenomine settings
		$sinenomineExtraSettings = array (
			'int1ToCheckbox' => true,
			'datePicker' => true,
			'richtextEditorToolbarSet' => 'BasicLonger',
			'richtextWidth' => 600,
			'richtextHeight' => 200,
			'hideTableIntroduction' => false,
			'tableCommentsInSelectionListOnly' => true,
		);
		
		# Define table attributes
		$attributesByTable = $this->formDataBindingAttributes ();
		$attributes = array ();
		foreach ($attributesByTable as $table => $attributesForTable) {
			foreach ($attributesForTable as $field => $fieldAttributes) {
				$attributes[] = array ($this->settings['database'], $table, $field, $fieldAttributes);
			}
		}
		
		# Define tables to deny editing for
		$deny[$this->settings['database']] = array (
			'administrators',
			'settings',
			'shoppingcart',
			'shoppingcartOrders',
		);
		
		# Hand off to the default editor, which will echo the HTML
		parent::editing ($attributes, $deny, $sinenomineExtraSettings);
	}
	
	
	# Helper function to define the dataBinding attributes
	private function formDataBindingAttributes ()
	{
		# Define the properties, by table
		$dataBindingAttributes = array (
			'*' => array (
				'photographFilename' => array ('directory' => $_SERVER['DOCUMENT_ROOT'] . $this->settings['imageStoreRoot'] . "/%table/", 'forcedFileName' => '%id', 'lowercaseExtension' => true, 'allowedExtensions' => array ('jpg')),
				'colour' => array ('type' => 'select', ),
			),
		);
		
		# Return the properties
		return $dataBindingAttributes;
	}
	
	
	# Settings
	public function settings ($dataBindingSettingsOverrides = array ())
	{
		# Define overrides
		$dataBindingSettingsOverrides = array (
			'attributes' => array (
				'introductionHtml' => array ('editorToolbarSet' => 'BasicLonger', 'width' => '650', 'height' => 150),
				'orderingHtml' => array ('editorToolbarSet' => 'BasicLonger'),
			),
		);
		
		# Run the main settings system with the overriden attributes
		return parent::settings ($dataBindingSettingsOverrides);
	}
	
	
	# Function to provide the shop basket
	public function basket ()
	{
		# Hand off to the shopping cart system
		$html = $this->shoppingCart->basket ();
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to provide the shop checkout
	public function checkout ()
	{
		# Hand off to the shopping cart system
		$excludeFields = array ('collectionDetails', 'comments', 'stockAvailable', );
		list ($result, $html) = $this->shoppingCart->checkout ($excludeFields);
		
		# End if no result
		if (!$result) {
			echo $html;
			return false;
		}
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to provide the e-payments callback
	public function callback ()
	{
		# Hand off to the shopping cart system
		$html = $this->shoppingCart->callback ();
		
		# Show the HTML
		echo $html;
	}
	
	
	# Function to list the orders
	public function orders ($id = false)
	{
		# Determine if in final confirmation mode
		$confirmationMode = (isSet ($_GET['mode']) && ($_GET['mode'] == 'confirmation'));
		
		# Hand off to the shopping cart system
		list ($result, $html, $isUpdatedFinalised) = $this->shoppingCart->orders ($id, $confirmationMode);
		
		# End if no result
		if (!$result) {
			echo $html;
			return false;
		}
		
		# Send and show a confirmation e-mail
		$html = $this->confirmationEmail ($result, true, $isUpdatedFinalised);
		
		# Show the HTML
		echo $html;
	}
}

?>
