<?php
declare(strict_types=1);
namespace Hyphper\Frame;

trait PaddingTrait
{
    protected $padding_length;

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->padding_length = (int) ($options['padding_length'] ?? 0);
    }

    /**
     * @param int $padding_length
     */
    public function setPaddingLength(int $padding_length)
    {
        $this->padding_length = $padding_length;
    }

    /**
     * @return int
     */
    public function getPaddingLength()
    {
        return $this->padding_length;
    }

    /**
     * @return string
     */
    protected function serializePaddingData(): string
    {
        if ($this->flags->hasFlag(Flag::PADDED)) {
            return pack('C', $this->padding_length);
        }

        return '';
    }

    /**
     * @param string $data
     *
     * @return int
     * @throws Exception\InvalidFrameException
     */
    protected function parsePaddingData(string $data): int
    {
        if ($this->flags->hasFlag(Flag::PADDED)) {
            if (!$unpack = @unpack('Cpadding_length', substr($data, 0, 1))) {
                throw new \Hyphper\Frame\Exception\InvalidFrameException("Invalid Padding Data");
            }

            $this->padding_length = $unpack['padding_length'];

            return static::IS_PADDED;
        }

        return static::IS_NOT_PADDED;
    }
}
