<?php

declare(strict_types=1);

namespace MediaToolsTest;

use Psr\Container\ContainerInterface;
use Soluble\MediaTools\Config\ConfigProvider;
use Soluble\MediaTools\Video\ConverterServiceInterface;
use Soluble\MediaTools\Video\DetectionServiceInterface;
use Soluble\MediaTools\Video\ProbeServiceInterface;
use Zend\ServiceManager\ServiceManager;

trait TestUtilTrait
{
    /**
     * @throws \Exception
     */
    public function getVideoConvertService(): ConverterServiceInterface
    {
        return $this->getConfiguredContainer()->get(ConverterServiceInterface::class);
    }

    /**
     * @throws \Exception
     */
    public function getVideoProbeService(): ProbeServiceInterface
    {
        return $this->getConfiguredContainer()->get(ProbeServiceInterface::class);
    }

    /**
     * @throws \Exception
     */
    public function getVideoDetectionService(): DetectionServiceInterface
    {
        return $this->getConfiguredContainer()->get(DetectionServiceInterface::class);
    }

    /**
     * @throws \Exception
     */
    public function getConfiguredContainer(): ContainerInterface
    {
        if (!defined('FFMPEG_BINARY_PATH')) {
            throw new \Exception('Missing phpunit constant FFMPEG_BINARY_PATH');
        }
        if (!defined('FFPROBE_BINARY_PATH')) {
            throw new \Exception('Missing phpunit constant FFPROBE_BINARY_PATH');
        }

        $ffmpegBinary = FFMPEG_BINARY_PATH;
        if (mb_strpos(FFMPEG_BINARY_PATH, './') !== false) {
            // relative directory
            $ffmpegBinary = realpath(FFMPEG_BINARY_PATH);
            if ($ffmpegBinary === false || !file_exists($ffmpegBinary)) {
                throw new \Exception(sprintf(
                    'FFMPEG binary does not exists: \'%s\', realpath returned: \'%s\'',
                    FFMPEG_BINARY_PATH,
                    is_bool($ffmpegBinary) ? 'false' : $ffmpegBinary
                ));
            }
        }

        $ffprobeBinary = FFPROBE_BINARY_PATH;
        if (mb_strpos(FFPROBE_BINARY_PATH, './') !== false) {
            // relative directory
            $ffprobeBinary = realpath(FFPROBE_BINARY_PATH);
            if ($ffprobeBinary === false || !file_exists($ffprobeBinary)) {
                throw new \Exception(sprintf(
                    'FFPROBE binary does not exists: \'%s\', realpath returned: \'%s\'',
                    FFPROBE_BINARY_PATH,
                    is_bool($ffprobeBinary) ? 'false' : $ffprobeBinary
                ));
            }
        }

        $config = [
            'soluble-mediatools' => [
                'ffmpeg.binary'  => $ffmpegBinary,
                'ffprobe.binary' => $ffprobeBinary,
            ],
        ];

        $sm = new ServiceManager(
            array_merge(
                [
                    'services' => [
                        'config' => $config,
                    ]],
                (new ConfigProvider())->getDependencies()
            )
        );

        return $sm;
    }
}
