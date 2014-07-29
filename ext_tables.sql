#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_multicolumn_parentid int(11) DEFAULT '0' NOT NULL,

	KEY tx_multicolumn (sectionIndex,colPos,pid,tx_multicolumn_parentid,sorting),
	KEY tx_multicolumn_parentid (tx_multicolumn_parentid,deleted,hidden)
);
