<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hoandoanviet
 * Date: 11/14/15
 * Time: 9:45 AM
 * To change this template use File | Settings | File Templates.
 */

namespace App\Service;

use App\Utils\Lib;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Images
{
    protected $mediaUrl;
    const NO_IMAGE = '/no-image.jpg';
    const IMAGE_SERVICE = 'ImageUtils';

    /*
     * ThanhDT add for common image lib
     * */
    public function __construct(string $mediaUrl)
    {
        $this->mediaUrl = $mediaUrl;
    }

    /**
     * Get image by thumb size
     * author: ThanhDT
     * date:   2018-05-18 09:24 AM
     * @param $url
     * @param $imageSize
     * @return string
     */
    public function getImageSize($url, $imageSize)
    {
        if (empty($url)) {
            $url = self::NO_IMAGE;
        }else if (strpos('http', $url) === 0) {
            return $url;
        }
        $thumbUrl = sprintf('%s/crop/%s%s', $this->mediaUrl, $imageSize, $url);

        return $thumbUrl;
    }

    /**
     * Get full size
     * author: ThanhDT
     * date:   2018-05-18 09:27 AM
     * @param $url
     * @return mixed|string
     */
    public function getFullImage($url)
    {
        if (empty($url)) {
            return $this->mediaUrl . self::NO_IMAGE;
        }
        $url = $this->mediaUrl . $url;
        return $url;
    }

}
