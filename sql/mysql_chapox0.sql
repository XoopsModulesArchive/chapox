CREATE TABLE chapox0_contents (
	lid		int(11) unsigned NOT NULL auto_increment,
	layer_num	int(2) unsigned NOT NULL default 0,
	layer1		int(8) unsigned NOT NULL default 0,	/* part       */
	layer2		int(8) unsigned NOT NULL default 0,	/* chapter    */
	layer3		int(8) unsigned NOT NULL default 0,	/* section    */
	layer4		int(8) unsigned NOT NULL default 0,	/* subsection */
	mystatus	varchar(255) NOT NULL default '',
	title		varchar(255) NOT NULL default '',
	mycontent	text NOT NULL default '',
	excerpt		text NOT NULL default '',
	footnote	text NOT NULL default '',
	uid		int(8) unsigned NOT NULL default 0,
	uname		varchar(64) NOT NULL default '',
	host		varchar(255) NOT NULL default '',
	posted		int(10) unsigned NOT NULL default 0,	/* outline created */
	updated		int(10) unsigned NOT NULL default 0,	/* content updated */
	PRIMARY KEY (lid)
) TYPE=MyISAM;
