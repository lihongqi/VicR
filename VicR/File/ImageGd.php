<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 2017/11/24
 * Time: 下午2:50
 */

namespace File;


class ImageGd
{
    /**
     * 创建
     * @param string $src 图片路径
     * @return array
     */
    protected static function createByPath($src)
    {
        $img_info = self::getImageSize($src);
        switch ($img_info['ext']) {
            case 1:
            case 'image/gif':
                $res = imagecreatefromgif($src);
                break;
            case 2:
            case 'image/pjpeg':
            case 'image/jpeg':
                $res = imagecreatefromjpeg($src);
                break;
            case 3:
            case 'image/x-png':
            case 'image/png':
                $res = imagecreatefrompng($src);
                break;
            default:
                throw new \Exception('create fail img ' . $src, 5001);
        }
        return [$res, $img_info];
    }

    /**
     * 得到图片尺寸
     * @param $src
     * @return array
     */
    public static function getImageSize($src)
    {
        $arr = getimagesize($src);
        return [
            'width' => $arr[0],
            'height' => $arr[1],
            'ext' => $arr[2]
        ];
    }

    /**
     * @param $w
     * @param $h
     * @return resource
     */
    protected static function create($w, $h)
    {
        return imagecreatetruecolor($w, $h);
    }


    /**
     * 合并图片
     * @param resource $dst 合并资源
     * @param resource $src 被合并的资源
     */
    protected  static function merge($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h)
    {
        imagecopy($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
    }

    /**
     * 保存图片
     * @param resource $dst
     * @param string $path
     * @param string $ext
     * @return bool
     */
    protected static function save($dst, $path, $ext)
    {
        switch ($ext) {
            case 1:
                imagegif($dst, $path);
                break;
            case 2:
                imagejpeg($dst, $path, 85);
                break;
            case 3:
                imagepng($dst, $path, 9);
                break;
            default:
                imagejpeg($dst, $path, 85);
                break;
        }
        return true;
    }

    /**
     * 缩放图片
     * @param string $dst_path
     * @param string $src_path
     * @param string $dst_width
     * @param string $dst_height
     * @param bool $focus 图片尺寸小于目标尺寸会放大
     * @param bool $is_in 在规定的矩形框内 false 标示充满矩形框
     * @return bool
     */
    public static function zoom($dst_path, $src_path, $dst_width, $dst_height, $focus = true, $is_in = true)
    {
        list($src, $src_info) = self::createByPath($src_path);
        $size_info = self::getZoomSize($dst_width, $dst_height, $src_info['width'], $src_info['height'], $focus, $is_in);
        $dst = self::create($size_info[0], $size_info[1]);
        if ($src_info == false) {
            return false;
        }
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $size_info[0], $size_info[1], $src_info['width'], $src_info['height']);
        self::save($dst, $dst_path, $src_info['ext']);
        return true;
    }

    /**
     * 计算缩放尺寸
     * @param $new_width
     * @param $new_height
     * @param $old_width
     * @param $old_height
     * @param bool $focus true：图片尺寸小于目标尺寸会放大
     * @param bool $is_in true：缩放后的图片在指定的区域内，false：最小边在指定区域内
     * @return array | bool
     */
    private static function getZoomSize($new_width, $new_height, $old_width, $old_height, $focus = true, $is_in = true)
    {
        if (
            ($new_height == $old_height && $new_width == $old_width && $focus == true) ||
            ($new_width >= $old_width && $new_height >= $old_height && $focus == false && $is_in == true) ||
            (($new_width >= $old_width || $new_height >= $old_height) && $focus == false && $is_in == false)
        ) {
            return false;
        }

        if ($new_width / $new_height > $old_width / $old_height) {
            if ($is_in) {
                $height = $new_height;
                $width = intval($height * $old_width / $old_height);
            } else {
                $width = $new_width;
                $height = intval($width * $old_height / $old_width);
            }
        } else {
            if ($is_in) {
                $width = $new_width;
                $height = intval($width * $old_height / $old_width);

            } else {
                $height = $new_height;
                $width = intval($height * $old_width / $old_height);
            }
        }
        return [$width, $height];
    }
}