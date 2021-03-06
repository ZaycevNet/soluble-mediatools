<?php

declare(strict_types=1);

namespace Soluble\MediaTools\Video\Filter;

use Soluble\MediaTools\Video\Filter\Type\FFMpegVideoFilterInterface;

class CropFilter implements FFMpegVideoFilterInterface
{
    /** @var int|string|null */
    protected $height;

    /** @var int|string|null */
    protected $width;

    /** @var int|string|null */
    protected $x;

    /** @var int|string|null */
    protected $y;

    /** @var bool */
    protected $keepAspect;

    /** @var bool */
    protected $exact;

    /**
     * Crop filter.
     *
     * @see https://ffmpeg.org/ffmpeg-filters.html#crop
     *
     * @param string|int|null $width      The width of the output video, it defaults to 'iw'
     * @param string|int|null $height     The height of the output video, it defaults to 'ih'
     * @param string|int|null $x          The horizontal position, in the input video, of the left edge of the output video. It defaults to '(in_w-out_w)/2'
     * @param string|int|null $y          The vertical position, in the input video, of the top edge of the output video. It defaults to '(in_h-out_h)/2'
     * @param bool            $keepAspect will force the output display aspect ratio to be the same of the input, by changing the output sample aspect ratio
     * @param bool            $exact      Enable exact cropping. If enabled, subsampled videos will be cropped at exact width/height/x/y as specified and will not be rounded to nearest smaller value.
     */
    public function __construct(
        $width = null,
        $height = null,
        $x = null,
        $y = null,
        bool $keepAspect = false,
        bool $exact = false
    ) {
        $this->width      = $width;
        $this->height     = $height;
        $this->x          = $x;
        $this->y          = $y;
        $this->keepAspect = $keepAspect;
        $this->exact      = $exact;
    }

    public function getFFmpegCLIValue(): string
    {
        $args = array_filter([
            ($this->width !== null) ? "w={$this->width}" : false,
            ($this->height !== null) ? "h={$this->height}" : false,
            ($this->x !== null) ? "x={$this->x}" : false,
            ($this->y !== null) ? "y={$this->y}" : false,
            ($this->keepAspect) ? 'keep_aspect=1' : false,
            ($this->exact) ? 'exact=1' : false,
        ]);

        return sprintf(
            'crop=%s',
            implode(':', $args)
        );
    }
}
