<?php

namespace RyanNielson\Meta;

class Meta
{
    /**
     * @var bool
     */
    protected $html5;

    /**
     * The current stored meta attributes to be rendered at a later stage.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * @param bool $html5 Whether or not to self-close generated meta tags
     */
    public function __construct($html5 = true)
    {
        $this->html5 = $html5;
    }

    /**
     * Sets the meta attributes.
     *
     * @param array $attributes
     * @return array
     */
    public function set(array $attributes = array())
    {
        $this->attributes = array_replace_recursive($this->attributes, $attributes);

        return $this->attributes;
    }

    /**
     * Display the meta tags with the set attributes.
     *
     * @param array $defaults The default meta attributes
     * @return string The meta tags
     */
    public function display(array $defaults = array())
    {
        $metaAttributes = array_replace_recursive($defaults, $this->attributes);
        $results = array();

        // Handle other custom properties.
        foreach ($metaAttributes as $name => $content) {
            $content = $this->removeFromArray($metaAttributes, $name);

            if ($name === "keywords") {
                $keywords = $this->prepareKeywords($content);
                $results[] = $this->metaTag("keywords", $keywords);
            } elseif (is_array($content) && $this->isAssociativeArray($content)) {
                $results = array_merge($results, $this->processNestedAttributes($name, $content));
            } else {
                foreach ((array) $content as $con) {
                    $results[] = $this->metaTag($name, $con);
                }
            }
        }

        return implode("\n", $results);
    }

    /**
     * Clears the meta attributes array.
     *
     * @return array
     */
    public function clear()
    {
        $this->attributes = array();
        return $this->attributes;
    }

    /**
     * Returns the current meta attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Prepares keywords and converts the array to a comma separated string if required.
     *
     * @param array|string $keywords
     * @return string Comma separated keywords.
     */
    protected function prepareKeywords($keywords)
    {
        if ($keywords === null) {
            return null;
        }

        if (is_array($keywords)) {
            $keywords = implode(", ", $keywords);
        }

        return strtolower(strip_tags($keywords));
    }

    /**
     * Process nested attributes recursively.
     *
     * @param string $property
     * @param array $content
     * @return array An array of meta tags for the nested attributes
     */
    protected function processNestedAttributes($property, array $content)
    {
        $results = array();

        if ($this->isAssociativeArray($content)) {
            foreach ($content as $key => $value) {
                $results = array_merge($results, $this->processNestedAttributes("{$property}:{$key}", $value));
            }
        } else {
            foreach ($content as $con) {
                if ($this->isAssociativeArray($con)) {
                    $results = array_merge($results, $this->processNestedAttributes($property, $con));
                } else {
                    $results[] = $this->metaTag($property, $con);
                }
            }
        }

        return $results;
    }

    /**
     * Determines if an array is associative.
     *
     * @param array $value
     * @return boolean
     */
    protected function isAssociativeArray(array $value)
    {
        return (bool) count(array_filter(array_keys($value), "is_string"));
    }

    /**
     * Returns a meta tag with the given name and content.
     *
     * @param string $name The name of the meta tag
     * @param string $content The meta tag content
     * @return string The constructed meta tag
     */
    protected function metaTag($name, $content)
    {
        $tag = "<meta name=\"$name\" content=\"$content\"";
        if ($this->html5 === false) {
            $tag .= "/";
        }
        return $tag . ">";
    }

    /**
     * Removes an item from the array and returns its value.
     *
     * @param array $array The input array
     * @param string $key The key pointing to the desired value
     * @return mixed The value mapped to $key or null if none
     */
    protected function removeFromArray(&$array, $key)
    {
        if (array_key_exists($key, $array)) {
            $val = $array[$key];
            unset($array[$key]);
            return $val;
        }

        return null;
    }
}

