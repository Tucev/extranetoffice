<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_email
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * emailHelperIMAP_Sort Class
 * 
 * This class is used to sort IMAP mailboxes
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_email
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class emailHelperIMAP_Sort {
 	/**
     * The delimiter character to use.
     *
     * @var string
     */
    var $_delimiter;
    /**
     * Should we sort with 'INBOX' at the front of the list?
     *
     * @var boolean
     */
    var $_sortinbox;

    /**
     * Constructor.
     *
     * @param string $delimiter  The delimiter used to separate mailboxes.
     */
    function emailHelperIMAP_Sort($delimiter) {
        $this->_delimiter = $delimiter;
    }

    /**
     * Sort a list of mailboxes (by value).
     *
     * @param array &$mbox    The list of mailboxes to sort.
     * @param boolean $inbox  When sorting, always put 'INBOX' at the head of
     *                        the list?
     * @param boolean $index  Maintain index association?
     */
    function sortMailboxes(&$mbox, $inbox = true, $index = false) {
        $this->_sortinbox = $inbox;
        if ($index) {
            uasort($mbox, array(&$this, '_mbox_cmp'));
        } else {
            usort($mbox, array(&$this, '_mbox_cmp'));
        }
    }

    /**
     * Sort a list of mailboxes (by key).
     *
     * @param array &$mbox    The list of mailboxes to sort, with the keys
     *                        being the mailbox names.
     * @param boolean $inbox  When sorting, always put 'INBOX' at the head of
     *                        the list?
     */
    function sortMailboxesByKey(&$mbox, $inbox = true) {
        $this->_sortinbox = $inbox;
        uksort($mbox, array(&$this, '_mbox_cmp'));
    }

    /**
     * Hierarchical folder sorting function (used with usort()).
     *
     * @access private
     *
     * @param string $a  Comparison item 1.
     * @param string $b  Comparison item 2.
     *
     * @return integer  See usort().
     */
    function _mbox_cmp($a, $b) {
    	$a = $a['nameX']; // Added to work with intranet office
    	$b = $b['nameX']; // Added to work with intranet office
        /* Always return INBOX as "smaller". */
        if ($this->_sortinbox) {
            if (strcasecmp($a, 'INBOX') == 0) {
                return -1;
            } 
            elseif (strcasecmp($b, 'INBOX') == 0) {
                return 1;
            }
        }
        
        // Hack to place Trash folde right after Inbox
    	if (strcasecmp($a, 'Trash') == 0) {
    		return -1;
    	} 
    	elseif (strcasecmp($b, 'Trash') == 0) {
        	return 1;
    	}

        $a_parts = explode($this->_delimiter, $a);
        $b_parts = explode($this->_delimiter, $b);

        $a_count = count($a_parts);
        $b_count = count($b_parts);

        $iMax = min($a_count, $b_count);

        for ($i = 0; $i < $iMax; $i++) {
            if ($a_parts[$i] != $b_parts[$i]) {
                /* If only one of the folders is under INBOX, return it as
                 * "smaller". */
                if ($this->_sortinbox && ($i == 0)) {
                    $a_base = (strcasecmp($a_parts[0], 'INBOX') == 0);
                    $b_base = (strcasecmp($b_parts[0], 'INBOX') == 0);
                    if ($a_base && !$b_base) {
                        return -1;
                    } 
                    elseif (!$a_base && $b_base) {
                        return 1;
                    }
                }
                $cmp = strnatcasecmp($a_parts[$i], $b_parts[$i]);
                if ($cmp == 0) {
                    return strcmp($a_parts[$i], $b_parts[$i]);
                }
                return $cmp;
            }
        }

        return ($a_count - $b_count);
    }
}
?>