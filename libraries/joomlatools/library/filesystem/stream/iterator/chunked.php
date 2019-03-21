<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright   Copyright (C) 2007 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/joomlatools/joomlatools-framework for the canonical source repository
 */

/**
 * Chunked FileSystem Stream Iterator
 *
 * Pulls out chunks from an inner stream iterator and yields the chunks as arrays.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filesystem\Stream\Iterator
 */
class KFilesystemStreamIteratorChunked implements OuterIterator, Countable
{
    /**
     * The inner stream iterator
     *
     * @var int
     */
    private $__iterator;

    /**
     * Constructor.
     *
     * @param KFilesystemStreamIterator $iterator  A FilesystemStream object
     */
    public function __construct(KFilesystemStreamIterator $iterator)
    {
        $this->__inneriterator = $iterator;
    }

    /**
     * Seeks to a given chunk position in the stream
     *
     * @param int $position
     * @throws OutOfBoundsException If the position is not seekable.
     * @return void
     */
    public function seek($position)
    {
        if ($position > $this->count()) {
            throw new OutOfBoundsException('Invalid seek position ('.$position.')');
        }

        $chunk_size = $this->getChunkSize();
        $position   = $chunk_size * $position;

        $this->getInnerIterator()->seek($position);
    }

    /**
     * Read data from the stream and advance the pointer
     *
     * @return string
     */
    public function current()
    {
        return $this->getInnerIterator()->current();
    }

    /**
     * Returns the current position of the stream read/write pointer
     *
     * @return bool|int|mixed
     */
    public function key()
    {
        return $this->getInnerIterator()->key() / $this->getChunkSize();
    }

    /**
     * Move to the next chunk
     *
     * @return void
     */
    public function next()
    {
        $this->getInnerIterator()->next();
    }

    /**
     * Rewind to the beginning of the stream
     *
     * @return void
     */
    public function rewind()
    {
        $this->getInnerIterator()->rewind();
    }

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        return $this->getInnerIterator()->valid();
    }

    /**
     *  Count total amount of chunks
     *
     * @return int
     */
    public function count()
    {
        return $this->getInnerIterator()->getStream()->getSize() / $this->getChunkSize();
    }

    /**
     * Get the chunk size
     *
     * @return integer
     */
    public function getChunkSize()
    {
        return $this->getInnerIterator()->getChunkSize();
    }

    /**
     * Get the stream object
     *
     * @return KFilesystemStreamIterator
     */
    public function getInnerIterator()
    {
        return $this->__inneriterator;
    }
}