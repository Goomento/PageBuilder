<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Modules;

use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Magento\Framework\Code\Minifier\Adapter\Css\CSSmin;

class Stylesheet
{
    /**
     * CSS Rules.
     *
     * Holds the list of CSS rules.
     *
     * @var array A list of CSS rules.
     */
    private $rules = [];

    /**
     * Devices.
     *
     * Holds the list of devices.
     *
     * @var array A list of devices.
     */
    private $devices = [];

    /**
     * Raw CSS.
     *
     * Holds the raw CSS.
     *
     * @var array The raw CSS.
     */
    private $raw = [];

    /**
     * CSS3 variables
     *
     * @var array
     */
    private $variables = [];

    /**
     * @var CSSmin|null
     */
    private $minifier;

    /**
     * Parse CSS rules.
     *
     * Goes over the list of CSS rules and generates the final CSS.
     *
     *
     * @param array $rules CSS rules.
     *
     * @return string Parsed rules.
     */
    public static function parseRules(array $rules)
    {
        $parsedRules = '';

        foreach ($rules as $selector => $properties) {
            $selectorContent = self::parseProperties($properties);

            if ($selectorContent) {
                $parsedRules .= $selector . '{' . $selectorContent . '}';
            }
        }

        return $parsedRules;
    }

    /**
     * Parse CSS properties.
     *
     * Goes over the selector properties and generates the CSS of the selector.
     *
     *
     * @param array $properties CSS properties.
     *
     * @return string Parsed properties.
     */
    public static function parseProperties(array $properties)
    {
        $parsedProperties = '';

        foreach ($properties as $propertyKey => $propertyValue) {
            if ('' !== $propertyValue) {
                $parsedProperties .= $propertyKey . ':' . $propertyValue . ';';
            }
        }

        return $parsedProperties;
    }

    /**
     * Add device.
     *
     * Add a new device to the devices list.
     *
     *
     * @param string $deviceName      Device name.
     * @param string $deviceMaxPoint Device maximum point.
     *
     * @return Stylesheet The current stylesheet class instance.
     */
    public function addDevice($deviceName, $deviceMaxPoint)
    {
        $this->devices[ $deviceName ] = $deviceMaxPoint;

        asort($this->devices);

        return $this;
    }

    /**
     * Add or update CSS variable
     *
     * @param string $name
     * @param string $value
     * @param string $root
     * @return $this
     */
    public function addVariable(string $name, string $value, string $root = ':root')
    {
        if (!isset($this->variables[$root])) {
            $this->variables[$root] = [];
        }

        $this->variables[$root][$name] = $value;

        return $this;
    }



    /**
     * Add rules.
     *
     * Add a new CSS rule to the rules list.
     *
     *
     * @param string $selector CSS selector.
     * @param null $styleRules Optional. Style rules. Default is `null`.
     * @param array|null $query Optional. Media query. Default is `null`.
     *
     * @return Stylesheet The current stylesheet class instance.
     */
    public function addRules($selector, $styleRules = null, array $query = null)
    {
        $queryHash = 'all';

        if ($query) {
            $queryHash = $this->queryToHash($query);
        }

        if (!isset($this->rules[ $queryHash ])) {
            $this->addQueryHash($queryHash);
        }

        if (null === $styleRules) {
            preg_match_all('/([^\s].+?(?=\{))\{((?s:.)+?(?=}))}/', $selector, $parsedRules);

            foreach ($parsedRules[1] as $index => $selector) {
                $this->addRules($selector, $parsedRules[2][ $index ], $query);
            }

            return $this;
        }

        if (!isset($this->rules[ $queryHash ][ $selector ])) {
            $this->rules[ $queryHash ][ $selector ] = [];
        }

        if (is_string($styleRules)) {
            $styleRules = array_filter(explode(';', trim($styleRules)));

            $orderedRules = [];

            foreach ($styleRules as $rule) {
                $property = explode(':', $rule, 2);

                if (count($property) < 2) {
                    return $this;
                }

                $orderedRules[ trim($property[0]) ] = trim($property[1], ' ;');
            }

            $styleRules = $orderedRules;
        }

        $this->rules[ $queryHash ][ $selector ] = array_merge($this->rules[ $queryHash ][ $selector ], $styleRules);

        return $this;
    }

    /**
     * Add raw CSS.
     *
     * Add a raw CSS rule.
     *
     *
     * @param string $css    The raw CSS.
     * @param string $device Optional. The device. Default is empty.
     *
     * @return Stylesheet The current stylesheet class instance.
     */
    public function addRawCss(string $css, string $device = '')
    {
        if (!isset($this->raw[ $device ])) {
            $this->raw[ $device ] = [];
        }

        $this->raw[ $device ][] = trim($css);

        return $this;
    }

    /**
     * Get CSS rules.
     *
     * Retrieve the CSS rules.
     *
     *
     * @param string $device   Optional. The device. Default is empty.
     * @param string $selector Optional. CSS selector. Default is empty.
     * @param string $property Optional. CSS property. Default is empty.
     *
     * @return null|array CSS rules, or `null` if not rules found.
     */
    public function getRules($device = null, $selector = null, $property = null)
    {
        if (!$device) {
            return $this->rules;
        }

        if ($property) {
            return $this->rules[$device][$selector][$property] ?? null;
        }

        if ($selector) {
            return $this->rules[$device][$selector] ?? null;
        }

        return $this->rules[$device] ?? null;
    }

    /**
     * To string.
     *
     * This magic method responsible for parsing the rules into one CSS string.
     *
     *
     * @return string CSS style.
     */
    public function __toString()
    {
        $styleText = '';

        foreach ($this->variables as $root => $rootData) {
            $styleText .= $root . '{';
            foreach ($rootData as $varName => $varValue) {
                $styleText .= $varName . ':' . $varValue . ';';
            }
            $styleText .= '}';
        }

        foreach ($this->rules as $queryHash => $rule) {
            $deviceText = self::parseRules($rule);

            if ('all' !== $queryHash) {
                $deviceText = $this->getQueryHashStyleFormat($queryHash) . '{' . $deviceText . '}';
            }

            $styleText .= $deviceText;
        }

        foreach ($this->raw as $deviceName => $raw) {
            $raw = implode("\n", $raw);

            if ($raw && isset($this->devices[ $deviceName ])) {
                $raw = '@media(max-width: ' . $this->devices[ $deviceName ] . 'px){' . $raw . '}';
            }

            $styleText .= $raw;
        }

        return $this->minifyContent($styleText);
    }


    /**
     * Minify CSS
     *
     * @param string $content
     * @return string
     */
    public function minifyContent(string $content) : string
    {
        $content = trim($content);
        if ($content !== '') {
            if ($this->minifier === null) {
                $this->minifier = ObjectManagerHelper::get(CSSmin::class);
            }

            $content = $this->minifier->minify($content);
        }

        return $content;
    }

    /**
     * Get device maximum value.
     *
     * Retrieve the maximum size of any given device.
     *
     * @throws \RangeException If max value for this device is out of range.
     *
     * @param string $deviceName Device name.
     *
     * @return int
     */
    private function getDeviceMaxValue($deviceName)
    {
        $devicesNames = array_keys($this->devices);

        $deviceNameIndex = array_search($deviceName, $devicesNames);

        $nextIndex = $deviceNameIndex + 1;

        if ($nextIndex >= count($devicesNames)) {
            throw new \RangeException('Max value for this device is out of range.');
        }

        return $this->devices[ $devicesNames[ $nextIndex ] ] - 1;
    }

    /**
     * Query to hash.
     *
     * Turns the media query into a hashed string that represents the query
     * endpoint in the rules list.
     *
     *
     * @param array $query CSS media query.
     *
     * @return string Hashed string of the query.
     */
    private function queryToHash(array $query)
    {
        $hash = [];

        foreach ($query as $endpoint => $value) {
            $hash[] = $endpoint . '_' . $value;
        }

        return implode('-', $hash);
    }

    /**
     * Hash to query.
     *
     * Turns the hashed string to an array that contains the data of the query
     * endpoint.
     *
     * @param string $hash Hashed string of the query.
     *
     * @return array Media query data.
     */
    private function hashToQuery($hash)
    {
        $query = [];

        $hash = array_filter(explode('-', $hash));

        foreach ($hash as $singleQuery) {
            $queryParts = explode('_', $singleQuery);

            $endPoint = $queryParts[0];

            $deviceName = $queryParts[1];

            $query[ $endPoint ] = 'max' === $endPoint ? $this->getDeviceMaxValue($deviceName) : $this->devices[ $deviceName ];
        }

        return $query;
    }

    /**
     * Add query hash.
     *
     * Register new endpoint query and sort the rules the way they should be
     * displayed in the final stylesheet based on the device and the viewport
     * width.
     *
     *
     * @param string $queryHash Hashed string of the query.
     */
    private function addQueryHash($queryHash)
    {
        $this->rules[ $queryHash ] = [];

        uksort(
            $this->rules,
            function ($a, $b) {
                if ('all' === $a) {
                    return -1;
                }

                if ('all' === $b) {
                    return 1;
                }

                $aQuery = $this->hashToQuery($a);

                $bQuery = $this->hashToQuery($b);

                if (isset($aQuery['min']) xor isset($bQuery['min'])) {
                    return 1;
                }

                if (isset($aQuery['min'])) {
                    $range = $aQuery['min'] - $bQuery['min'];

                    if ($range) {
                        return $range;
                    }

                    $aHasMax = isset($aQuery['max']);

                    if ($aHasMax xor isset($bQuery['max'])) {
                        return $aHasMax ? 1 : -1;
                    }

                    if (!$aHasMax) {
                        return 0;
                    }
                }

                return $bQuery['max'] - $aQuery['max'];
            }
        );
    }

    /**
     * Get query hash style format.
     *
     * Retrieve formated media query rule with the endpoint width settings.
     *
     * The method returns the CSS `@media` rule and supported viewport width in
     * pixels. It can also handel multiple width endpoints.
     *
     *
     * @param string $queryHash The hash of the query.
     *
     * @return string CSS media query.
     */
    private function getQueryHashStyleFormat($queryHash)
    {
        $query = $this->hashToQuery($queryHash);

        $styleFormat = [];

        foreach ($query as $endPoint => $value) {
            $styleFormat[] = '(' . $endPoint . '-width:' . $value . 'px)';
        }

        return '@media' . implode(' and ', $styleFormat);
    }
}
