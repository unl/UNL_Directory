<?php

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

class UNL_Peoplefinder_Record_QRCode implements UNL_Peoplefinder_DirectOutput, UNL_Peoplefinder_Routable
{
    protected $options;

    protected $uid;
    protected $format;

    protected $record;

    private $valid_formats = array('png', 'svg');

    public static $icon_path_png;
    public static $icon_path_svg;
    public static $icon_size;

    public static $cache_dir = __DIR__ . '/../../../../data/qr/cache/';
    public static $cache_prefix;

    public function __construct($options = [])
    {
        if (!isset($options['uid']) || empty($options['uid'])) {
            throw new Exception('Bad Input', 400);
        }

        $this->uid = rtrim($options['uid'], '/');
        $this->format = 'png';

        $split_uid = explode('.', strtolower($this->uid));
        if (in_array(end($split_uid), $this->valid_formats)) {
            $this->format = end($split_uid);
            $temp_split_uid = array_slice($split_uid, 0, -1);
            $this->uid = implode('.', $temp_split_uid);
            $this->uid = rtrim($this->uid, '/');
        }

        if (empty($this->uid)) {
            throw new Exception('Bad Input', 400);
        }

        if (filter_var($this->uid, FILTER_VALIDATE_EMAIL)) {
            // Check if they have a record
            try {
                $this->record = new UNL_Peoplefinder_Record(array('email' => $this->uid));
            } catch (Exception $e) {
                // If not a 404 it will throw it
                if ($e->getCode() !== 404) {
                    throw $e;
                }
            }
        } else {
            // Check if they have a record
            try {
                $this->record = new UNL_Peoplefinder_Record(array('uid' => $this->uid));
            } catch (Exception $e) {
                // If not a 404 it will throw it
                if ($e->getCode() !== 404) {
                    throw $e;
                }
            }
        }

        if (!$this->record instanceof UNL_Peoplefinder_Record) {
            throw new Exception('Could Not Find That Person', 404);
        }
    }

    public function getUrl($options = [])
    {
        return UNL_Peoplefinder::$url . 'qrcode/' . $this->record->uid;
    }

    public function send()
    {
        $qrCodeHash = hash("sha512", $this->record->uid);
        $qrCache = realpath(self::$cache_dir) . '/' . (self::$cache_prefix ?? "") . $qrCodeHash . '.' . $this->format;

        // Remove any old cached files
        if (isset(self::$cache_prefix) && !empty(self::$cache_prefix)) {
            $file_pattern = realpath(self::$cache_dir) . '/*' . $qrCodeHash . '.' . $this->format;
            $files = glob($file_pattern, GLOB_BRACE);
            foreach ($files as $file) {
                // Check the current cachePrefix is not in there before deleting
                if (strpos($file, self::$cache_prefix) === false) {
                    unlink($file);
                }
            }
        }

        // get users vcard data
        $savvy = new UNL_Peoplefinder_Savvy();
        $content = $savvy->render($this->record, 'templates/vcard/Peoplefinder/Record.tpl.php');

        if (!file_exists($qrCache)) {
            if ($this->format === 'png') {
                $this->createAndSavePNG($content, $qrCache);
            } else if ($this->format === 'svg') {
                $this->createAndSaveSVG($content, $qrCache);
            }
        }

        if ($this->format === 'png') {
            $this->sendPNG($qrCache);
        } else if ($this->format === 'svg') {
            $this->sendSVG($qrCache);
        }
    }

    private function sendPNG(string $file_path)
    {
        $out = imagecreatefrompng($file_path);
        header('Content-Type: image/png');
        imagepng($out);
        imagedestroy($out);
        exit;
    }

    private function sendSVG(string $file_path)
    {
        header('Content-Type: image/svg+xml');
        include_once($file_path);
        exit;
    }

    private function createAndSavePNG(string $data, string $file_path)
    {
        $writer = new PngWriter();

        $qrCode = QrCode::create($data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(1080)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        if (!empty(self::$icon_path_png) && !empty(self::$icon_size) && file_exists(self::$icon_path_png)) {
            // Create generic logo
            $qrLogo = Logo::create(self::$icon_path_png)
            ->setResizeToWidth(self::$icon_size)
            ->setResizeToHeight(self::$icon_size);

            $writer->write($qrCode, $qrLogo)->saveToFile($file_path);
        } else {
            $writer->write($qrCode)->saveToFile($file_path);
        }
    }

    private function createAndSaveSVG(string $data, string $file_path)
    {
        $writer = new SVGWriter();

        $qrCode = QrCode::create($data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(1080)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        if (!empty(self::$icon_path_svg) && !empty(self::$icon_size) && file_exists(self::$icon_path_svg)) {
            // Create generic logo
            $qrLogo = Logo::create(self::$icon_path_svg)
                ->setResizeToWidth(self::$icon_size)
                ->setResizeToHeight(self::$icon_size);

            // Get the string version of the SVG QR code
            $result = $writer->write($qrCode, $qrLogo)->getString();

            // Get the SVG icon and prep it for preg_replace with back reference
            $svg_file = file_get_contents(self::$icon_path_svg);
            $svg_file = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $svg_file);
            $svg_file = str_replace('<svg', '<svg x="$1" y="$2" width="$3" height="$4"', $svg_file);

            // Replace image with SVG icon, use back reference to get the variables we need
            $result = preg_replace(
                '/<image x="([\d]+)" y="([\d]+)" width="([\d]+)" height="([\d]+)".*\/>/',
                $svg_file,
                $result
            );

            // Write the files to the cache
            file_put_contents($file_path, $result);
        } else {
            $writer->write($qrCode)->saveToFile($file_path);
        }
    }
}
