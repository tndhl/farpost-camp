<?php
namespace Library;

class Image
{
    private $imageType;
    private $imageResource = NULL;

    function __construct($filename)
    {
        $this->load($filename);
    }

    /**
     * Возвращает ресурс текущего изобращения
     *
     * @return resource|NULL
     */
    public function getResource()
    {
        return $this->imageResource;
    }

    /**
     * Загрузка изобращения
     *
     * @param string $filename Путь к изобращению
     */
    public function load($filename)
    {
        $image_info = @getimagesize($filename);
        $this->imageType = $image_info[2];

        switch ($this->imageType) {
            case IMAGETYPE_JPEG:
                $this->imageResource = \imagecreatefromjpeg($filename);
                break;

            case IMAGETYPE_GIF:
                $this->imageResource = \imagecreatefromgif($filename);
                break;

            case IMAGETYPE_PNG:
                $this->imageResource = \imagecreatefrompng($filename);
                break;
        }
    }

    /**
     * Вывод изображения в браузер
     *
     * @param string $imageType Тип изображения
     */
    public function printOut($imageType = "jpg")
    {
        switch ($imageType) {
            case "jpg":
                header("Content-Type: image/jpeg");
                \imageinterlace($this->imageResource, 1);
                \imagejpeg($this->imageResource);
                break;

            case "png":
                header("Content-Type: image/png");
                \imagepng($this->imageResource);
                break;

            case "gif":
                header("Content-Type: image/gif");
                \imagegif($this->imageResource);
                break;
        }
    }

    /**
     * Сохранить текущие изображение
     *
     * @param string $filename Путь сохраняемого файла
     */
    public function save($filename)
    {
        $parts = explode(".", $filename);
        $imageType = strtolower(end($parts));

        switch ($imageType) {
            case "jpg":
                imageinterlace($this->imageResource, 1);
                imagejpeg($this->imageResource, $filename);
                break;

            case "png":
                imagepng($this->imageResource, $filename);
                break;

            case "gif":
                imagegif($this->imageResource, $filename);
                break;
        }
    }

    /**
     * Возвращает ширину текущего изображения
     *
     * @return int
     */
    public function getWidth()
    {
        return imagesx($this->imageResource);
    }

    /**
     * Возвращает высоту текущего изображения
     *
     * @return int
     */
    public function getHeight()
    {
        return imagesy($this->imageResource);
    }

    /**
     * Пропорциональное изменение размеров текущего изображения
     *
     * @param float $scale Масштаб
     */
    public function scale($scale)
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getHeight() * $scale / 100;

        $this->resize($width, $height);
    }

    /**
     * Пропорциональное изменение размеров текущего изображения по ширине
     *
     * @param int $width Ширина (px)
     */
    public function scaleToWidth($width)
    {
        $scale = $width / $this->getWidth();
        $height = $this->getHeight() * $scale;

        $this->resize($width, $height);
    }

    /**
     * Пропорциональное изменение размеров текущего изображения по высоте
     *
     * @param int $height Высота (px)
     */
    public function scaleToHeight($height)
    {
        $scale = $height / $this->getHeight();
        $width = $this->getWidth() * $scale;

        $this->resize($width, $height);
    }

    /**
     * Изменение размеров текущего изображения
     *
     * @param int $width  Ширина (px)
     * @param int $height Высота (px)
     */
    public function resize($width, $height)
    {
        $resizedImageResource = imagecreatetruecolor($width, $height);

        imagecopyresampled(
            $resizedImageResource,
            $this->imageResource,
            0, 0,
            0, 0,
            $width,
            $height,
            $this->getWidth(),
            $this->getHeight()
        );

        $this->imageResource = $resizedImageResource;
    }

    /**
     * Наложить изображение поверх текущего изображения
     *
     * @param Image $image   Изображение
     * @param int   $marginX Отступ слева
     * @param int   $marginY Отступ сверху
     * @param int   $opacity Непрозрачность
     */
    public function mergeWith(Image $image, $marginX = 0, $marginY = 0, $opacity = 100)
    {
        imagecopymerge(
            $this->imageResource,
            $image->getResource(),
            $marginX, $marginY,
            0, 0,
            $image->getWidth(),
            $image->getHeight(),
            $opacity
        );
    }
}