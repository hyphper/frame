<?php
declare(strict_types=1);
namespace Hyphper\Frame;

use Hyphper\Frame\Exception\InvalidFrameException;

/**
 * The GOAWAY frame informs the remote peer to stop creating streams on this
 * connection. It can be sent from the client or the server. Once sent, the
 * sender will ignore frames sent on new streams for the remainder of the
 * connection.
 *
 * @package Hyphper\Frame
 */
class GoAwayFrame extends \Hyphper\Frame
{
    protected $defined_flags = [];
    protected $type = 0x07;
    protected $stream_association = self::NO_STREAM;
    protected $last_stream_id;
    protected $error_code;
    protected $additional_data;

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->last_stream_id = (int) ($options['last_stream_id'] ?? 0);
        $this->error_code = (int) ($options['error_code'] ?? 0);
        $this->additional_data = (int) ($options['additional_data'] ?? '');
    }

    public function serializeBody(): string
    {
        $data = pack(
            'NN',
            $this->last_stream_id & 0x7FFFFFFF,
            $this->error_code
        );

        $data .= $this->additional_data;

        return $data;
    }

    /**
     * Given the body of a frame, parses it into frame data. This populates
     * the non-header parts of the frame: that is, it does not populate the
     * stream ID or flags.
     *
     *
     * @param string $data
     *
     * @return void
     */
    public function parseBody(string $data)
    {
        if (!$unpack = @unpack('Nlast_stream_id/Nerror_code', substr($data, 0, 8))) {
            throw new InvalidFrameException('Invalid GOAWAY body.');
        }

        $this->last_stream_id = $unpack['last_stream_id'];
        $this->error_code = $unpack['error_code'];

        $this->body_len = strlen($data);
        if (strlen($data) > 8) {
            $this->additional_data = substr($data, 8);
        }
    }

    /**
     * @param int $last_stream_id
     *
     * @return GoAwayFrame
     */
    public function setLastStreamId($last_stream_id)
    {
        $this->last_stream_id = $last_stream_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getLastStreamId()
    {
        return $this->last_stream_id;
    }

    /**
     * @param int $error_code
     *
     * @return GoAwayFrame
     */
    public function setErrorCode($error_code)
    {
        $this->error_code = $error_code;

        return $this;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->error_code;
    }

    /**
     * @param int $additional_data
     *
     * @return GoAwayFrame
     */
    public function setAdditionalData($additional_data)
    {
        $this->additional_data = $additional_data;

        return $this;
    }

    /**
     * @return int
     */
    public function getAdditionalData()
    {
        return $this->additional_data;
    }
}
