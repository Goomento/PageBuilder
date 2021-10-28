<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder;

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
        $parsed_rules = '';

        foreach ($rules as $selector => $properties) {
            $selector_content = self::parseProperties($properties);

            if ($selector_content) {
                $parsed_rules .= $selector . '{' . $selector_content . '}';
            }
        }

        return $parsed_rules;
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
        $parsed_properties = '';

        foreach ($properties as $property_key => $property_value) {
            if ('' !== $property_value) {
                $parsed_properties .= $property_key . ':' . $property_value . ';';
            }
        }

        return $parsed_properties;
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
     * @param null $style_rules Optional. Style rules. Default is `null`.
     * @param array|null $query Optional. Media query. Default is `null`.
     *
     * @return Stylesheet The current stylesheet class instance.
     */
    public function addRules($selector, $style_rules = null, array $query = null)
    {
        $query_hash = 'all';

        if ($query) {
            $query_hash = $this->queryToHash($query);
        }

        if (!isset($this->rules[ $query_hash ])) {
            $this->addQueryHash($query_hash);
        }

        if (null === $style_rules) {
            preg_match_all('/([^\s].+?(?=\{))\{((?s:.)+?(?=}))}/', $selector, $parsed_rules);

            foreach ($parsed_rules[1] as $index => $selector) {
                $this->addRules($selector, $parsed_rules[2][ $index ], $query);
            }

            return $this;
        }

        if (!isset($this->rules[ $query_hash ][ $selector ])) {
            $this->rules[ $query_hash ][ $selector ] = [];
        }

        if (is_string($style_rules)) {
            $style_rules = array_filter(explode(';', trim($style_rules)));

            $ordered_rules = [];

            foreach ($style_rules as $rule) {
                $property = explode(':', $rule, 2);

                if (count($property) < 2) {
                    return $this;
                }

                $ordered_rules[ trim($property[0]) ] = trim($property[1], ' ;');
            }

            $style_rules = $ordered_rules;
        }

        $this->rules[ $query_hash ][ $selector ] = array_merge($this->rules[ $query_hash ][ $selector ], $style_rules);

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

        foreach ($this->rules as $query_hash => $rule) {
            $device_text = self::parseRules($rule);

            if ('all' !== $query_hash) {
                $device_text = $this->getQueryHashStyleFormat($query_hash) . '{' . $device_text . '}';
            }

            $styleText .= $device_text;
        }

        foreach ($this->raw as $device_name => $raw) {
            $raw = implode("\n", $raw);

            if ($raw && isset($this->devices[ $device_name ])) {
                $raw = '@media(max-width: ' . $this->devices[ $device_name ] . 'px){' . $raw . '}';
            }

            $styleText .= $raw;
        }

        return $styleText;
    }

    /**
     * Get device maximum value.
     *
     * Retrieve the maximum size of any given device.
     *
     * @throws \RangeException If max value for this device is out of range.
     *
     * @param string $device_name Device name.
     *
     * @return int
     */
    private function getDeviceMaxValue($device_name)
    {
        $devices_names = array_keys($this->devices);

        $device_name_index = array_search($device_name, $devices_names);

        $next_index = $device_name_index + 1;

        if ($next_index >= count($devices_names)) {
            throw new \RangeException('Max value for this device is out of range.');
        }

        return $this->devices[ $devices_names[ $next_index ] ] - 1;
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

        foreach ($hash as $single_query) {
            $query_parts = explode('_', $single_query);

            $end_point = $query_parts[0];

            $device_name = $query_parts[1];

            $query[ $end_point ] = 'max' === $end_point ? $this->getDeviceMaxValue($device_name) : $this->devices[ $device_name ];
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
     * @param string $query_hash Hashed string of the query.
     */
    private function addQueryHash($query_hash)
    {
        $this->rules[ $query_hash ] = [];

        uksort(
            $this->rules,
            function ($a, $b) {
                if ('all' === $a) {
                    return -1;
                }

                if ('all' === $b) {
                    return 1;
                }

                $a_query = $this->hashToQuery($a);

                $b_query = $this->hashToQuery($b);

                if (isset($a_query['min']) xor isset($b_query['min'])) {
                    return 1;
                }

                if (isset($a_query['min'])) {
                    $range = $a_query['min'] - $b_query['min'];

                    if ($range) {
                        return $range;
                    }

                    $a_has_max = isset($a_query['max']);

                    if ($a_has_max xor isset($b_query['max'])) {
                        return $a_has_max ? 1 : -1;
                    }

                    if (!$a_has_max) {
                        return 0;
                    }
                }

                return $b_query['max'] - $a_query['max'];
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
     * @param string $query_hash The hash of the query.
     *
     * @return string CSS media query.
     */
    private function getQueryHashStyleFormat($query_hash)
    {
        $query = $this->hashToQuery($query_hash);

        $style_format = [];

        foreach ($query as $end_point => $value) {
            $style_format[] = '(' . $end_point . '-width:' . $value . 'px)';
        }

        return '@media' . implode(' and ', $style_format);
    }
}
