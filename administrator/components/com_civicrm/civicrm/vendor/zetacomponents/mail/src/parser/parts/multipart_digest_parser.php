<?php
/**
 * File containing the ezcMailMultipartDigestParser class
 *
 * @package Mail
 * @version //autogen//
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Parses multipart/digest mail parts.
 *
 * @package Mail
 * @version //autogen//
 * @access private
 */
class ezcMailMultipartDigestParser extends ezcMailMultipartParser
{
    /**
     * Holds the ezcMailMultipartDigest part corresponding to the data parsed with this parser.
     *
     * @var ezcMailMultipartDigest
     */
    private $part = null;

    /**
     * Constructs a new ezcMailMultipartDigestParser.
     *
     * @param ezcMailHeadersHolder $headers
     */
    public function __construct( ezcMailHeadersHolder $headers )
    {
        parent::__construct( $headers );
        $this->part = new ezcMailMultipartDigest();
    }

    /**
     * Adds the part $part to the list of multipart messages.
     *
     * This method is called automatically by ezcMailMultipartParser
     * each time a part is parsed.
     *
     * @param ezcMailPart $part
     */
    public function partDone( ezcMailPart $part )
    {
        $this->part->appendPart( $part );
    }

    /**
     * Returns the parts parsed for this multipart.
     *
     * @return ezcMailMultipartDigest
     */
    public function finishMultipart()
    {
        $size = 0;
        foreach ( $this->part->getParts() as $part )
        {
            $size += $part->size;
        }
        $this->part->size = $size;
        return $this->part;
    }
}

?>
