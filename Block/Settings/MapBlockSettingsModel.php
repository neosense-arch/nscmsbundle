<?php

namespace NS\CmsBundle\Block\Settings;

/**
 * Menu block settings model
 *
 */
class MapBlockSettingsModel
{
    /**
     * @var float
     */
    private $lat = 55.4899270;

    /**
     * @var float
     */
    private $lng = 37.3193290;

    /**
     * @var float
     */
    private $markerLat;

    /**
     * @var float
     */
    private $markerLng;

    /**
     * @var string
     */
    private $markerTitle;

    /**
     * @var int
     */
    private $width = 600;

    /**
     * @var int
     */
    private $height = 400;

    /**
     * @var int
     */
    private $zoom = 16;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param float $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param float $lng
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    }

    /**
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $zoom
     */
    public function setZoom($zoom)
    {
        $this->zoom = $zoom;
    }

    /**
     * @return int
     */
    public function getZoom()
    {
        return $this->zoom;
    }

    /**
     * @param float $markerLat
     */
    public function setMarkerLat($markerLat)
    {
        $this->markerLat = $markerLat;
    }

    /**
     * @return float
     */
    public function getMarkerLat()
    {
        return $this->markerLat;
    }

    /**
     * @param float $markerLng
     */
    public function setMarkerLng($markerLng)
    {
        $this->markerLng = $markerLng;
    }

    /**
     * @return float
     */
    public function getMarkerLng()
    {
        return $this->markerLng;
    }

    /**
     * @param string $markerTitle
     */
    public function setMarkerTitle($markerTitle)
    {
        $this->markerTitle = $markerTitle;
    }

    /**
     * @return string
     */
    public function getMarkerTitle()
    {
        return $this->markerTitle;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
}
